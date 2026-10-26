<?php

/*
|--------------------------------------------------------------------------
| ALUR UMUM FILE INI (BACA DULU)
|--------------------------------------------------------------------------
| Ini adalah CONTROLLER dalam pola MVC (Model - View - Controller).
|
|   Browser (admin klik/ketik)
|        |
|        v
|   ROUTE (routes/web.php)  -> menentukan URL ini diarahkan ke method mana
|        |
|        v
|   MIDDLEWARE              -> "satpam": cek sudah login? role-nya admin?
|        |
|        v
|   CONTROLLER (file ini)   -> "otak": menerima request lalu memproses
|        |
|        v
|   MODEL (User)            -> "penghubung ke database" (tabel users)
|        |
|        v
|   VIEW (blade)            -> "tampilan" yang dikirim kembali ke browser
|
| Fitur file ini disebut CRUD (kelola data user):
|   C = Create (tambah)  -> create() + store()
|   R = Read   (lihat)   -> index() + show()
|   U = Update (ubah)    -> edit() + update()
|   D = Delete (hapus)   -> destroy()
|
| Ada 3 jenis user (role) di sistem ini: admin, petugas, customer.
*/

// NAMESPACE = "alamat folder" class ini di dalam project Laravel
// (app/Http/Controllers/Admin/). Dipakai agar Laravel bisa menemukan
// class ini secara otomatis (autoload).
namespace App\Http\Controllers\Admin;

// USE = "memanggil" class lain supaya bisa dipakai di file ini.

// Controller induk (parent). Semua controller mewarisi class ini.
use App\Http\Controllers\Controller;

// MODEL User = class yang mewakili tabel "users" di database.
// Lewat model ini kita bisa ambil, tambah, ubah, dan hapus data user.
use App\Models\User;

// Request = class bawaan Laravel yang membawa semua data kiriman dari
// browser (isi form, parameter URL, dll).
use Illuminate\Http\Request;

// Hash = alat bawaan Laravel untuk MENGACAK password (enkripsi satu arah).
// Password asli TIDAK BOLEH disimpan apa adanya di database. Kalau database
// bocor, password yang sudah di-hash tidak bisa dibaca orang lain.
use Illuminate\Support\Facades\Hash;

/**
 * CLASS = "cetakan/blueprint" yang berisi kumpulan method (fungsi).
 *
 * Controller untuk mengelola data user pada sistem.
 *
 * Admin memanfaatkan controller ini untuk melihat seluruh user,
 * menambah user baru, mengubah data, dan menghapus akun.
 *
 * "extends Controller" artinya UserController MEWARISI (inheritance)
 * semua kemampuan dari class Controller induknya.
 */
class UserController extends Controller
{
    /**
     * METHOD index() -> bagian "R" (Read): lihat DAFTAR semua user.
     *
     * Menampilkan daftar semua user dalam sistem.
     *
     * Data yang ditampilkan meliputi admin, petugas, dan customer.
     * Penggunaannya menggunakan pagination agar tampilan tidak terlalu panjang.
     *
     * ALUR:
     *  1. Ambil data user dari database lewat Model.
     *  2. Urutkan dari yang terbaru, bagi per halaman (10 data/halaman).
     *  3. Kirim datanya ke view untuk ditampilkan sebagai tabel.
     *
     * Method = fungsi yang berada DI DALAM class.
     * "public" artinya boleh dipanggil dari luar class (oleh Route).
     */
    public function index()
    {
        // $users = VARIABLE (wadah penyimpan data sementara).
        // Tanda $ selalu menandakan variable di PHP.
        //
        // User::latest()->paginate(10) dibaca dari kiri ke kanan:
        //   User         -> pakai Model User (tabel "users")
        //   ::           -> cara memanggil method static milik class
        //   latest()     -> urutkan dari data TERBARU (kolom created_at)
        //   ->           -> lanjut memanggil method berikutnya (method chaining)
        //   paginate(10) -> ambil 10 data saja per halaman, otomatis
        //                   membuat nomor halaman (1, 2, 3, ...)
        $users = User::latest()->paginate(10);

        // view() = fungsi bawaan Laravel untuk menampilkan halaman.
        // 'users.index' = file resources/views/users/index.blade.php
        // (tanda titik dibaca sebagai tanda folder "/").
        //
        // compact('users') = membungkus variable $users menjadi array
        // supaya bisa dipakai di blade dengan nama $users.
        // Hasilnya sama dengan: ['users' => $users]
        return view('users.index', compact('users'));
    }

