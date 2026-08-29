@extends('layouts.app')

@section('title', 'Data Pengaduan - Sistem Pengaduan')

@section('content')
<div class="d-flex">
    @include('partials.sidebar-admin')

    <div class="main-content flex-grow-1 p-4">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Semua Pengaduan</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Customer</th>
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
                                    <td>{{ $item->user->nama }}</td>
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
                                <tr><td colspan="7" class="text-center text-muted">Belum ada pengaduan.</td></tr>
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
