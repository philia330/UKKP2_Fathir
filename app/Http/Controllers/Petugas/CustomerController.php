<?php

/*
|--------------------------------------------------------------------------
| ALUR UMUM FILE INI (BACA DULU)
|--------------------------------------------------------------------------
| Ini adalah CONTROLLER untuk PETUGAS yang mengelola data CUSTOMER.
| Petugas hanya bisa:
|   - melihat DAFTAR customer              -> index()
|   - membuka FORM tambah customer         -> create()
|   - MENYIMPAN customer baru              -> store()
| (Tidak ada edit dan hapus, dan petugas tidak bisa memilih role.)
|
|   Browser (petugas klik/ketik)
|        |
|        v
|   ROUTE (routes/web.php)  -> URL diarahkan ke method yang tepat
|        |
|        v
|   MIDDLEWARE              -> "satpam": cek sudah login? role-nya petugas?
|        |
|        v
|   CONTROLLER (file ini)   -> "otak": memproses permintaan
|        |
|        v
|   MODEL (User)            -> "penghubung ke database" (tabel users)
|        |
|        v
|   VIEW (blade)            -> "tampilan" yang dikirim ke browser
|
| Bedanya dengan UserController milik admin:
|   - Admin  : boleh lihat semua user dan memilih role apa saja.
|   - Petugas: hanya melihat customer, dan akun baru SELALU berrole customer.
*/

// NAMESPACE = "alamat folder" class ini di dalam project Laravel
// (app/Http/Controllers/Petugas/). Dipisah per peran (Admin, Petugas,
// Customer) supaya project rapi dan mudah dicari.
namespace App\Http\Controllers\Petugas;

// USE = "memanggil" class lain supaya bisa dipakai di file ini.

// Controller induk (parent). Semua controller mewarisi class ini.
use App\Http\Controllers\Controller;

// MODEL User = mewakili tabel "users" di database.
// Customer, petugas, dan admin semuanya ada di tabel yang sama,
// dibedakan lewat kolom "role".
use App\Models\User;

// Request = class bawaan Laravel yang membawa semua data kiriman dari
// browser (isi form, parameter URL, dll).
use Illuminate\Http\Request;

// Hash = alat bawaan Laravel untuk MENGACAK password (enkripsi satu arah).
// Password asli TIDAK BOLEH disimpan apa adanya di database.
use Illuminate\Support\Facades\Hash;

/**
 * CLASS = "cetakan/blueprint" yang berisi kumpulan method (fungsi).
 *
 * Controller khusus untuk kebutuhan petugas dalam mengelola customer.
 *
 * Petugas tidak bisa mengakses data admin atau mengubah role user,
 * tetapi masih dapat melihat dan menambahkan customer baru.
 *
 * "extends Controller" artinya CustomerController MEWARISI (inheritance)
 * semua kemampuan dari class Controller induknya.
 */
class CustomerController extends Controller
{
    /**
     * METHOD index() -> melihat DAFTAR customer.
     *
     * Menampilkan daftar customer yang terdaftar di sistem.
     *
     * Query hanya mengambil user dengan role customer agar petugas tidak
     * melihat data admin atau petugas lain.
     *
     * ALUR:
     *  1. Ambil dari tabel users HANYA baris yang role-nya "customer".
     *  2. Urutkan dari yang terbaru, bagi per halaman (10 data/halaman).
     *  3. Kirim ke view untuk ditampilkan sebagai tabel.
     *
     * Method = fungsi yang berada DI DALAM class.
     * "public" artinya boleh dipanggil dari luar class (oleh Route).
     */
    public function index()
    {
        // $customers = VARIABLE (wadah penyimpan data sementara).
        // Tanda $ selalu menandakan variable di PHP.
        //
        // Dibaca dari atas ke bawah (method chaining):
        //   User::where('role', 'customer')
        //       -> pakai Model User, ambil hanya baris yang kolom role-nya
        //          SAMA DENGAN 'customer'. Ini filter keamanan: data admin
        //          dan petugas tidak ikut terambil.
        //   ->latest()
        //       -> urutkan dari data TERBARU (kolom created_at).
        //   ->paginate(10)
        //       -> ambil 10 data per halaman, otomatis membuat nomor halaman.
        $customers = User::where('role', 'customer')
            ->latest()
            ->paginate(10);

        // view() = fungsi bawaan Laravel untuk menampilkan halaman.
        // 'customers.index' = file resources/views/customers/index.blade.php
        // (tanda titik dibaca sebagai tanda folder "/").
        //
        // compact('customers') = membungkus variable $customers menjadi array
        // agar bisa dipakai di blade dengan nama $customers.
        // Hasilnya sama dengan: ['customers' => $customers]
        return view('customers.index', compact('customers'));
    }

