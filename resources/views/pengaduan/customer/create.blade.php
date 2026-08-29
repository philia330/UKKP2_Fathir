@extends('layouts.app')

@section('title', 'Buat Pengaduan - Sistem Pengaduan')

@section('content')
<div class="d-flex">
    @include('partials.sidebar-customer')

    <div class="main-content flex-grow-1 p-4">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('customer.pengaduan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pengaduan" class="form-label">Isi Pengaduan <span class="text-danger">*</span></label>
                        <textarea name="pengaduan" id="pengaduan" rows="5" class="form-control @error('pengaduan') is-invalid @enderror" placeholder="Jelaskan pengaduan Anda secara lengkap..." required>{{ old('pengaduan') }}</textarea>
                        @error('pengaduan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="foto" class="form-label">Upload Foto Bukti (opsional)</label>
                        <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                        <div class="form-text">Format gambar (jpg, png, gif), maksimal 2MB.</div>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Kirim Pengaduan
                    </button>
                    <a href="{{ route('customer.pengaduan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
