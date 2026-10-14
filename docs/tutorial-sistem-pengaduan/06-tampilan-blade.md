# 6. Tampilan Blade, Bootstrap 5, CSS, dan ikon

## Tahap 20 — Membuat layout utama dan navigasi

**Tujuan:** menyamakan tampilan, menu berdasarkan role, pesan, Bootstrap 5, CSS sederhana, dan ikon.  
**Persiapan:** route dan controller dari bab 3–5 sudah ditulis.  
**Terminal:** VS Code untuk membuat file; CMD root hanya jika menjalankan Artisan.

Buat folder/file persis sesuai path di bawah. Semua path dihitung dari `D:\AplikasiPengaduan`.

**File `resources/views/layouts/app.blade.php`:**

```blade
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengaduan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; min-height: 100vh; }
        .navbar-brand { font-weight: 700; }
        .card { border: 0; box-shadow: 0 .15rem .6rem rgba(20, 40, 80, .08); }
        .stat-card { border-left: .3rem solid #0d6efd; }
        .page-title { font-weight: 700; }
        footer { color: #6c757d; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ auth()->check() ? route(auth()->user()->role.'.dashboard') : route('login') }}">
                <i class="bi bi-megaphone-fill me-2"></i>Sistem Pengaduan
            </a>
            @auth
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama" aria-controls="menuUtama" aria-expanded="false" aria-label="Buka menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="menuUtama">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route(auth()->user()->role.'.dashboard') }}">Dashboard</a>
                        </li>
                        @if(auth()->user()->role === 'admin')
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">User</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.kategori.index') }}">Kategori</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pengaduan.index') }}">Pengaduan</a></li>
                        @elseif(auth()->user()->role === 'petugas')
                            <li class="nav-item"><a class="nav-link" href="{{ route('petugas.customers.index') }}">Customer</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('petugas.pengaduan.index') }}">Pengaduan</a></li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{ route('customer.pengaduan.index') }}">Pengaduan Saya</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('customer.pengaduan.create') }}">Buat Pengaduan</a></li>
                        @endif
                    </ul>
                    <div class="d-flex align-items-center gap-3 text-white">
                        <a class="nav-link text-white" href="{{ route(auth()->user()->role.'.profile') }}">
                            {{ auth()->user()->nama }} ({{ ucfirst(auth()->user()->role) }})
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-light" type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Periksa kembali data berikut:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="container py-3 text-center small">Tutorial Sistem Pengaduan Laravel 12</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

Layout memuat CSS Bootstrap, Bootstrap Icons, CSS custom minimal, menu sesuai role, token CSRF, pesan sukses, dan pesan validasi. Semua halaman selanjutnya memakai layout ini.

## Tahap 21 — Halaman register dan login

**Tujuan:** menyediakan form autentikasi dengan nama field sama dengan validasi controller.  
**Persiapan:** `AuthController` dan route bernama `login`/`register` tersedia.

**File `resources/views/auth/login.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="h3 page-title text-center mb-4"><i class="bi bi-shield-lock me-2"></i>Login</h1>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" name="password" type="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i>Masuk</button>
                </form>
                <p class="text-center mt-3 mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai customer</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
```

**File `resources/views/auth/register.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Daftar Customer')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="h3 page-title text-center mb-4"><i class="bi bi-person-plus me-2"></i>Daftar Customer</h1>
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama lengkap</label>
                        <input id="nama" name="nama" class="form-control" value="{{ old('nama') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_telp" class="form-label">Nomor telepon (opsional)</label>
                        <input id="no_telp" name="no_telp" class="form-control" value="{{ old('no_telp') }}">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (minimal 8 karakter)</label>
                        <input id="password" name="password" type="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Ulangi password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Buat Akun</button>
                </form>
                <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
