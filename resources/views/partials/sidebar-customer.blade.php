<nav class="sidebar col-md-2 p-3">
    <h5 class="mb-4">
        <i class="bi bi-person me-2"></i>Customer Panel
    </h5>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.pengaduan.index') ? 'active' : '' }}" href="{{ route('customer.pengaduan.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i>Pengaduan Saya
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.pengaduan.create') ? 'active' : '' }}" href="{{ route('customer.pengaduan.create') }}">
                <i class="bi bi-plus-circle me-2"></i>Buat Pengaduan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}">
                <i class="bi bi-person-circle me-2"></i>Profile
            </a>
        </li>
        <li class="nav-item mt-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </li>
    </ul>
</nav>
