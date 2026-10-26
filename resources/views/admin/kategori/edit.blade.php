{{-- Memakai layout utama (layouts/app.blade.php) sebagai kerangka halaman --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Ubah Kategori - Sistem Pengaduan' --}}
@section('title', 'Edit Kategori - Sistem Pengaduan')

{{-- Isi utama halaman yang akan dimasukkan ke @yield('content') di layout --}}
@section('content')

{{--
    d-flex = membuat sidebar dan konten sejajar ke samping (kiri-kanan).
    Contoh: hapus "d-flex" -> sidebar dan konten jadi tersusun atas-bawah.
    Contoh: ganti "d-flex flex-row-reverse" -> sidebar pindah ke sisi kanan.
--}}
<div class="d-flex">

    {{-- Memanggil menu sidebar admin dari file partials/sidebar-admin.blade.php --}}
    @include('partials.sidebar-admin')

    {{--
        Form ini digunakan untuk mengubah nama atau deskripsi kategori yang sudah ada.

        main-content = class buatan sendiri, atur lebarnya/marginnya di file CSS (cari ".main-content").
        flex-grow-1  = konten melebar mengisi sisa ruang setelah sidebar.
                       Contoh: hapus -> konten hanya selebar isinya.
        p-4          = jarak dalam (padding) ke semua sisi. Skala 0 sampai 5.
                       Contoh: p-2 (lebih rapat), p-5 (lebih lega), px-4 (hanya kiri-kanan), py-4 (hanya atas-bawah).
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{--
            card = kotak putih dengan border dan sudut membulat.
            Contoh: tambah "shadow" -> ada bayangan. Tambah "border-0" -> tanpa garis tepi.
            Contoh: tambah "w-50" -> lebar card jadi 50%. Tambah "mx-auto" -> card di tengah.
        --}}
        <div class="card">

            {{-- card-body = area isi di dalam card (sudah punya padding bawaan) --}}
            <div class="card-body">

                {{-- Menampilkan semua pesan error validasi dari controller (jika ada) --}}
                @if ($errors->any())
                    {{--
                        alert alert-danger = kotak peringatan berwarna merah.
                        Contoh ganti warna: alert-warning (kuning), alert-info (biru), alert-success (hijau).
                    --}}
                    <div class="alert alert-danger">
                        {{--
                            mb-0 = margin bawah 0 (menghilangkan jarak bawah daftar).
                            ps-3 = padding kiri (start) 3, jarak titik list dari tepi kiri.
                            Contoh: ps-5 -> daftar lebih menjorok ke kanan. mb-3 -> ada jarak bawah.
                            Contoh: list-unstyled -> menghilangkan titik/bullet.
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
                    Form kirim data ke route update kategori.
                    action  = tujuan pengiriman data (route admin.kategori.update + data $kategori).
                    method  = POST, karena HTML form tidak mendukung PUT langsung.
                --}}
                <form action="{{ route('admin.kategori.update', $kategori) }}" method="POST">
                    {{-- Token keamanan Laravel, wajib ada agar form tidak ditolak (error 419) --}}
                    @csrf
                    {{-- Mengubah method POST menjadi PUT, sesuai aturan route update --}}
                    @method('PUT')

                    {{--
                        mb-3 = margin bawah (jarak ke elemen di bawahnya). Skala 0 sampai 5.
                        Contoh: mb-1 (rapat), mb-5 (renggang). mt-3 -> jarak di atas.
                    --}}
                    <div class="mb-3">
                        {{--
                            form-label = gaya teks label di atas input.
                            text-danger = warna merah untuk tanda bintang wajib diisi.
                            Contoh ganti warna: text-primary (biru), text-success (hijau), text-muted (abu-abu).
                            Contoh: fw-bold -> label jadi tebal. fs-5 -> ukuran huruf lebih besar.
                        --}}
                        <label for="nama" class="form-label">Nama Kategori <span class="text-danger">*</span></label>

                        {{--
                            Input teks untuk nama kategori.
                            form-control = input selebar penuh dengan gaya Bootstrap.
                            Contoh: form-control-lg -> input lebih besar. form-control-sm -> lebih kecil.
                            is-invalid (muncul otomatis lewat @error) = border input jadi merah saat validasi gagal.
                            value = isi awal. old() menjaga input lama saat validasi gagal,
                                    kalau belum ada diisi dari data database ($kategori->nama).
                            required = browser menolak submit jika kosong.
                            placeholder = teks petunjuk abu-abu. Contoh: ubah jadi "Contoh: Jalan Rusak".
                        --}}
                        <input type="text"
                               class="form-control @error('nama') is-invalid @enderror"
                               id="nama"
                               name="nama"
                               value="{{ old('nama', $kategori->nama) }}"
                               placeholder="Masukkan nama kategori"
                               required>

                        {{-- Pesan error khusus kolom nama, tampil di bawah input (warna merah otomatis) --}}
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        {{-- Label untuk kolom deskripsi (tanpa tanda wajib karena opsional) --}}
                        <label for="deskripsi" class="form-label">Deskripsi</label>

                        {{--
                            Textarea untuk deskripsi kategori.
                            rows="3" = tinggi kotak setara 3 baris teks.
                            Contoh: rows="6" -> kotak lebih tinggi. rows="1" -> lebih pendek.
                            Isi awal diambil dari old() atau database ($kategori->deskripsi).
                            Contoh: tambah style="resize: none;" -> user tidak bisa menarik ukuran kotak.
                        --}}
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                  id="deskripsi"
                                  name="deskripsi"
                                  rows="3"
                                  placeholder="Masukkan deskripsi kategori (opsional)">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>

                        {{-- Pesan error khusus kolom deskripsi --}}
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{--
                        Wadah dua tombol (Update dan Kembali).
                        d-flex = tombol berjajar ke samping.
                        gap-2  = jarak antar tombol. Contoh: gap-4 -> lebih renggang, gap-0 -> menempel.
                        Contoh: tambah "justify-content-end" -> tombol pindah ke kanan.
                        Contoh: tambah "justify-content-center" -> tombol di tengah.
                        Contoh: ganti "d-grid" -> tombol jadi selebar penuh dan bertumpuk.
                    --}}
                    <div class="d-flex gap-2">

                        {{--
                            Tombol simpan perubahan.
                            btn btn-primary = tombol berwarna biru.
                            Contoh ganti warna: btn-success (hijau), btn-warning (kuning), btn-danger (merah).
                            Contoh: btn-outline-primary -> hanya garis tepi biru, tanpa isi warna.
                            Contoh ukuran: tambah btn-lg (besar) atau btn-sm (kecil).
                            type="submit" = menekan tombol ini mengirim form.
                        --}}
                        <button type="submit" class="btn btn-primary">
                            {{--
                                bi bi-check-circle = ikon centang dari Bootstrap Icons.
                                me-1 = margin kanan (end) 1, jarak ikon ke teks "Update".
                                Contoh: me-3 -> jarak lebih jauh. ms-1 -> jarak di sisi kiri ikon.
                                Contoh ganti ikon: bi-save, bi-pencil-square.
                            --}}
                            <i class="bi bi-check-circle me-1"></i>Update
                        </button>

                        {{--
                            Tombol kembali ke halaman daftar kategori (tanpa menyimpan).
                            btn-secondary = abu-abu. Contoh: btn-light (putih), btn-dark (hitam).
                        --}}
                        <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">
                            {{-- Ikon panah kiri, me-1 = jarak kanan ikon ke teks --}}
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection