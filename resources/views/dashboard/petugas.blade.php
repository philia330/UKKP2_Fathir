@extends('layouts.app')

@section('title', 'Dashboard Petugas - Sistem Pengaduan')

@section('content')
<div class="d-flex">
    @include('partials.sidebar-petugas')

    <!-- Konten utama dashboard petugas. Di sini petugas melihat laporan yang masuk dan statusnya. -->
    <div class="main-content flex-grow-1 p-4">
        <!-- Kartu statistik ini membantu petugas memantau apakah laporan masih menunggu, sedang dikerjakan, atau sudah selesai. -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Pengaduan</h6>
                                <h3 class="mb-0">{{ $totalPengaduan }}</h3>
                            </div>
                            <div class="icon text-primary">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Menunggu</h6>
                                <h3 class="mb-0">{{ $menunggu }}</h3>
                            </div>
                            <div class="icon text-warning">
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Selesai</h6>
                                <h3 class="mb-0">{{ $selesai }}</h3>
                            </div>
                            <div class="icon text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar pengaduan terbaru agar petugas bisa melihat laporan yang baru masuk. -->
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pengaduan Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($recentPengaduan->isEmpty())
                        <p class="text-muted text-center mb-0">Belum ada pengaduan.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Customer</th>
                                        <th>Pengaduan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPengaduan as $index => $pengaduan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $pengaduan->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $pengaduan->user->nama }}</td>
                                        <td>{{ Str::limit($pengaduan->pengaduan, 50) }}</td>
                                        <td>
                                            @if($pengaduan->status == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($pengaduan->status == 'diproses')
                                                <span class="badge bg-info">Diproses</span>
                                            @else
                                                <span class="badge bg-success">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Selamat datang, <strong>{{ auth()->user()->nama }}</strong>! Anda login sebagai <strong>Petugas</strong>.
            </div>
        </div>
    </div>
</div>
@endsection
