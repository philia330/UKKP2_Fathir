<?php

/*
|--------------------------------------------------------------------------
| ALUR UMUM FILE INI (BACA DULU)
|--------------------------------------------------------------------------
| Ini adalah CONTROLLER untuk AUTENTIKASI = proses "membuktikan siapa kamu".
| Fitur di file ini:
|   - tampil form login      -> showLoginForm()
|   - proses login           -> login()
|   - tampil form register   -> showRegisterForm()
|   - proses register        -> register()
|   - proses logout          -> logout()
|   - arahkan sesuai role    -> redirectBasedOnRole() (method bantu/helper)
|
| ALUR LOGIN:
|   Isi email + password -> validasi -> cek ke database (Auth::attempt)
|        |                                   |
|        |                          berhasil | gagal
|        v                                   v
|   buat session baru            kembali ke form login + pesan error
|        |
|        v
|   cek role user -> admin / petugas / customer -> masuk dashboard masing-masing
|
| ISTILAH PENTING:
|   - Autentikasi = cek "kamu siapa?" (login).
|   - Session     = "catatan sementara" di server yang mengingat kamu sudah login.
|   - CSRF token  = kode rahasia di setiap form, mencegah form dipalsukan
|                   oleh situs lain.
*/

// NAMESPACE = "alamat folder" class ini (app/Http/Controllers/).
// Karena langsung di folder Controllers (bukan di Admin/Petugas/Customer),
// controller ini dipakai bersama oleh semua role.
namespace App\Http\Controllers;

// USE = "memanggil" class lain supaya bisa dipakai di file ini.

// MODEL User = mewakili tabel "users" di database.
use App\Models\User;

// Request = class bawaan Laravel yang membawa semua data kiriman dari
// browser (isi form, session, dll).
use Illuminate\Http\Request;

// Auth = alat bawaan Laravel untuk urusan login/logout dan mengetahui
// siapa user yang sedang login.
use Illuminate\Support\Facades\Auth;

// Hash = alat bawaan Laravel untuk MENGACAK password (enkripsi satu arah).
use Illuminate\Support\Facades\Hash;

/**
 * CLASS = "cetakan/blueprint" yang berisi kumpulan method (fungsi).
 *
 * "extends Controller" artinya AuthController MEWARISI (inheritance)
 * semua kemampuan dari class Controller induknya.
 * (Controller induk sudah ada di folder yang sama, jadi tidak perlu "use".)
 */
class AuthController extends Controller
{
    /**
     * METHOD showLoginForm() -> TAMPILKAN FORM LOGIN.
     *
     * Menampilkan halaman login.
     *
     * Ini adalah halaman pertama yang dilihat user ketika belum login.
     *
     * Method = fungsi yang berada DI DALAM class.
     * "public" artinya boleh dipanggil dari luar class (oleh Route).
     */
    public function showLoginForm()
    {
        // view() = fungsi bawaan Laravel untuk menampilkan halaman.
        // 'auth.login' = file resources/views/auth/login.blade.php
        // (tanda titik dibaca sebagai tanda folder "/").
        return view('auth.login');
    }

    /**
     * METHOD login() -> PROSES LOGIN.
     *
     * Memproses login user.
     *
     * Validasi email dan password, kemudian mengarahkan
     * user ke halaman dashboard sesuai role mereka.
     *
     * PARAMETER (data masukan method):
     *   Request $request = objek yang membawa isi form login
     *   (email dan password) dari browser.
     */
    public function login(Request $request)
    {
        // Validasi data login
        //
        // $credentials = VARIABLE (wadah data). Tanda $ menandakan variable.
        // "credentials" artinya kredensial = data bukti identitas (email + password).
        //
        // $request->validate([...]) = MEMERIKSA isi form.
        // Jika ada yang salah, Laravel otomatis kembali ke form + pesan error.
        // Jika benar, data bersih disimpan ke $credentials.
        $credentials = $request->validate([
            // email: wajib diisi dan formatnya harus email.
            'email' => 'required|email',
            // password: cukup wajib diisi. Tidak ada min/max karena di sini
            // kita hanya MENCOCOKKAN password, bukan membuat password baru.
            'password' => 'required',
        ]);

        // Coba login dengan kredensial yang diberikan
        //
        // IF = percabangan (kondisi). Isinya hanya dijalankan kalau kondisinya benar.
        //
        // Auth::attempt($credentials) bekerja seperti ini:
        //   1. Cari user di tabel users yang email-nya cocok.
        //   2. Hash password yang diketik, lalu bandingkan dengan password
        //      yang tersimpan di database.
        //   3. Jika cocok -> user dianggap login (hasilnya true).
        //      Jika tidak -> hasilnya false.
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk keamanan
            //
            // Membuat ID session BARU setelah login berhasil.
            // Tujuannya mencegah serangan "session fixation" (penyerang
            // memakai ID session lama untuk menyamar sebagai kita).
            $request->session()->regenerate();

            // Arahkan user sesuai role
            //
            // $this = "objek class ini sendiri".
            // $this->redirectBasedOnRole(...) = memanggil method lain
            //   milik class ini (ada di bagian paling bawah file).
            // Auth::user() = data lengkap user yang sedang login
            //   (berupa objek User, jadi bisa dibaca ->role, ->nama, dll).
            return $this->redirectBasedOnRole(Auth::user());
        }

