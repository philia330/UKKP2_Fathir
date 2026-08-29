@extends('layouts.app')

@section('title', 'Profile - Sistem Pengaduan')

@section('content')
<div class="d-flex">
    @include('partials.sidebar-admin')

    <div class="main-content flex-grow-1 p-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                    <div class="ms-3">
                        <h4 class="mb-0">{{ $user->nama }}</h4>
                        <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                    </div>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <th width="200">Nama</th>
                        <td>{{ $user->nama }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td>{{ $user->no_telp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ ucfirst($user->role) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