```

## Tahap 22 — Tiga dashboard

**Tujuan:** menampilkan statistik yang sesuai dengan role; laporan customer tetap difilter di controller.  
**Persiapan:** `DashboardController` telah dibuat.

**File `resources/views/dashboard/admin.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<h1 class="h3 page-title mb-4">Dashboard Admin</h1>
<div class="row g-3">
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted">Total User</div><div class="fs-2">{{ $totalUsers }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted">Customer</div><div class="fs-2">{{ $totalCustomers }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted">Petugas</div><div class="fs-2">{{ $totalPetugas }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted">Pengaduan</div><div class="fs-2">{{ $totalPengaduan }}</div></div></div></div>
</div>
<div class="alert alert-info mt-4"><i class="bi bi-info-circle me-2"></i>Selamat datang, {{ auth()->user()->nama }}. Gunakan menu untuk mengelola user, kategori, dan laporan.</div>
@endsection
```

**File `resources/views/dashboard/petugas.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
<h1 class="h3 page-title mb-4">Dashboard Petugas</h1>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card"><div class="card-body">Semua laporan<div class="fs-2">{{ $totalPengaduan }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body">Menunggu<div class="fs-2">{{ $menunggu }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body">Diproses<div class="fs-2">{{ $diproses }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body">Selesai<div class="fs-2">{{ $selesai }}</div></div></div></div>
</div>
<div class="card">
    <div class="card-header bg-white fw-bold"><i class="bi bi-clock-history me-2"></i>Lima Pengaduan Terbaru</div>
    <div class="card-body table-responsive">
        @if($recentPengaduan->isEmpty())
            <p class="text-muted mb-0">Belum ada pengaduan.</p>
        @else
            <table class="table table-striped align-middle">
                <thead><tr><th>Tanggal</th><th>Customer</th><th>Kategori</th><th>Isi</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($recentPengaduan as $pengaduan)
                        <tr>
                            <td>{{ $pengaduan->created_at->format('d-m-Y') }}</td>
                            <td>{{ $pengaduan->user->nama }}</td>
                            <td>{{ $pengaduan->kategori?->nama ?? 'Kategori dihapus' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($pengaduan->pengaduan, 60) }}</td>
                            <td><span class="badge text-bg-secondary">{{ ucfirst($pengaduan->status) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
```

**File `resources/views/dashboard/customer.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Dashboard Customer')

@section('content')
<h1 class="h3 page-title mb-4">Dashboard Customer</h1>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card"><div class="card-body">Pengaduan Saya<div class="fs-2">{{ $totalPengaduan }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body">Menunggu<div class="fs-2">{{ $menunggu }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body">Diproses<div class="fs-2">{{ $diproses }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body">Selesai<div class="fs-2">{{ $selesai }}</div></div></div></div>
</div>
<a class="btn btn-primary mb-4" href="{{ route('customer.pengaduan.create') }}"><i class="bi bi-plus-circle me-1"></i>Buat Pengaduan</a>
<div class="card">
    <div class="card-header bg-white fw-bold">Laporan Terbaru Saya</div>
    <div class="card-body table-responsive">
        @if($recentPengaduan->isEmpty())
            <p class="text-muted mb-0">Anda belum mengirim pengaduan.</p>
        @else
            <table class="table table-striped">
                <thead><tr><th>Tanggal</th><th>Kategori</th><th>Isi</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($recentPengaduan as $pengaduan)
                        <tr>
                            <td>{{ $pengaduan->created_at->format('d-m-Y') }}</td>
                            <td>{{ $pengaduan->kategori?->nama ?? 'Kategori dihapus' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($pengaduan->pengaduan, 70) }}</td>
                            <td>{{ ucfirst($pengaduan->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
```

## Tahap 23 — Halaman user dan kategori

**Tujuan:** menyediakan halaman baca, tambah, edit, dan hapus dengan nama route resource yang sesuai.  
**Persiapan:** controller admin sudah tersedia.

**File `resources/views/users/index.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Data User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">Data User</h1>
    <a class="btn btn-primary" href="{{ route('admin.users.create') }}">Tambah User</a>
</div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle">
        <thead><tr><th>Nama</th><th>Email</th><th>No. Telepon</th><th>Role</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->nama }}</td><td>{{ $user->email }}</td><td>{{ $user->no_telp ?? '-' }}</td><td>{{ ucfirst($user->role) }}</td>
                <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-warning" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus akun ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center">Belum ada user.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</div></div>
@endsection
```

**File `resources/views/users/create.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<h1 class="h3 page-title mb-3">Tambah User</h1>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="mb-3"><label for="nama" class="form-label">Nama</label><input id="nama" name="nama" class="form-control" value="{{ old('nama') }}" required></div>
        <div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required></div>
        <div class="mb-3"><label for="no_telp" class="form-label">Nomor telepon</label><input id="no_telp" name="no_telp" class="form-control" value="{{ old('no_telp') }}"></div>
        <div class="mb-3"><label for="role" class="form-label">Role</label><select id="role" name="role" class="form-select" required><option value="">Pilih role</option>@foreach(['admin', 'petugas', 'customer'] as $role)<option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
        <div class="mb-3"><label for="password" class="form-label">Password minimal 8 karakter</label><input id="password" name="password" type="password" class="form-control" required></div>
        <div class="mb-3"><label for="password_confirmation" class="form-label">Ulangi password</label><input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required></div>
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Kembali</a>
    </form>
</div></div>
@endsection
```

**File `resources/views/users/edit.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<h1 class="h3 page-title mb-3">Edit User</h1>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3"><label for="nama" class="form-label">Nama</label><input id="nama" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required></div>
        <div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
        <div class="mb-3"><label for="no_telp" class="form-label">Nomor telepon</label><input id="no_telp" name="no_telp" class="form-control" value="{{ old('no_telp', $user->no_telp) }}"></div>
        <div class="mb-3"><label for="role" class="form-label">Role</label><select id="role" name="role" class="form-select" required>@foreach(['admin', 'petugas', 'customer'] as $role)<option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
        <div class="mb-3"><label for="password" class="form-label">Password baru (kosongkan untuk tetap memakai password lama)</label><input id="password" name="password" type="password" class="form-control"></div>
        <div class="mb-3"><label for="password_confirmation" class="form-label">Ulangi password baru</label><input id="password_confirmation" name="password_confirmation" type="password" class="form-control"></div>
        <button class="btn btn-primary" type="submit">Perbarui</button>
        <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Kembali</a>
    </form>
</div></div>
@endsection
```

**File `resources/views/kategori/index.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">Kategori Pengaduan</h1>
    <a class="btn btn-primary" href="{{ route('admin.kategori.create') }}">Tambah Kategori</a>
</div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle">
        <thead><tr><th>Nama</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($kategoris as $kategori)
            <tr>
                <td>{{ $kategori->nama }}</td><td>{{ $kategori->deskripsi ?? '-' }}</td>
                <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-warning" href="{{ route('admin.kategori.edit', $kategori) }}">Edit</a>
                    <form action="{{ route('admin.kategori.destroy', $kategori) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-center">Belum ada kategori. Tambahkan kategori sebelum membuat pengaduan.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $kategoris->links() }}
</div></div>
@endsection
```

**File `resources/views/kategori/create.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<h1 class="h3 page-title mb-3">Tambah Kategori</h1>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.kategori.store') }}" method="POST">
        @csrf
        <div class="mb-3"><label for="nama" class="form-label">Nama kategori</label><input id="nama" name="nama" class="form-control" value="{{ old('nama') }}" required></div>
        <div class="mb-3"><label for="deskripsi" class="form-label">Deskripsi</label><textarea id="deskripsi" name="deskripsi" class="form-control" rows="4">{{ old('deskripsi') }}</textarea></div>
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('admin.kategori.index') }}">Kembali</a>
    </form>
</div></div>
@endsection
```

**File `resources/views/kategori/edit.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<h1 class="h3 page-title mb-3">Edit Kategori</h1>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.kategori.update', $kategori) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3"><label for="nama" class="form-label">Nama kategori</label><input id="nama" name="nama" class="form-control" value="{{ old('nama', $kategori->nama) }}" required></div>
        <div class="mb-3"><label for="deskripsi" class="form-label">Deskripsi</label><textarea id="deskripsi" name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $kategori->deskripsi) }}</textarea></div>
        <button class="btn btn-primary" type="submit">Perbarui</button>
        <a class="btn btn-secondary" href="{{ route('admin.kategori.index') }}">Kembali</a>
    </form>
