# 3. Register, login, role, middleware, dan route

## Tahap 9 — Membuat autentikasi register, login, dan logout

**Tujuan:** customer bisa mendaftar sendiri; semua role bisa login dan logout.  
**Persiapan:** tabel `users` sudah memiliki `nama`, `email`, `no_telp`, `role`, `password`.  
**Terminal:** CMD. **Direktori:** root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller AuthController
```

Ganti isi **`app/Http/Controllers/AuthController.php`**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan form login.
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memvalidasi kredensial dan memulai sesi login.
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak sesuai.'])
                ->onlyInput('email');
        }

        // Mengganti ID sesi setelah login untuk mencegah session fixation.
        $request->session()->regenerate();

        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => abort(403, 'Role akun tidak dikenali.'),
        };
    }

    // Menampilkan form pendaftaran customer.
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Pendaftaran publik selalu diberi role customer oleh server.
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'] ?? null,
            'role' => 'customer',
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    // Logout hanya boleh melalui POST dan menghapus sesi lama.
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}
```

`Hash::make()` mengubah password menjadi hash; password asli tidak disimpan. Field `role` tidak dibaca dari form register sehingga pengunjung tidak dapat mendaftarkan dirinya sebagai admin.

## Tahap 10 — Membuat middleware role

**Tujuan:** membatasi URL di sisi server, bukan hanya menyembunyikan menu.  
**Persiapan:** autentikasi dibuat.  
**Terminal:** CMD, root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:middleware RoleMiddleware
```

Isi lengkap **`app/Http/Middleware/RoleMiddleware.php`**:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // Route mengirim role yang diperbolehkan, misalnya role:admin.
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== $role) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
```

Daftarkan alias dalam **`bootstrap/app.php`** dengan mengganti seluruh file berikut (isi ini memakai bootstrap Laravel 12 standar):

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias role dapat dipakai pada route sebagai role:admin, dan seterusnya.
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

## Tahap 11 — Menulis semua route aplikasi

**Tujuan:** menghubungkan URL, middleware, controller, dan nama route.  
**Persiapan:** controller pada bab selanjutnya akan dibuat dengan nama yang dirujuk di sini.  
**Terminal:** VS Code untuk edit file. **Direktori:** path project.

Ganti isi lengkap **`routes/web.php`**:

```php
<?php

use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\PengaduanController as CustomerPengaduanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Petugas\CustomerController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Pengunjung yang belum login dapat membuka halaman autentikasi.
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('kategori', KategoriController::class)->except(['show']);
        Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
        Route::put('/pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus'])
            ->name('pengaduan.updateStatus');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'petugas'])->name('dashboard');
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
        Route::put('/pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus'])
            ->name('pengaduan.updateStatus');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

Route::middleware(['auth', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');
        Route::get('/pengaduan', [CustomerPengaduanController::class, 'index'])->name('pengaduan.index');
        Route::get('/pengaduan/create', [CustomerPengaduanController::class, 'create'])->name('pengaduan.create');
        Route::post('/pengaduan', [CustomerPengaduanController::class, 'store'])->name('pengaduan.store');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
```

Meskipun nama route `profile` muncul di tiga grup, prefix nama (`admin.`, `petugas.`, `customer.`) menjadikan nama akhirnya berbeda. Begitu juga endpoint status pengaduan.

## Uji tahap

Di CMD root project jalankan:

```bat
cd /d D:\AplikasiPengaduan
php artisan route:list
```

**Hasil yang diharapkan:** route login/register/logout dan dashboard tiap role tampil, serta route aplikasi memakai middleware `auth` dan `role`. Form Blade pada bab 6 memakai nama route yang sama persis.

**Error umum:** `Target class [role] does not exist` berarti alias middleware belum terdaftar atau ada salah namespace; `Class "App\Http\Controllers\Admin\UserController" not found` berarti file/controller belum dibuat atau namespace keliru. Jika route belum dikenali sesaat setelah perubahan, jalankan `php artisan route:clear`.

**Checklist:** [ ] daftar hanya membuat customer [ ] login mengarahkan ke dashboard sesuai role [ ] middleware role terdaftar [ ] route admin, petugas, customer diberi middleware yang sesuai [ ] `route:list` tidak error.