    /**
     * METHOD create() -> bagian "C" (Create) tahap 1: TAMPILKAN FORM.
     *
     * Menampilkan form untuk menambah user baru.
     *
     * Form ini dipakai admin saat ingin membuat akun baru
     * dengan role tertentu sesuai kebutuhan sistem.
     *
     * CATATAN: method ini HANYA menampilkan form kosong.
     * Penyimpanan datanya dilakukan oleh method store() di bawah.
     */
    public function create()
    {
        // Tidak ada data yang dikirim, karena form tambah memang kosong.
        return view('users.create');
    }

    /**
     * METHOD store() -> bagian "C" (Create) tahap 2: SIMPAN KE DATABASE.
     *
     * Menyimpan user baru ke database.
     *
     * Data yang masuk divalidasi terlebih dahulu agar email unik,
     * nomor telepon sesuai format, dan password sesuai ketentuan.
     * Setelah itu password di-hash agar aman sebelum disimpan.
     *
     * ALUR:
     *  1. Admin mengisi form lalu klik Simpan.
     *  2. Data dikirim ke method ini lewat $request.
     *  3. Data divalidasi. Kalau salah -> kembali ke form + pesan error.
     *  4. Kalau benar -> password di-hash, lalu user disimpan ke database.
     *  5. Pindah ke halaman daftar user + pesan sukses.
     *
     * PARAMETER (data masukan method):
     *   Request $request = objek yang membawa semua isi form dari browser.
     *   "Request" = tipe datanya (class), "$request" = nama variable-nya.
     */
    public function store(Request $request)
    {
        // Validasi data input agar tidak ada data kosong atau format salah.
        //
        // $request->validate([...]) = fungsi untuk MEMERIKSA isi form.
        // Jika ada yang salah, Laravel otomatis menghentikan proses dan
        // mengembalikan admin ke form beserta pesan error-nya.
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
            //   nullable                -> boleh dikosongkan
            //   regex:/^[0-9]+$/        -> hanya boleh ANGKA 0-9
            //                              (^ awal teks, $ akhir teks, + minimal 1 karakter)
            //   max:20                  -> maksimal 20 karakter
            'no_telp' => 'nullable|regex:/^[0-9]+$/|max:20',

            // password:
            //   required   -> wajib diisi
            //   min:6      -> minimal 6 karakter
            //   confirmed  -> harus sama dengan input "password_confirmation"
            //                 (kolom "ulangi password" di form)
            'password' => 'required|min:6|confirmed',

            // role:
            //   required              -> wajib dipilih
            //   in:admin,petugas,customer -> hanya boleh salah satu dari 3 nilai ini
            'role' => 'required|in:admin,petugas,customer',
        ]);

        // User::create([...]) = membuat 1 baris data BARU di tabel users.
        // Di sini data disusun satu per satu (bukan langsung $validated)
        // karena password harus di-hash dulu sebelum disimpan.
        // Syarat: kolom-kolom ini harus terdaftar di $fillable pada Model User.
        User::create([
            // $validated['nama'] = ambil nilai "nama" dari data yang sudah valid.
            'nama' => $validated['nama'],
            'email' => $validated['email'],

            // Tanda ?? null (null coalescing) artinya:
            // "kalau no_telp ada isinya pakai itu, kalau tidak ada pakai null (kosong)".
            'no_telp' => $validated['no_telp'] ?? null,

            'role' => $validated['role'],

            // Hash::make(...) = mengacak password menjadi teks panjang
            // yang tidak bisa dibaca kembali. Contoh: "123456" menjadi
            // "$2y$10$abc...". Saat login, Laravel membandingkan hasil hash-nya.
            'password' => Hash::make($validated['password']),
        ]);

        // redirect() = pindahkan browser ke halaman lain.
        // ->route('admin.users.index') = tujuan pindah, dipanggil lewat NAMA route
        //     (bukan URL), jadi aman kalau URL-nya berubah.
        // ->with('success', '...') = kirim pesan singkat (flash message)
        //     yang hanya muncul 1x di halaman tujuan, lalu hilang sendiri.
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * METHOD show() -> bagian "R" (Read): lihat DETAIL 1 user.
     *
     * Menampilkan detail satu user khusus.
     *
     * Laravel akan otomatis menangkap model User berdasarkan ID yang dikirim
     * melalui URL, lalu menampilkan datanya di halaman detail.
     *
     * PARAMETER: User $user
     * Ini fitur Laravel bernama ROUTE MODEL BINDING. Contoh URL
     * /admin/users/5 -> Laravel otomatis mencari user ber-id 5 di database
     * lalu memasukkannya ke variable $user. Jika id tidak ada,
     * Laravel otomatis menampilkan halaman 404 (tidak ditemukan).
     */
    public function show(User $user)
    {
        // Kirim data satu user ($user) ke halaman detail.
        return view('users.show', compact('user'));
    }

    /**
     * METHOD edit() -> bagian "U" (Update) tahap 1: TAMPILKAN FORM EDIT.
     *
     * Menampilkan form edit data user.
     *
     * Data user yang dipilih dimasukkan ke form supaya admin bisa
     * mengubah nama, email, nomor telepon, role, dan password jika diperlukan.
     *
     * Sama seperti show(), $user diambil otomatis dari database
     * lewat Route Model Binding berdasarkan id di URL.
     */
    public function edit(User $user)
    {
        // Kirim data lama ke form, supaya kolom form terisi otomatis.
        return view('users.edit', compact('user'));
    }

    /**
     * METHOD update() -> bagian "U" (Update) tahap 2: SIMPAN PERUBAHAN.
     *
     * Memperbarui data user yang dipilih.
     *
     * Password bersifat opsional. Jika kolom password diisi,
     * maka password lama diganti dengan password baru yang sudah di-hash.
     * Jika tidak diisi, password lama tetap dipertahankan.
     *
     * PARAMETER:
     *   Request $request = isi form yang baru dikirim admin
     *   User $user       = data user LAMA yang sedang diedit
     */
    public function update(Request $request, User $user)
    {
        // Validasi data input, dengan pengecualian email milik user yang sedang diedit.
        $validated = $request->validate([
            'nama' => 'required|string|max:255',

            // Bedanya dengan store(): ada tambahan ',' . $user->id
            //   $user->id = ambil nilai kolom id milik user ini.
            //   Tanda titik (.) di PHP = menggabungkan dua teks.
            // Artinya: "email harus unik, KECUALI untuk baris dengan id ini".
            // Tanpa ini, menyimpan tanpa mengganti email akan dianggap
            // duplikat dan muncul error, padahal itu emailnya sendiri.
            'email' => 'required|email|unique:users,email,' . $user->id,

            'no_telp' => 'nullable|regex:/^[0-9]+$/|max:20',

            // Bedanya dengan store(): password "nullable" (boleh kosong).
            // Kosong = admin tidak ingin mengganti password.
            // Kalau diisi tetap harus min 6 karakter dan cocok dengan konfirmasinya.
            'password' => 'nullable|min:6|confirmed',

            'role' => 'required|in:admin,petugas,customer',
        ]);

        // Mengisi nilai ke PROPERTY (atribut) objek $user satu per satu.
        // $user->nama = mengakses kolom "nama" milik user ini.
        // Di tahap ini data BARU berubah di memori saja, belum masuk database.
        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->no_telp = $validated['no_telp'] ?? null;
        $user->role = $validated['role'];

        // Update password hanya jika admin mengisi field password.
        // IF = percabangan (kondisi). Isinya hanya dijalankan kalau kondisinya benar.
        //   empty(...) = true jika nilainya kosong
        //   !          = tanda "TIDAK" (kebalikan)
        // Jadi: "jika password TIDAK kosong, maka ganti password-nya".
        if (!empty($validated['password'])) {
            // Password baru di-hash dulu sebelum disimpan.
            $user->password = Hash::make($validated['password']);
        }

        // ->save() = menyimpan semua perubahan di atas ke database
        // (menjalankan perintah UPDATE pada baris user ini).
        $user->save();

        // Kembali ke halaman daftar sambil membawa pesan sukses.
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * METHOD destroy() -> bagian "D" (Delete): HAPUS user.
     *
     * Menghapus user dari database.
     *
     * Setelah data user dihapus, user tersebut tidak akan bisa login lagi.
     */
    public function destroy(User $user)
    {
        // ->delete() = hapus baris data user ini dari tabel users.
        $user->delete();

        // Kembali ke halaman daftar sambil membawa pesan sukses.
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}