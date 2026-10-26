<?php

/*
|--------------------------------------------------------------------------
| ALUR UMUM FILE INI (BACA DULU)
|--------------------------------------------------------------------------
| Ini adalah CONTROLLER untuk halaman DASHBOARD (halaman ringkasan).
| Setiap role punya dashboard sendiri, jadi ada 3 method:
|   - adminDashboard()     -> untuk admin    (statistik user + pengaduan)
|   - petugasDashboard()   -> untuk petugas  (statistik status + 5 pengaduan terbaru)
|   - customerDashboard()  -> untuk customer (statistik MILIKNYA SAJA)
|
| Hubungan dengan AuthController:
|   Setelah login berhasil, AuthController::redirectBasedOnRole() mengarahkan
|   user ke route dashboard sesuai role, lalu route tersebut memanggil
|   salah satu method di file ini.
|
|   Login berhasil
|        |
|        v
|   redirectBasedOnRole()  -> admin / petugas / customer
|        |
|        v
|   ROUTE dashboard -> MIDDLEWARE (cek role) -> method di file ini
|        |
|        v
|   MODEL (User, Pengaduan) -> hitung data (count) / ambil data (get)
|        |
|        v
|   VIEW dashboard.admin / dashboard.petugas / dashboard.customer
|
| Status pengaduan yang dipakai di sistem ini:
|   menunggu -> diproses -> selesai
*/

// NAMESPACE = "alamat folder" class ini (app/Http/Controllers/).
// Berada langsung di folder Controllers karena dipakai oleh semua role.
namespace App\Http\Controllers;

// USE = "memanggil" class lain supaya bisa dipakai di file ini.

// MODEL User = mewakili tabel "users" di database.
use App\Models\User;

// MODEL Pengaduan = mewakili tabel "pengaduan" di database.
use App\Models\Pengaduan;

// Request = class bawaan Laravel untuk membawa data dari browser.
// CATATAN: di file ini Request sebenarnya TIDAK dipakai (tidak ada method
// yang menerima $request). Tidak error, tapi boleh dihapus agar rapi.
use Illuminate\Http\Request;

// Auth = alat bawaan Laravel untuk mengetahui siapa user yang sedang login.
// Auth::id() menghasilkan id user yang sedang login.
use Illuminate\Support\Facades\Auth;

/**
 * CLASS = "cetakan/blueprint" yang berisi kumpulan method (fungsi).
 *
 * Controller yang menangani tampilan dashboard berdasarkan role user.
 *
 * Setiap role memiliki dashboard berbeda agar admin, petugas, dan customer
 * hanya melihat informasi yang relevan dengan tugas mereka masing-masing.
 *
 * "extends Controller" artinya DashboardController MEWARISI (inheritance)
 * semua kemampuan dari class Controller induknya.
 */
class DashboardController extends Controller
{
    /**
     * METHOD adminDashboard() -> DASHBOARD ADMIN.
     *
     * Menampilkan dashboard untuk Admin.
     *
     * Di sini admin melihat jumlah user, jumlah customer, jumlah petugas,
     * dan total pengaduan yang masuk ke sistem.
     *
     * ALUR:
     *  1. Hitung jumlah data di database (pakai count()).
     *  2. Simpan hasilnya ke beberapa variable.
     *  3. Kirim semua variable ke view untuk ditampilkan sebagai kartu statistik.
     *
     * Method = fungsi yang berada DI DALAM class.
     * "public" artinya boleh dipanggil dari luar class (oleh Route).
     */
    public function adminDashboard()
    {
        // Menghitung jumlah seluruh user berdasarkan role.
        //
        // $totalUsers dst. = VARIABLE (wadah penyimpan data sementara).
        // Tanda $ selalu menandakan variable di PHP.

        // User::count() = hitung SEMUA baris di tabel users
        // (admin + petugas + customer). Hasilnya berupa angka.
        $totalUsers = User::count();

        // User::where('role', 'customer')->count() dibaca:
        //   where('role', 'customer') -> saring hanya baris yang role-nya 'customer'
        //   ->count()                 -> hitung jumlah baris hasil saringan itu
        $totalCustomers = User::where('role', 'customer')->count();

        // Sama seperti di atas, tetapi untuk role 'petugas'.
        $totalPetugas = User::where('role', 'petugas')->count();

        // Pengaduan::count() = hitung SEMUA baris di tabel pengaduan.
        $totalPengaduan = Pengaduan::count();

        // view() = fungsi bawaan Laravel untuk menampilkan halaman.
        // 'dashboard.admin' = file resources/views/dashboard/admin.blade.php
        // (tanda titik dibaca sebagai tanda folder "/").
        //
        // compact(...) = membungkus variable-variable di atas menjadi array
        // supaya bisa dipakai di blade dengan nama yang sama
        // (misalnya {{ $totalUsers }}).
        // Hasilnya sama dengan: ['totalUsers' => $totalUsers, ...]
        return view('dashboard.admin', compact(
            'totalUsers',
            'totalCustomers',
            'totalPetugas',
            'totalPengaduan'
        ));
    }

