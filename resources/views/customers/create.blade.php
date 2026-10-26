{{-- Memakai layout utama (layouts/app.blade.php) sebagai kerangka halaman --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Registrasi Customer - Sistem Pengaduan' --}}
@section('title', 'Tambah Customer - Sistem Pengaduan')

{{-- Isi utama halaman yang dimasukkan ke @yield('content') di layout --}}
@section('content')

{{--
    d-flex = sidebar dan konten sejajar ke samping (kiri-kanan).
    Contoh: hapus "d-flex" -> sidebar dan konten jadi tersusun atas-bawah.
    Contoh: "d-flex flex-row-reverse" -> sidebar pindah ke sisi kanan.
--}}
<div class="d-flex">

    {{-- Memanggil menu sidebar PETUGAS dari partials/sidebar-petugas.blade.php (beda dengan sidebar admin) --}}
    @include('partials.sidebar-petugas')

    {{--
        Form ini dipakai petugas untuk menambahkan data customer baru ke sistem.

        main-content = class buatan sendiri, atur lebar/margin di file CSS (cari ".main-content").
        flex-grow-1  = konten melebar mengisi sisa ruang setelah sidebar.
                       Contoh: hapus -> konten hanya selebar isinya.
        p-4          = jarak dalam (padding) ke semua sisi. Skala 0 sampai 5.
                       Contoh: p-2 (lebih rapat), p-5 (lebih lega), px-4 (kiri-kanan saja), py-4 (atas-bawah saja).
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{--
            card = kotak putih dengan border dan sudut membulat.
            Contoh: tambah "shadow" -> ada bayangan. Tambah "border-0" -> tanpa garis tepi.
            Contoh: tambah "mx-auto" + "w-50" -> card selebar 50% dan berada di tengah.
        --}}
        <div class="card">

            {{-- card-body = area isi card (sudah punya padding bawaan) --}}
            <div class="card-body">

                {{-- Menampilkan semua pesan error validasi dari controller (jika ada) --}}
                @if ($errors->any())
                    {{--
                        alert alert-danger = kotak peringatan merah.
                        Contoh ganti warna: alert-warning (kuning), alert-info (biru).
                    --}}
                    <div class="alert alert-danger">
                        {{--
                            mb-0 = margin bawah 0 (hilangkan jarak bawah list).
                            ps-3 = padding kiri (start) 3, jarak bullet dari tepi kiri.
                            Contoh: ps-5 -> lebih menjorok ke kanan. list-unstyled -> tanpa bullet.
                        --}}
                        <ul class="mb-0 ps-3">
                            {{-- Mengulang setiap pesan error menjadi satu item list --}}
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{--
                    Form tambah customer. Data dikirim ke route petugas.customers.store dengan method POST.
                    action = tujuan pengiriman data (diatur di routes/web.php).
                --}}
                <form action="{{ route('petugas.customers.store') }}" method="POST">
                    {{-- Token keamanan Laravel, wajib ada agar form tidak ditolak (error 419) --}}
                    @csrf

                    {{-- ===== INPUT NAMA ===== --}}
                    {{--
                        mb-3 = margin bawah 3, jarak ke input di bawahnya. Skala 0 sampai 5.
                        Contoh: mb-1 (rapat), mb-5 (renggang). mt-3 -> jarak di atas.
                    --}}
                    <div class="mb-3">
                        {{--
                            form-label = gaya teks label di atas input.
                            text-danger = warna merah untuk tanda bintang (wajib diisi).
                            Contoh ganti warna: text-primary (biru), text-muted (abu-abu).
                            Contoh: fw-bold -> label jadi tebal. fs-5 -> huruf lebih besar.
                        --}}
                        <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>

                        {{--
                            form-control = input selebar penuh dengan gaya Bootstrap.
                            Contoh: form-control-lg -> input lebih besar, form-control-sm -> lebih kecil.
                            is-invalid (muncul otomatis lewat @error) = border merah saat input bermasalah.
                            value="{{ old('nama') }}" = mengisi ulang data lama saat validasi gagal,
                                                        supaya user tidak mengetik ulang.
                            required = browser menolak submit kalau kosong.
                            Contoh: tambah placeholder="Contoh: Budi Santoso" -> teks petunjuk abu-abu.
                        --}}
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>

                        {{--
                            Pesan error khusus kolom nama, tampil tepat di bawah input (merah otomatis).
                            invalid-feedback hanya terlihat kalau input di atasnya punya class is-invalid.
                        --}}
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- ===== INPUT EMAIL ===== --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>

                        {{-- type="email" = browser mengecek format email (harus ada tanda @) --}}
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- ===== INPUT NOMOR TELEPON (opsional) ===== --}}
                    <div class="mb-3">
                        {{-- Tidak ada tanda bintang karena kolom ini opsional --}}
                        <label for="no_telp" class="form-label">No. Telepon</label>

                        {{--
                            Tidak ada atribut "required" karena opsional.
                            Contoh: tambah "required" -> nomor telepon jadi wajib (jangan lupa tambah bintang di label).
                            Contoh: ganti type="tel" -> keyboard HP menampilkan angka.
                        --}}
                        <input type="text" name="no_telp" id="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp') }}">
                        @error('no_telp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- ===== INPUT PASSWORD ===== --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>

                        {{--
                            type="password" = karakter yang diketik disamarkan jadi titik.
                            Tidak ada value/old() karena password sengaja tidak diisi ulang demi keamanan.
                        --}}
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- ===== INPUT KONFIRMASI PASSWORD ===== --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>

                        {{--
                            name="password_confirmation" = nama ini WAJIB persis begitu supaya
                            aturan "confirmed" di validasi Laravel bisa mencocokkan dengan field "password".
                            Tidak ada @error di sini karena error kecocokan password muncul di field "password".
                        --}}
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>

                    {{--
                        Tombol simpan.
                        btn-primary = biru. Contoh ganti warna: btn-success (hijau), btn-dark (hitam).
                        Contoh: btn-outline-primary -> hanya garis tepi biru tanpa isi warna.
                        Contoh ukuran: tambah btn-lg (besar) atau btn-sm (kecil).
                        type="submit" = menekan tombol ini mengirim form.
                    --}}
                    <button type="submit" class="btn btn-primary">
                        {{--
                            bi-save = ikon disket.
                            me-1 = margin kanan (end) 1, jarak ikon ke teks.
                            Contoh: me-3 -> jarak lebih jauh. Contoh ganti ikon: bi-check-circle, bi-person-plus.
                        --}}
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>

                    {{-- Tombol kembali ke daftar customer (tanpa menyimpan). btn-secondary = abu-abu. Contoh: btn-light (putih) --}}
                    <a href="{{ route('petugas.customers.index') }}" class="btn btn-secondary">
                        {{-- Ikon panah kiri --}}
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection