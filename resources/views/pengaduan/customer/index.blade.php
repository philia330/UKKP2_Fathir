@extends('layouts.app')

@section('title', 'Pengaduan Saya - Sistem Pengaduan')

@section('content')
<div class="d-flex">
    @include('partials.sidebar-customer')

    <div class="main-content flex-grow-1 p-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Daftar Pengaduan</h5>
                    <a href="{{ route('customer.pengaduan.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Buat Pengaduan
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Pengaduan</th>
                                <th>Foto</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengaduans as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($pengaduans->currentPage() - 1) * $pengaduans->perPage() }}</td>
                                    <td>
                                        @if($item->kategori)
                                            <span class="badge bg-secondary">{{ $item->kategori->nama }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($item->pengaduan, 60) }}</td>
                                    <td>
                                        @if ($item->foto)
                                            <a href="{{ asset('storage/' . $item->foto) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto pengaduan" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badge = match($item->status) {
                                                'menunggu' => 'bg-warning',
                                                'diproses' => 'bg-info',
                                                'selesai' => 'bg-success',
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ ucfirst($item->status) }}</span>
                                    </td>
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada pengaduan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $pengaduans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
