# 4. Dashboard, CRUD user/customer, dan profile

## Tahap 12 — Controller dashboard tiga role

**Tujuan:** setiap role melihat ringkasan data yang sesuai.  
**Persiapan:** migration dan model tersedia.  
**Terminal:** CMD, root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller DashboardController
```

Isi lengkap **`app/Http/Controllers/DashboardController.php`**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Ringkasan seluruh akun dan laporan untuk admin.
    public function admin()
    {
        return view('dashboard.admin', [
            'totalUsers' => User::count(),
            'totalCustomers' => User::where('role', 'customer')->count(),
            'totalPetugas' => User::where('role', 'petugas')->count(),
            'totalPengaduan' => Pengaduan::count(),
        ]);
    }

    // Statistik dan laporan terbaru untuk petugas.
    public function petugas()
    {
        return view('dashboard.petugas', [
            'totalPengaduan' => Pengaduan::count(),
            'menunggu' => Pengaduan::where('status', 'menunggu')->count(),
            'diproses' => Pengaduan::where('status', 'diproses')->count(),
            'selesai' => Pengaduan::where('status', 'selesai')->count(),
            'recentPengaduan' => Pengaduan::with(['user', 'kategori'])->latest()->take(5)->get(),
        ]);
    }

    // Query customer selalu difilter dengan ID akun yang sedang login.
    public function customer()
    {
        $query = Pengaduan::where('user_id', Auth::id());

        return view('dashboard.customer', [
            'totalPengaduan' => (clone $query)->count(),
            'menunggu' => (clone $query)->where('status', 'menunggu')->count(),
            'diproses' => (clone $query)->where('status', 'diproses')->count(),
            'selesai' => (clone $query)->where('status', 'selesai')->count(),
            'recentPengaduan' => (clone $query)->with('kategori')->latest()->take(5)->get(),
        ]);
    }
}
```

## Tahap 13 — Admin CRUD user

**Tujuan:** admin dapat melihat, menambah, mengubah, dan menghapus user dengan validasi.  
**Persiapan:** route resource admin sudah ditulis.  
**Terminal:** CMD, root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller Admin/UserController --resource
```

Isi lengkap **`app/Http/Controllers/Admin/UserController.php`**:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::latest()->paginate(10)]);
    }

    public function create()
    {
        return view('users.create', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'petugas', 'customer'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'petugas', 'customer'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Admin tidak boleh menurunkan role akunnya sendiri dan kehilangan akses.
        if ($user->id === Auth::id() && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Role akun yang sedang digunakan tidak dapat diturunkan.'])->withInput();
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'Akun yang sedang digunakan tidak dapat dihapus.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
```

## Tahap 14 — Petugas melihat dan menambahkan customer

**Tujuan:** petugas bisa membantu mendaftarkan customer tanpa memilih role sendiri.  
**Persiapan:** route `/petugas/customers` ada.  
**Terminal:** CMD, root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller Petugas/CustomerController
```

Isi lengkap **`app/Http/Controllers/Petugas/CustomerController.php`**:

```php
<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')->latest()->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'no_telp' => $data['no_telp'] ?? null,
            'role' => 'customer',
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('petugas.customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }
}
```

## Tahap 15 — Profile hanya milik akun yang login

**Tujuan:** menampilkan dan memperbarui profil akun yang sedang digunakan, tanpa menerima ID user dari URL.  
**Persiapan:** setiap group role sudah memiliki route `/profile` dan `PUT /profile`.  
**Terminal:** CMD, root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller ProfileController
```

Isi lengkap **`app/Http/Controllers/ProfileController.php`**:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
```

## Uji controller

Simpan semua file. Dari CMD root project jalankan:

```bat
cd /d D:\AplikasiPengaduan
php artisan route:list
```

Pastikan seluruh action yang disebut route tersedia: `admin`, `petugas`, `customer`, `index`, `create`, `store`, `edit`, `update`, `destroy`, `show`, dan `updateStatus`. Tampilan view akan dibuat pada bab 6.

**Error umum:** `View [users.index] not found` baru akan hilang setelah view di bab 6 dibuat; saat ini validasi route dapat dilanjutkan. `MassAssignmentException` berarti kolom belum masuk `$fillable`; `Unknown column` berarti migration dan controller berbeda nama.  
**Checklist:** [ ] dashboard customer hanya menghitung laporan miliknya [ ] admin dapat CRUD akun [ ] petugas selalu membuat role customer [ ] password opsional saat edit user [ ] profile mengambil `Auth::user()` dan tidak menerima ID URL.