</div></div>
@endsection
```

## Tahap 24 — Customer dan profil

**Tujuan:** melengkapi halaman data customer dan halaman ubah profil.  
**Persiapan:** controller customer dan profile sudah dibuat.

**File `resources/views/customers/index.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Data Customer')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">Data Customer</h1>
    <a class="btn btn-primary" href="{{ route('petugas.customers.create') }}">Tambah Customer</a>
</div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped">
        <thead><tr><th>Nama</th><th>Email</th><th>Nomor telepon</th></tr></thead>
        <tbody>
        @forelse($customers as $customer)
            <tr><td>{{ $customer->nama }}</td><td>{{ $customer->email }}</td><td>{{ $customer->no_telp ?? '-' }}</td></tr>
        @empty
            <tr><td colspan="3" class="text-center">Belum ada customer.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $customers->links() }}
</div></div>
@endsection
```

**File `resources/views/customers/create.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Tambah Customer')

@section('content')
<h1 class="h3 page-title mb-3">Tambah Customer</h1>
<div class="card"><div class="card-body">
    <form action="{{ route('petugas.customers.store') }}" method="POST">
        @csrf
        <div class="mb-3"><label for="nama" class="form-label">Nama</label><input id="nama" name="nama" class="form-control" value="{{ old('nama') }}" required></div>
        <div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required></div>
        <div class="mb-3"><label for="no_telp" class="form-label">Nomor telepon</label><input id="no_telp" name="no_telp" class="form-control" value="{{ old('no_telp') }}"></div>
        <div class="mb-3"><label for="password" class="form-label">Password minimal 8 karakter</label><input id="password" name="password" type="password" class="form-control" required></div>
        <div class="mb-3"><label for="password_confirmation" class="form-label">Ulangi password</label><input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required></div>
        <button class="btn btn-primary" type="submit">Simpan Customer</button>
        <a class="btn btn-secondary" href="{{ route('petugas.customers.index') }}">Kembali</a>
    </form>
</div></div>
@endsection
```

**File `resources/views/profile/show.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<h1 class="h3 page-title mb-3">Profil Saya</h1>
<div class="card"><div class="card-body">
    <p class="text-muted">Role: <strong>{{ ucfirst($user->role) }}</strong></p>
    <form action="{{ route(auth()->user()->role.'.profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3"><label for="nama" class="form-label">Nama</label><input id="nama" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required></div>
        <div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
        <div class="mb-3"><label for="no_telp" class="form-label">Nomor telepon</label><input id="no_telp" name="no_telp" class="form-control" value="{{ old('no_telp', $user->no_telp) }}"></div>
        <button class="btn btn-primary" type="submit">Simpan Profil</button>
    </form>
</div></div>
@endsection
```

## Tahap 25 — Halaman pengaduan customer dan petugas/admin

**Tujuan:** form mengirim nama field yang divalidasi controller; petugas/admin dapat memperbarui status.  
**Persiapan:** `storage:link` sudah dibuat atau akan dibuat pada bab 5.

**File `resources/views/pengaduan/create.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Buat Pengaduan')

@section('content')
<h1 class="h3 page-title mb-3">Buat Pengaduan</h1>
<div class="card"><div class="card-body">
    <form action="{{ route('customer.pengaduan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="kategori_id" class="form-label">Kategori</label>
            <select id="kategori_id" name="kategori_id" class="form-select" required>
                <option value="">Pilih kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" @selected(old('kategori_id') == $kategori->id)>{{ $kategori->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="pengaduan" class="form-label">Isi pengaduan (minimal 10 karakter)</label>
            <textarea id="pengaduan" name="pengaduan" class="form-control" rows="6" required>{{ old('pengaduan') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Foto bukti (opsional, JPG/PNG maksimal 2 MB)</label>
            <input id="foto" name="foto" type="file" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        </div>
        <button class="btn btn-primary" type="submit"><i class="bi bi-send me-1"></i>Kirim Pengaduan</button>
        <a class="btn btn-secondary" href="{{ route('customer.pengaduan.index') }}">Kembali</a>
    </form>
</div></div>
@endsection
```

**File `resources/views/pengaduan/customer-index.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Pengaduan Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">Pengaduan Saya</h1>
    <a class="btn btn-primary" href="{{ route('customer.pengaduan.create') }}">Buat Pengaduan</a>
</div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle">
        <thead><tr><th>Tanggal</th><th>Kategori</th><th>Isi</th><th>Foto</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($pengaduans as $pengaduan)
            <tr>
                <td>{{ $pengaduan->created_at->format('d-m-Y H:i') }}</td>
                <td>{{ $pengaduan->kategori?->nama ?? 'Kategori dihapus' }}</td>
                <td>{{ $pengaduan->pengaduan }}</td>
                <td>
                    @if($pengaduan->foto)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($pengaduan->foto) }}" target="_blank" rel="noopener">Lihat foto</a>
                    @else
                        -
                    @endif
                </td>
                <td><span class="badge text-bg-secondary">{{ ucfirst($pengaduan->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center">Belum ada pengaduan.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $pengaduans->links() }}
</div></div>
@endsection
```

**File `resources/views/pengaduan/manage.blade.php`:**

```blade
@extends('layouts.app')

@section('title', 'Daftar Pengaduan')

@section('content')
<h1 class="h3 page-title mb-3">Semua Pengaduan</h1>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-striped align-middle">
        <thead><tr><th>Tanggal</th><th>Customer</th><th>Kategori</th><th>Pengaduan</th><th>Foto</th><th>Status</th><th>Ubah Status</th></tr></thead>
        <tbody>
        @forelse($pengaduans as $pengaduan)
            <tr>
                <td>{{ $pengaduan->created_at->format('d-m-Y H:i') }}</td>
                <td>{{ $pengaduan->user->nama }}</td>
                <td>{{ $pengaduan->kategori?->nama ?? 'Kategori dihapus' }}</td>
                <td>{{ $pengaduan->pengaduan }}</td>
                <td>
                    @if($pengaduan->foto)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($pengaduan->foto) }}" target="_blank" rel="noopener">Lihat foto</a>
                    @else
                        -
                    @endif
                </td>
                <td>{{ ucfirst($pengaduan->status) }}</td>
                <td>
                    <form action="{{ route($prefix.'.pengaduan.updateStatus', $pengaduan) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        @method('PUT')
                        <select name="status" class="form-select form-select-sm" aria-label="Status pengaduan">
                            @foreach(['menunggu', 'diproses', 'selesai'] as $status)
                                <option value="{{ $status }}" @selected($pengaduan->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-success" type="submit">Simpan</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center">Belum ada pengaduan.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $pengaduans->links() }}
</div></div>
@endsection
```

## Uji tampilan

Pastikan setiap file berada di subfolder yang benar. Jalankan nanti `php artisan serve` setelah seeder, lalu buka halaman dan klik tautan antarmuka. Pesan validasi dan sukses ditampilkan oleh layout.

**Error umum:** `View [users.index] not found` berarti nama folder/file tidak cocok dengan nama view pada controller; `Route [admin.users.index] not defined` berarti nama route di Blade tidak cocok dengan `routes/web.php`; halaman Bootstrap tanpa styling berarti koneksi CDN/internet bermasalah.  
**Checklist:** [ ] semua view yang dirujuk controller tersedia [ ] setiap form POST memiliki `@csrf` [ ] form update/delete memakai `@method` [ ] form foto memakai `multipart/form-data` [ ] daftar customer menggunakan paginasi [ ] pesan validasi tampil.
