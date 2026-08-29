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

// Halaman utama - redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes - hanya untuk yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout - menggunakan POST
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==================
// ROUTE ADMIN
// ==================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // Menu User - hanya Admin yang bisa akses (CRUD lengkap)
    Route::resource('users', UserController::class);

    // Menu Kategori - hanya Admin yang bisa akses (CRUD lengkap)
    Route::resource('kategori', KategoriController::class);

    Route::get('/profile', [ProfileController::class, 'adminProfile'])->name('profile');
    Route::get('/pengaduan', [PengaduanController::class, 'adminIndex'])->name('pengaduan.index');
});

// ==================
// ROUTE PETUGAS
// ==================
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'petugasDashboard'])->name('dashboard');

    // Petugas hanya boleh melihat & menambah customer
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

    Route::get('/profile', [ProfileController::class, 'petugasProfile'])->name('profile');
    Route::get('/pengaduan', [PengaduanController::class, 'petugasIndex'])->name('pengaduan.index');
});

// ==================
// ROUTE CUSTOMER
// ==================
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'customerDashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'customerProfile'])->name('profile');

    Route::get('/pengaduan', [CustomerPengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/create', [CustomerPengaduanController::class, 'create'])->name('pengaduan.create');
    Route::post('/pengaduan', [CustomerPengaduanController::class, 'store'])->name('pengaduan.store');
});