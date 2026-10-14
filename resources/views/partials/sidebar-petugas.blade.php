<nav class="sidebar col-md-2 p-3">
    <!-- Sidebar ini hanya ditampilkan untuk role petugas. Fungsi utamanya adalah membantu petugas menangani laporan dan data customer. -->
    <h5 class="mb-4">
        <i class="bi bi-person-badge me-2"></i>Petugas Panel
    </h5>
    <ul class="nav flex-column">
        <li class="nav-item">
            <!-- Dashboard petugas menampilkan ringkasan pengaduan yang perlu ditangani. -->
            <a class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}" href="{{ route('petugas.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <!-- Petugas dapat menambah atau mengelola data customer yang masuk ke sistem. -->
            <a class="nav-link {{ request()->routeIs('petugas.customers.*') ? 'active' : '' }}" href="{{ route('petugas.customers.index') }}">
                <i class="bi bi-person-plus me-2"></i>Tambah Customer
            </a>
        </li>
        <li class="nav-item">
            <!-- Menu ini menampilkan seluruh pengaduan yang harus dipantau dan diperbarui statusnya. -->
            <a class="nav-link {{ request()->routeIs('petugas.pengaduan.*') ? 'active' : '' }}" href="{{ route('petugas.pengaduan.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i>Pengaduan
            </a>
        </li>
        <li class="nav-item">
            <!-- Profil petugas menampilkan data akun yang sedang login. -->
            <a class="nav-link {{ request()->routeIs('petugas.profile') ? 'active' : '' }}" href="{{ route('petugas.profile') }}">
                <i class="bi bi-person-circle me-2"></i>Profile
            </a>
        </li>
        <li class="nav-item mt-3">
            <!-- Logout pakai POST agar aman dan tidak bisa diakses lewat link biasa. -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </li>
    </ul>
</nav>
