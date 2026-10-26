{{-- Memakai layout utama (layouts/app.blade.php) sebagai kerangka halaman --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Lihat Kategori - Sistem Pengaduan' --}}
@section('title', 'Detail Kategori - Sistem Pengaduan')

{{-- Isi utama halaman yang dimasukkan ke @yield('content') di layout --}}
@section('content')

{{--
    d-flex = sidebar dan konten sejajar ke samping (kiri-kanan).
    Contoh: hapus "d-flex" -> sidebar dan konten jadi tersusun atas-bawah.
    Contoh: "d-flex flex-row-reverse" -> sidebar pindah ke sisi kanan.
--}}
<div class="d-flex">

    {{-- Memanggil menu sidebar admin dari partials/sidebar-admin.blade.php --}}
    @include('partials.sidebar-admin')

    {{--
        Halaman ini menampilkan detail satu kategori dan daftar pengaduan yang masuk ke kategori tersebut.

        main-content = class buatan sendiri, atur lebar/margin di file CSS (cari ".main-content").
        flex-grow-1  = konten melebar mengisi sisa ruang setelah sidebar.
                       Contoh: hapus -> konten hanya selebar isinya.
        p-4          = jarak dalam (padding) ke semua sisi. Skala 0 sampai 5.
                       Contoh: p-2 (lebih rapat), p-5 (lebih lega), px-4 (kiri-kanan saja), py-4 (atas-bawah saja).
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{--
            ===== CARD DETAIL KATEGORI =====
            card = kotak putih dengan border dan sudut membulat.
            Contoh: tambah "shadow" -> ada bayangan. Tambah "border-0" -> tanpa garis tepi.
            Contoh: tambah "mx-auto" + "w-75" -> card selebar 75% dan berada di tengah.
        --}}
        <div class="card">

            {{--
                card-header = bagian judul di atas card.
                d-flex                  = judul dan tombol berjajar ke samping.
                justify-content-between = judul di kiri, tombol di kanan (ujung ke ujung).
                                          Contoh: justify-content-start (semua ke kiri),
                                          justify-content-end (semua ke kanan), justify-content-center (tengah).
                align-items-center      = isi header rata tengah secara vertikal.
                                          Contoh: align-items-start (rata atas), align-items-end (rata bawah).
                Contoh warna: tambah "bg-primary text-white" -> header biru dengan teks putih.
            --}}
            <div class="card-header d-flex justify-content-between align-items-center">

                {{--
                    h5 = ukuran judul. Contoh: h4 (lebih besar), h6 (lebih kecil).
                    mb-0 = margin bawah 0 supaya judul rata tengah dengan tombol.
                    bi bi-tags = ikon label. me-2 = jarak kanan ikon ke teks.
                    Contoh ganti ikon: bi-folder, bi-list-ul.
                --}}
                <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Detail Kategori</h5>

                {{--
                    Div pembungkus dua tombol supaya dihitung sebagai satu kelompok di sisi kanan.
                    Contoh: tambah class "d-flex gap-2" -> jarak antar tombol lebih rapi dan seragam.
                --}}
                <div>
                    {{--
                        Tombol menuju halaman edit kategori.
                        btn-warning = kuning. Contoh ganti warna: btn-primary (biru), btn-success (hijau).
                        btn-sm = ukuran kecil. Contoh: hapus btn-sm -> normal, ganti btn-lg -> besar.
                    --}}
                    <a href="{{ route('admin.kategori.edit', $kategori) }}" class="btn btn-warning btn-sm">
                        {{-- Ikon pensil. me-1 = jarak kanan ikon ke teks. Contoh: me-2 (lebih jauh) --}}
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>

                    {{-- Tombol kembali ke daftar kategori. btn-secondary = abu-abu. Contoh: btn-dark (hitam) --}}
                    <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary btn-sm">
                        {{-- Ikon panah kiri --}}
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            {{-- card-body = area isi card (sudah punya padding bawaan) --}}
            <div class="card-body">

                {{--
                    Tabel informasi kategori (bentuk label: nilai).
                    table-borderless = tabel tanpa garis.
                    Contoh: ganti "table-bordered" -> ada garis di semua sel,
                    "table-striped" -> baris selang-seling warna, "table-sm" -> lebih rapat.
                --}}
                <table class="table table-borderless">
                    <tr>
                        {{--
                            width="200" = lebar kolom label 200 piksel, supaya semua nilai sejajar rapi.
                            Contoh: width="300" -> kolom label lebih lebar, nilai bergeser ke kanan.
                            Catatan: atribut width di HTML sudah usang, alternatifnya style="width: 200px;".
                        --}}
                        <th width="200">ID</th>
                        {{-- ID kategori dari database --}}
                        <td>{{ $kategori->id }}</td>
                    </tr>
                    <tr>
                        <th>Nama Kategori</th>
                        {{-- Nama kategori --}}
                        <td>{{ $kategori->nama }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        {{-- Deskripsi kategori. Kalau kosong (null) tampil tanda "-" --}}
                        <td>{{ $kategori->deskripsi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Pengaduan</th>
                        {{-- Jumlah pengaduan yang memakai kategori ini (lewat relasi pengaduans di model) --}}
                        <td>{{ $kategori->pengaduans->count() }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        {{--
                            Waktu data dibuat, diformat tanggal/bulan/tahun jam:menit.
                            Contoh ubah format: 'd M Y' -> 26 Okt 2026, 'Y-m-d H:i:s' -> 2026-10-26 14:30:00.
                        --}}
                        <td>{{ $kategori->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        {{-- Waktu terakhir data diubah, format sama seperti di atas --}}
                        <td>{{ $kategori->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{--
            ===== CARD DAFTAR PENGADUAN =====
            Hanya tampil kalau kategori ini punya minimal 1 pengaduan.
            Kalau kosong, card ini disembunyikan.
        --}}
        @if($kategori->pengaduans->count() > 0)

        {{--
            mt-4 = margin atas 4, jarak card ini dari card di atasnya. Skala 0 sampai 5.
            Contoh: mt-2 (lebih dekat), mt-5 (lebih jauh).
        --}}
        <div class="card mt-4">

            {{-- card-header tanpa d-flex karena hanya berisi judul (tidak ada tombol) --}}
            <div class="card-header">
                {{-- bi-file-earmark-text = ikon dokumen. me-2 = jarak kanan ikon ke teks --}}
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Daftar Pengaduan</h5>
            </div>

            <div class="card-body">
                {{--
                    table-responsive = tabel bisa digeser (scroll) ke samping di layar kecil/HP
                    supaya tidak merusak tampilan halaman.
                --}}
                <div class="table-responsive">
                    {{--
                        table-hover = baris berubah warna saat kursor diarahkan.
                        Contoh tambahan: table-striped, table-bordered, table-sm.
                    --}}
                    <table class="table table-hover">
                        {{-- Judul kolom tabel. Contoh: tambah class "table-dark" pada thead -> header hitam --}}
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
                            {{-- Perulangan semua pengaduan di kategori ini. $index dipakai untuk nomor urut --}}
                            @foreach($kategori->pengaduans as $index => $pengaduan)
                            <tr>
                                {{-- $index mulai dari 0, jadi ditambah 1 supaya nomor mulai dari 1 --}}
                                <td>{{ $index + 1 }}</td>

                                {{-- Tanggal pengaduan dibuat. Contoh ubah format: 'd M Y' -> 26 Okt 2026 --}}
                                <td>{{ $pengaduan->created_at->format('d/m/Y') }}</td>

                                {{-- Nama customer pembuat pengaduan (lewat relasi user di model Pengaduan) --}}
                                <td>{{ $pengaduan->user->nama }}</td>

                                {{--
                                    Isi pengaduan, dipotong maksimal 50 karakter lalu diberi "..." supaya tabel tidak melebar.
                                    Contoh: Str::limit($pengaduan->pengaduan, 100) -> tampil sampai 100 karakter.
                                --}}
                                <td>{{ Str::limit($pengaduan->pengaduan, 50) }}</td>

                                {{-- Kolom status, tampil sebagai badge berwarna sesuai nilai status --}}
                                <td>
                                    @if($pengaduan->status == 'menunggu')
                                        {{--
                                            badge = label kecil berwarna. bg-warning = kuning.
                                            Contoh ganti warna: bg-danger (merah), bg-secondary (abu-abu), bg-primary (biru).
                                            Contoh: tambah "rounded-pill" -> badge berbentuk kapsul bulat.
                                        --}}
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($pengaduan->status == 'diproses')
                                        {{-- bg-info = biru muda untuk status diproses --}}
                                        <span class="badge bg-info">Diproses</span>
                                    @else
                                        {{-- bg-success = hijau untuk status selesai (semua status selain dua di atas) --}}
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