    /**
     * METHOD create() -> TAMPILKAN FORM tambah customer.
     *
     * Menampilkan form pendaftaran customer baru oleh petugas.
     *
     * Proses ini biasanya dipakai ketika customer belum memiliki akun
     * atau petugas ingin membantu pembuatan akun.
     *
     * CATATAN: method ini HANYA menampilkan form kosong.
     * Penyimpanan datanya dilakukan oleh method store() di bawah.
     * Perhatikan: di form tidak ada pilihan role, karena role diisi otomatis.
     */
    public function create()
    {
        // Tidak ada data yang dikirim, karena form tambah memang kosong.
        return view('customers.create');
    }

    /**
     * METHOD store() -> SIMPAN customer baru ke database.
     *
     * Menyimpan customer baru ke database.
     *
     * Semua akun yang dibuat dari sini otomatis diberi role customer,
     * karena petugas tidak diperkenankan membuat akun admin atau petugas.
     *
     * ALUR:
     *  1. Petugas mengisi form lalu klik Simpan.
     *  2. Data dikirim ke method ini lewat $request.
     *  3. Data divalidasi. Kalau salah -> kembali ke form + pesan error.
     *  4. Kalau benar -> password di-hash, role diisi 'customer', lalu disimpan.
     *  5. Pindah ke halaman daftar customer + pesan sukses.
     *
     * PARAMETER (data masukan method):
     *   Request $request = objek yang membawa semua isi form dari browser.
     *   "Request" = tipe datanya (class), "$request" = nama variable-nya.
     */
    public function store(Request $request)
    {
        // Validasi data input agar email unik dan password aman.
        //
        // $request->validate([...]) = fungsi untuk MEMERIKSA isi form.
        // Jika ada yang salah, Laravel otomatis menghentikan proses dan
        // mengembalikan petugas ke form beserta pesan error-nya.
        // Jika semua benar, data yang sudah bersih disimpan ke $validated.
        $validated = $request->validate([
            // nama:
            //   required -> wajib diisi
            //   string   -> harus berupa teks
            //   max:255  -> maksimal 255 karakter
            'nama' => 'required|string|max:255',

            // email:
            //   required            -> wajib diisi
            //   email               -> formatnya harus email (ada @ dan domain)
            //   unique:users,email  -> tidak boleh sama dengan email user lain
            //                          di tabel "users" kolom "email"
            'email' => 'required|email|unique:users,email',

            // no_telp:
            //   nullable          -> boleh dikosongkan
            //   regex:/^[0-9]+$/  -> hanya boleh ANGKA 0-9
            //                        (^ awal teks, $ akhir teks, + minimal 1 karakter)
            //   max:20            -> maksimal 20 karakter
            'no_telp' => 'nullable|regex:/^[0-9]+$/|max:20',

            // password:
            //   required   -> wajib diisi
            //   min:6      -> minimal 6 karakter
            //   confirmed  -> harus sama dengan input "password_confirmation"
            //                 (kolom "ulangi password" di form)
            'password' => 'required|min:6|confirmed',

            // CATATAN: tidak ada aturan untuk "role" karena petugas TIDAK
            // boleh menentukan role. Kalau ada yang mengirim field "role"
            // lewat cara curang, field itu diabaikan karena tidak masuk
            // ke $validated.
        ]);

        // Membuat customer baru dengan role customer.
        //
        // User::create([...]) = membuat 1 baris data BARU di tabel users.
        // Data disusun satu per satu (bukan langsung $validated) agar kita
        // bisa mengatur role dan meng-hash password dengan aman.
        // Syarat: kolom-kolom ini harus terdaftar di $fillable pada Model User.
        User::create([
            // $validated['nama'] = ambil nilai "nama" dari data yang sudah valid.
            'nama' => $validated['nama'],
            'email' => $validated['email'],

            // Tanda ?? null (null coalescing) artinya:
            // "kalau no_telp ada isinya pakai itu, kalau tidak ada pakai null (kosong)".
            'no_telp' => $validated['no_telp'] ?? null,

            // Role DITULIS LANGSUNG di kode, bukan diambil dari form.
            // Jadi seberapa pun petugas mengutak-atik form, akun baru
            // tetap berrole 'customer'. Ini mencegah petugas membuat admin.
            'role' => 'customer',

            // Hash::make(...) = mengacak password menjadi teks panjang
            // yang tidak bisa dibaca kembali. Contoh: "123456" menjadi
            // "$2y$10$abc...". Saat login, Laravel membandingkan hasil hash-nya.
            'password' => Hash::make($validated['password']),
        ]);

        // redirect() = pindahkan browser ke halaman lain.
        // ->route('petugas.customers.index') = tujuan pindah, dipanggil lewat
        //     NAMA route (bukan URL), jadi aman kalau URL-nya berubah.
        // ->with('success', '...') = kirim pesan singkat (flash message)
        //     yang hanya muncul 1x di halaman tujuan, lalu hilang sendiri.
        return redirect()
            ->route('petugas.customers.index')
            ->with('success', 'Customer berhasil ditambahkan.');
    }
}