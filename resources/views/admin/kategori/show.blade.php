@extends('layouts.app')

@section('title', 'Detail Kategori - Sistem Pengaduan')

@section('content')
<div class="d-flex">
    @include('partials.sidebar-admin')

    <!-- Main Content -->
    <div class="main-content flex-grow-1 p-4">
        <!-- Detail Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Detail Kategori</h5>
                <div>
                    <a href="{{ route('admin.kategori.edit', $kategori) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">ID</th>
                        <td>{{ $kategori->id }}</td>
                    </tr>
                    <tr>
                        <th>Nama Kategori</th>
                        <td>{{ $kategori->nama }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $kategori->deskripsi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Pengaduan</th>
                        <td>{{ $kategori->pengaduans->count() }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $kategori->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ $kategori->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Pengaduan List -->
        @if($kategori->pengaduans->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Daftar Pengaduan</h5>
            </div>
            <div class="card-body">
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
                            @foreach($kategori->pengaduans as $index => $pengaduan)
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
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
