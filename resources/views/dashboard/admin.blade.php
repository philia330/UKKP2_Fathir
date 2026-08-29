@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Pengaduan')

@section('content')
<div class="d-flex">
    @include('partials.sidebar-admin')

    <!-- Main Content -->
    <div class="main-content flex-grow-1 p-4">
        <!-- Dashboard Content -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total User</h6>
                                <h3 class="mb-0">{{ $totalUsers }}</h3>
                            </div>
                            <div class="icon text-primary">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Customer</h6>
                                <h3 class="mb-0">{{ $totalCustomers }}</h3>
                            </div>
                            <div class="icon text-success">
                                <i class="bi bi-person-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Petugas</h6>
                                <h3 class="mb-0">{{ $totalPetugas }}</h3>
                            </div>
                            <div class="icon text-info">
                                <i class="bi bi-person-badge"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Pengaduan</h6>
                                <h3 class="mb-0">{{ $totalPengaduan }}</h3>
                            </div>
                            <div class="icon text-warning">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Selamat datang, <strong>{{ auth()->user()->nama }}</strong>! Anda login sebagai <strong>Admin</strong>.
            </div>
        </div>
    </div>
</div>
@endsection