    /**
     * METHOD petugasDashboard() -> DASHBOARD PETUGAS.
     *
     * Menampilkan dashboard untuk Petugas.
     *
     * Petugas melihat jumlah pengaduan berdasarkan status dan daftar pengaduan
     * terbaru yang sedang ditangani atau menunggu respon.
     *
     * Petugas melihat pengaduan dari SEMUA customer (tidak difilter user_id),
     * karena tugas petugas memang menangani seluruh pengaduan.
     */
    public function petugasDashboard()
    {
        // Menghitung pengaduan berdasarkan status.

        // Total semua pengaduan dari semua customer.
        $totalPengaduan = Pengaduan::count();

        // Hitung pengaduan yang statusnya 'menunggu' (belum disentuh petugas).
        $menunggu = Pengaduan::where('status', 'menunggu')->count();

        // Hitung pengaduan yang statusnya 'diproses' (sedang ditangani).
        $diproses = Pengaduan::where('status', 'diproses')->count();

        // Hitung pengaduan yang statusnya 'selesai' (sudah beres).
        $selesai = Pengaduan::where('status', 'selesai')->count();

        // Mengambil 5 pengaduan terbaru untuk ditampilkan di dashboard.
        //
        // Dibaca dari atas ke bawah (method chaining):
        //   Pengaduan::with('user')
        //       -> pakai Model Pengaduan, sekaligus ikut ambil data pemiliknya
        //          dari relasi "user" (Eager Loading). Gunanya supaya nama
        //          pelapor bisa ditampilkan tanpa query tambahan per baris
        //          (mencegah masalah N+1 query yang membuat aplikasi lambat).
        //   ->latest()
        //       -> urutkan dari data TERBARU (kolom created_at).
        //   ->take(5)
        //       -> batasi hanya 5 data.
        //   ->get()
        //       -> jalankan query dan ambil hasilnya.
        $recentPengaduan = Pengaduan::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Kirim semua data ke file resources/views/dashboard/petugas.blade.php
        return view('dashboard.petugas', compact(
            'totalPengaduan',
            'menunggu',
            'diproses',
            'selesai',
            'recentPengaduan'
        ));
    }

    /**
     * METHOD customerDashboard() -> DASHBOARD CUSTOMER.
     *
     * Menampilkan dashboard untuk Customer.
     *
     * Customer hanya melihat statistik pengaduan miliknya sendiri, bukan data
     * pengaduan semua user. Ini menjaga privasi masing-masing pelanggan.
     *
     * Bedanya dengan dashboard petugas: SEMUA query di sini ditambah
     * where('user_id', Auth::id()) sebagai filter.
     */
    public function customerDashboard()
    {
        // Menghitung pengaduan customer yang sedang login.
        //
        // Auth::id() = id user yang sedang login.
        // where('user_id', Auth::id()) = saring hanya pengaduan yang
        // pemiliknya adalah user yang sedang login.

        // Total pengaduan milik customer ini.
        $totalPengaduan = Pengaduan::where('user_id', Auth::id())->count();

        // Pengaduan miliknya yang berstatus 'menunggu'.
        // where() boleh dirangkai lebih dari satu: kedua syarat HARUS terpenuhi
        // (user_id cocok DAN status cocok).
        $menunggu = Pengaduan::where('user_id', Auth::id())
            ->where('status', 'menunggu')
            ->count();

        // Pengaduan miliknya yang berstatus 'diproses'.
        $diproses = Pengaduan::where('user_id', Auth::id())
            ->where('status', 'diproses')
            ->count();

        // Pengaduan miliknya yang berstatus 'selesai'.
        $selesai = Pengaduan::where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->count();

        // Mengambil pengaduan terbaru milik customer yang sedang login.
        // Urutannya: saring milik user ini -> urutkan terbaru -> ambil 5 -> get.
        $recentPengaduan = Pengaduan::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        // Kirim semua data ke file resources/views/dashboard/customer.blade.php
        return view('dashboard.customer', compact(
            'totalPengaduan',
            'menunggu',
            'diproses',
            'selesai',
            'recentPengaduan'
        ));
    }
}