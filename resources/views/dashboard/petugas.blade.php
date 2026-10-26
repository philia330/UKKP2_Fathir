{{--
    CATATAN: komentar {{-- --}} ini khusus Blade, jadi TIDAK muncul di "View Page Source" browser.
    Beda dengan komentar HTML <!-- --> yang tetap terlihat oleh pengunjung.
--}}

{{-- Memakai layout utama (layouts/app.blade.php). Semua isi di bawah akan dimasukkan ke @yield('content') di layout itu. --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh: ganti jadi 'Beranda Petugas' kalau mau judul lain. --}}
@section('title', 'Dashboard Petugas - Sistem Pengaduan')

{{-- Awal isi halaman yang dimasukkan ke layout --}}
@section('content')

{{--
    WRAPPER UTAMA (sidebar + konten)
    - d-flex      : membuat anak-anaknya berjajar menyamping (sidebar kiri, konten kanan)
    - Contoh      : ganti jadi "d-flex flex-column" kalau mau sidebar di atas & konten di bawah
--}}
<div class="d-flex">

    {{-- SIDEBAR: memanggil file resources/views/partials/sidebar-petugas.blade.php (menu navigasi di sisi kiri) --}}
    @include('partials.sidebar-petugas')

    {{--
        AREA KONTEN UTAMA: tempat petugas melihat laporan yang masuk dan statusnya.
        - main-content  : class buatan sendiri (ada di file CSS kamu), biasanya untuk warna latar / margin
        - flex-grow-1   : konten mengambil sisa lebar layar setelah dipakai sidebar
        - p-4           : padding (jarak isi ke tepi) ukuran 4 dari skala 0-5
                          Contoh: p-2 = lebih rapat, p-5 = lebih lega, px-4 = hanya kiri-kanan, py-4 = hanya atas-bawah
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{-- ========== BAGIAN 1: KARTU STATISTIK ==========
             Tujuan: petugas cepat melihat jumlah laporan total, yang masih menunggu, dan yang selesai.
             - row   : baris grid Bootstrap (isinya kolom-kolom col-*)
             - g-4   : jarak antar kartu (gap). Contoh: g-2 = rapat, g-5 = renggang
        --}}
        <div class="row g-4">

            {{-- Kartu 1: Total Pengaduan
                 - col-md-4 : lebar 4 dari 12 kolom di layar medium ke atas = 3 kartu sebaris.
                              Contoh: col-md-6 = 2 kartu sebaris, col-md-12 = 1 kartu penuh, col-md-3 = 4 kartu sebaris
                              (di HP otomatis turun jadi 1 kartu per baris) --}}
            <div class="col-md-4">
                {{-- card      : kotak putih dengan border & sudut membulat bawaan Bootstrap
                     card-stat : class buatan sendiri di CSS kamu untuk gaya khusus kartu statistik (bayangan, hover, dll) --}}
                <div class="card card-stat">
                    {{-- card-body : area dalam kartu dengan padding bawaan --}}
                    <div class="card-body">
                        {{-- d-flex + justify-content-between : teks di kiri, ikon di kanan (terdorong ke ujung)
                             align-items-center : keduanya sejajar di tengah secara vertikal
                             Contoh: justify-content-start = semua menempel ke KIRI,
                                     justify-content-end   = semua menempel ke KANAN,
                                     justify-content-center = semua di TENGAH,
                                     align-items-start = sejajar ke ATAS, align-items-end = sejajar ke BAWAH --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                {{-- text-muted : warna abu-abu (teks sekunder). Contoh: text-dark = hitam, text-primary = biru
                                     mb-2       : margin bawah ukuran 2 (jarak ke judul angka). Contoh: mb-0 = tanpa jarak, mb-4 = lebih jauh
                                     h6         : ukuran font kecil. Contoh: ganti tag jadi <h5> untuk lebih besar --}}
                                <h6 class="text-muted mb-2">Total Pengaduan</h6>
                                {{-- h3 : ukuran font angka (lebih besar dari h6). Contoh: h2 = lebih besar, h4 = lebih kecil
                                     mb-0 : tanpa margin bawah supaya kartu tidak terlalu tinggi
                                     {{ $totalPengaduan }} : angka dari controller (jumlah semua pengaduan) --}}
                                <h3 class="mb-0">{{ $totalPengaduan }}</h3>
                            </div>
                            {{-- icon : class buatan sendiri di CSS (biasanya mengatur ukuran ikon, mis. font-size: 2rem)
                                 text-primary : warna ikon biru. Contoh: text-danger = merah, text-success = hijau, text-warning = kuning, text-secondary = abu --}}
                            <div class="icon text-primary">
                                {{-- bi bi-file-earmark-text : ikon Bootstrap Icons (gambar dokumen).
                                     Contoh: ganti jadi bi-folder untuk ikon folder. Daftar ikon: icons.getbootstrap.com --}}
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu 2: Menunggu (laporan yang belum ditangani). Struktur sama dengan kartu 1, bedanya warna ikon kuning (text-warning) --}}
            <div class="col-md-4">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Menunggu</h6>
                                {{-- $menunggu : jumlah pengaduan berstatus 'menunggu' dari controller --}}
                                <h3 class="mb-0">{{ $menunggu }}</h3>
                            </div>
                            <div class="icon text-warning">
                                {{-- bi-clock : ikon jam, melambangkan "sedang menunggu" --}}
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu 3: Selesai (laporan yang sudah tuntas). Ikon hijau (text-success) --}}
            <div class="col-md-4">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Selesai</h6>
                                {{-- $selesai : jumlah pengaduan berstatus 'selesai' dari controller --}}
                                <h3 class="mb-0">{{ $selesai }}</h3>
                            </div>
                            <div class="icon text-success">
                                {{-- bi-check-circle : ikon centang dalam lingkaran --}}
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- ========== AKHIR KARTU STATISTIK ========== --}}

        {{-- ========== BAGIAN 2: TABEL PENGADUAN TERBARU ==========
             Tujuan: petugas melihat laporan yang baru masuk.
             - mt-4 : margin atas ukuran 4 (jarak dari kartu statistik di atasnya).
                      Contoh: mt-2 = lebih dekat, mt-5 = lebih jauh, mb-4 = jarak di bawah, ms-4 = jarak kiri, me-4 = jarak kanan
        --}}
        <div class="mt-4">
            <div class="card">
                {{-- card-header : bagian judul di atas kartu (biasanya latar abu muda).
                     Contoh: tambah "bg-primary text-white" supaya header jadi biru dengan teks putih --}}
                <div class="card-header">
                    {{-- mb-0 : hilangkan margin bawah judul
                         bi-clock-history : ikon jam riwayat
                         me-2 : margin kanan ukuran 2 (jarak ikon ke teks). Contoh: me-3 = lebih jauh, ms-2 = jarak di KIRI ikon --}}
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pengaduan Terbaru</h5>
                </div>
                <div class="card-body">

                    {{-- Jika data kosong, tampilkan pesan. Jika ada, tampilkan tabel. --}}
                    @if($recentPengaduan->isEmpty())
                        {{-- text-muted : abu-abu
                             text-center : teks rata TENGAH. Contoh: text-start = rata KIRI, text-end = rata KANAN
                             mb-0 : tanpa margin bawah --}}
                        <p class="text-muted text-center mb-0">Belum ada pengaduan.</p>
                    @else
                        {{-- table-responsive : tabel bisa digeser menyamping di layar kecil (HP) agar tidak merusak layout --}}
                        <div class="table-responsive">
                            {{-- table : gaya tabel dasar Bootstrap
                                 table-hover : baris berubah warna saat kursor diarahkan.
                                 Contoh tambahan: table-striped = baris selang-seling warna, table-bordered = ada garis di semua sisi,
                                                  table-sm = tabel lebih rapat/kecil --}}
                            <table class="table table-hover">
                                {{-- thead : baris judul kolom.
                                     Contoh: <thead class="table-dark"> = header hitam, <thead class="table-primary"> = header biru muda --}}
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Customer</th>
                                        <th>Pengaduan</th>
                                        <th>Status</th>
                                        {{-- Contoh rata kanan satu kolom: <th class="text-end">Status</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Mengulang (loop) setiap pengaduan terbaru jadi satu baris. $index mulai dari 0. --}}
                                    @foreach($recentPengaduan as $index => $pengaduan)
                                    <tr>
                                        {{-- Nomor urut. +1 karena $index mulai dari 0 --}}
                                        <td>{{ $index + 1 }}</td>
                                        {{-- Tanggal masuk, format hari/bulan/tahun. Contoh: 'd-m-Y H:i' => 26-10-2026 14:30 --}}
                                        <td>{{ $pengaduan->created_at->format('d/m/Y') }}</td>
                                        {{-- Nama pelanggan lewat relasi user di model Pengaduan --}}
                                        <td>{{ $pengaduan->user->nama }}</td>
                                        {{-- Isi pengaduan dipotong 50 karakter supaya tabel tidak terlalu panjang. Contoh: ganti 50 jadi 100 untuk lebih panjang --}}
                                        <td>{{ Str::limit($pengaduan->pengaduan, 50) }}</td>
                                        <td>
                                            {{-- Badge status dengan warna berbeda tergantung status:
                                                 - badge    : label kecil berbentuk kotak membulat
                                                 - bg-warning (kuning) = menunggu, bg-info (biru muda) = diproses, bg-success (hijau) = selesai
                                                 Contoh: ganti bg-warning jadi bg-danger untuk merah.
                                                 Tambah "rounded-pill" supaya badge berbentuk kapsul bulat --}}
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
        {{-- ========== AKHIR TABEL PENGADUAN TERBARU ========== --}}

        {{-- ========== BAGIAN 3: PESAN SELAMAT DATANG ==========
             Tujuan: memberi tahu petugas siapa yang sedang login dan perannya. --}}
        <div class="mt-4">
            {{-- alert : kotak pemberitahuan
                 alert-info : warna biru muda. Contoh: alert-success = hijau, alert-warning = kuning, alert-danger = merah, alert-dark = gelap --}}
            <div class="alert alert-info">
                {{-- bi-info-circle : ikon "i" dalam lingkaran. me-2 = jarak kanan ikon --}}
                <i class="bi bi-info-circle me-2"></i>
                {{-- auth()->user()->nama : nama user yang sedang login
                     <strong> : teks tebal (bold) --}}
                Selamat datang, <strong>{{ auth()->user()->nama }}</strong>! Anda login sebagai <strong>Petugas</strong>.
            </div>
        </div>
        {{-- ========== AKHIR PESAN SELAMAT DATANG ========== --}}

    </div>
    {{-- Akhir area konten utama --}}
</div>
{{-- Akhir wrapper sidebar + konten --}}
@endsection