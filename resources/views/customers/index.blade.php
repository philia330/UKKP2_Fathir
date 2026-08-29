@extends('layouts.app')

@section('title', 'Data Customer - Sistem Pengaduan')

@section('content')
<div class="d-flex">
    @include('partials.sidebar-petugas')

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
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Daftar Customer</h5>
                    <a href="{{ route('petugas.customers.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Customer
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                                    <td>{{ $customer->nama }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->no_telp ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data customer.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