        // Login gagal, kembali ke halaman login dengan error
        //
        // Bagian ini hanya tercapai kalau Auth::attempt() gagal
        // (karena kalau berhasil, sudah "return" di atas).
        //
        //   back()          -> kembali ke halaman sebelumnya (form login)
        //   ->withErrors()  -> kirim pesan error ke form, bisa ditampilkan
        //                      dengan @error('email') di blade
        //   ->onlyInput('email') -> isi kembali kolom email di form supaya
        //                      tidak perlu ketik ulang. Password SENGAJA
        //                      tidak diisi ulang demi keamanan.
        //
        // Pesan error sengaja tidak menyebut mana yang salah (email atau
        // password) agar penyerang tidak bisa menebak email yang terdaftar.
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * METHOD showRegisterForm() -> TAMPILKAN FORM REGISTRASI.
     *
     * Menampilkan halaman registrasi customer.
     */
    public function showRegisterForm()
    {
        // Menampilkan file resources/views/auth/register.blade.php
        return view('auth.register');
    }

    /**
     * METHOD register() -> PROSES REGISTRASI.
     *
     * Meregistrasi customer baru.
     *
     * Role otomatis diatur menjadi 'customer' karena
     * registrasi hanya diperuntukkan untuk customer.
     *
     * ALUR:
     *  1. Pengunjung mengisi form lalu klik Daftar.
     *  2. Data divalidasi. Kalau salah -> kembali ke form + pesan error.
     *  3. Kalau benar -> password di-hash, role diisi 'customer', lalu disimpan.
     *  4. Pindah ke halaman login + pesan sukses.
     *
     * PARAMETER:
     *   Request $request = objek yang membawa isi form registrasi.
     */
    public function register(Request $request)
    {
        // Validasi data registrasi
        $validated = $request->validate([
            // nama: wajib, berupa teks, maksimal 255 karakter.
            'nama' => 'required|string|max:255',

            // email: wajib, format email, dan unik di tabel "users"
            // kolom "email" (tidak boleh sama dengan akun lain).
            'email' => 'required|email|unique:users,email',

            // no_telp: boleh kosong (nullable), berupa teks, maksimal 20 karakter.
            'no_telp' => 'nullable|string|max:20',

            // password: wajib, minimal 6 karakter, dan "confirmed" artinya
            // harus sama dengan input "password_confirmation" di form.
            'password' => 'required|min:6|confirmed',
        ]);

        // Membuat user baru dengan role 'customer'
        //
        // User::create([...]) = membuat 1 baris data BARU di tabel users.
        // Syarat: kolom-kolom ini harus terdaftar di $fillable pada Model User.
        User::create([
            // $validated['nama'] = ambil nilai "nama" dari data yang sudah valid.
            'nama' => $validated['nama'],
            'email' => $validated['email'],

            // ?? null (null coalescing) artinya:
            // "kalau no_telp ada isinya pakai itu, kalau tidak ada pakai null (kosong)".
            'no_telp' => $validated['no_telp'] ?? null,

            // Role DITULIS LANGSUNG di kode, bukan dari form, sehingga
            // pengunjung tidak bisa mendaftar sebagai admin atau petugas.
            'role' => 'customer',

            // Hash::make(...) = mengacak password menjadi teks panjang
            // yang tidak bisa dibaca kembali.
            'password' => Hash::make($validated['password']),
        ]);

        // Redirect ke halaman login dengan pesan sukses
        //
        // redirect()->route('login') = pindah ke halaman yang route-nya
        //   bernama "login" (dipanggil lewat NAMA, bukan URL).
        // ->with('success', '...') = pesan sekali tampil (flash message).
        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
    }

    /**
     * METHOD logout() -> PROSES LOGOUT.
     *
     * Logout user.
     *
     * Menggunakan method POST untuk keamanan.
     *
     * KENAPA POST (bukan GET)?
     * Kalau logout memakai link biasa (GET), orang jahat bisa memasang
     * gambar/link tersembunyi yang otomatis membuat kita logout.
     * Dengan POST, form logout dilindungi CSRF token.
     */
    public function logout(Request $request)
    {
        // Logout user
        // Menghapus status "sudah login" dari user ini.
        Auth::logout();

        // Invalidate session
        // Menghapus SEMUA isi session lama supaya tidak bisa dipakai lagi.
        $request->session()->invalidate();

        // Regenerasi CSRF token
        // Membuat token CSRF baru agar token lama tidak bisa disalahgunakan.
        $request->session()->regenerateToken();

        // Redirect ke halaman login
        return redirect()
            ->route('login')
            ->with('success', 'Anda telah logout.');
    }

    /**
     * METHOD redirectBasedOnRole() -> ARAHKAN USER SESUAI ROLE.
     *
     * Mengarahkan user ke dashboard sesuai role.
     *
     * Ini adalah method BANTU (helper) yang dipanggil oleh login().
     * Tidak dipanggil lewat route, hanya dipakai di dalam class ini.
     *
     * "protected" artinya hanya boleh dipanggil dari dalam class ini
     * dan class turunannya (tidak bisa dipanggil dari luar).
     * Bandingkan dengan "public" yang bisa dipanggil dari mana saja.
     *
     * PARAMETER:
     *   $user = objek User yang baru login (berisi kolom role, nama, dll).
     */
    protected function redirectBasedOnRole($user)
    {
        // SWITCH = percabangan untuk memilih satu dari banyak kemungkinan.
        // Nilai $user->role dicocokkan dengan tiap "case" dari atas ke bawah.
        // $user->role = isi kolom "role" milik user ini.
        switch ($user->role) {
            // Jika role-nya 'admin' -> ke dashboard admin.
            case 'admin':
                return redirect()->route('admin.dashboard');

            // Jika role-nya 'petugas' -> ke dashboard petugas.
            case 'petugas':
                return redirect()->route('petugas.dashboard');

            // Jika role-nya 'customer' -> ke dashboard customer.
            case 'customer':
                return redirect()->route('customer.dashboard');

            // DEFAULT = jika tidak ada case yang cocok (role tidak dikenal),
            // kembalikan ke halaman login.
            default:
                return redirect()->route('login');
        }
    }
}