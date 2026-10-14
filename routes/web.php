<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Petugas\CustomerController;
use App\Http\Controllers\Customer\PengaduanController as CustomerPengaduanController;
use Illuminate\Support\Facades\Route;

/**
 * File route utama aplikasi pengaduan.
 *
 * File ini menentukan URL yang bisa diakses user, method HTTP yang dipakai,
 * controller yang menangani request, serta middleware yang harus dijalankan
 * sebelum akses diterima. Pembagian route dibuat sesuai role pengguna:
 * admin, petugas, dan customer.
 */

// Halaman utama - redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes - hanya untuk yang belum login
Route::middleware('guest')->group(function () {
    // Halaman login menampilkan form login.
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // Proses login dilakukan dengan metode POST saat user mengirim email dan password.
    Route::post('/login', [AuthController::class, 'login']);
    // Halaman daftar pelanggan baru untuk akun customer.
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    // Proses penyimpanan data akun baru customer.
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout - menggunakan POST agar lebih aman dan tidak bisa dijalankan lewat link biasa.
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==================
// ROUTE ADMIN
// ==================
// Route admin hanya bisa diakses oleh user yang sudah login dan memiliki role admin.
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard admin menampilkan statistik jumlah user dan pengaduan di sistem.
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // Menu User - hanya Admin yang bisa akses CRUD lengkap untuk semua user.
    Route::resource('users', UserController::class);

    // Menu Kategori - hanya Admin yang bisa mengelola kategori pengaduan.
    Route::resource('kategori', KategoriController::class);

    // Halaman profil admin berdasarkan data user yang sedang login.
    Route::get('/profile', [ProfileController::class, 'adminProfile'])->name('profile');
    // Menampilkan semua pengaduan untuk admin dalam satu daftar.
    Route::get('/pengaduan', [PengaduanController::class, 'adminIndex'])->name('pengaduan.index');
    // Menampilkan detail satu pengaduan sesuai id.
    Route::get('/pengaduan/{id}', [PengaduanController::class, 'show'])->name('pengaduan.show');
    // Mengubah status pengaduan seperti menunggu, diproses, atau selesai.
    Route::put('/pengaduan/{id}/status', [PengaduanController::class, 'updateStatus'])->name('pengaduan.updateStatus');
});

// ==================
// ROUTE PETUGAS
// ==================
// Route petugas hanya bisa diakses oleh user yang sudah login dan memiliki role petugas.
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    // Dashboard petugas menampilkan statistik pengaduan yang sedang ditangani.
    Route::get('/dashboard', [DashboardController::class, 'petugasDashboard'])->name('dashboard');

    // Petugas hanya bisa melihat daftar customer serta menambah customer baru.
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

    // Profil petugas menampilkan data akun petugas yang sedang login.
    Route::get('/profile', [ProfileController::class, 'petugasProfile'])->name('profile');
    // Menampilkan daftar pengaduan yang bisa dilihat petugas.
    Route::get('/pengaduan', [PengaduanController::class, 'petugasIndex'])->name('pengaduan.index');
    // Menampilkan detail pengaduan agar petugas bisa melihat rincian laporan.
    Route::get('/pengaduan/{id}', [PengaduanController::class, 'show'])->name('pengaduan.show');
    // Petugas bisa memperbarui status pengaduan setelah menangani laporan.
    Route::put('/pengaduan/{id}/status', [PengaduanController::class, 'updateStatus'])->name('pengaduan.updateStatus');
});

// ==================
// ROUTE CUSTOMER
// ==================
// Route customer hanya bisa diakses oleh user yang sudah login dan memiliki role customer.
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard customer menampilkan ringkasan pengaduan milik user yang sedang login.
    Route::get('/dashboard', [DashboardController::class, 'customerDashboard'])->name('dashboard');

    // Profil customer menampilkan data akun customer yang login.
    Route::get('/profile', [ProfileController::class, 'customerProfile'])->name('profile');

    // Customer melihat daftar pengaduan yang dibuatnya sendiri.
    Route::get('/pengaduan', [CustomerPengaduanController::class, 'index'])->name('pengaduan.index');
    // Form pembuatan pengaduan baru.
    Route::get('/pengaduan/create', [CustomerPengaduanController::class, 'create'])->name('pengaduan.create');
    // Proses menyimpan pengaduan baru dari customer.
    Route::post('/pengaduan', [CustomerPengaduanController::class, 'store'])->name('pengaduan.store');
});