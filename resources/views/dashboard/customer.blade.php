{{-- Memakai layout utama (layouts/app.blade.php) sebagai kerangka halaman --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Beranda Customer - Sistem Pengaduan' --}}
@section('title', 'Dashboard Customer - Sistem Pengaduan')

{{-- Isi utama halaman yang dimasukkan ke @yield('content') di layout --}}
@section('content')

{{--
    d-flex = sidebar dan konten sejajar ke samping (kiri-kanan).
    Contoh: hapus "d-flex" -> sidebar dan konten jadi tersusun atas-bawah.
    Contoh: "d-flex flex-row-reverse" -> sidebar pindah ke sisi kanan.
--}}
<div class="d-flex">

    {{-- Memanggil menu sidebar CUSTOMER dari partials/sidebar-customer.blade.php --}}
    @include('partials.sidebar-customer')

    {{--
        Konten utama dashboard customer. Data yang ditampilkan bersifat personal berdasarkan user yang login.

        main-content = class buatan sendiri, atur lebar/margin di file CSS (cari ".main-content").
        flex-grow-1  = konten melebar mengisi sisa ruang setelah sidebar.
                       Contoh: hapus -> konten hanya selebar isinya.
        p-4          = jarak dalam (padding) ke semua sisi. Skala 0 sampai 5.
                       Contoh: p-2 (lebih rapat), p-5 (lebih lega), px-4 (kiri-kanan saja), py-4 (atas-bawah saja).
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{--
            ===== BARIS KARTU STATISTIK =====
            Kartu statistik ringkas untuk melihat perkembangan laporan customer.

            row = baris grid Bootstrap (tempat kolom diletakkan, total 12 kolom per baris).
            g-4 = jarak (gap) antar kolom, horizontal dan vertikal. Skala 0 sampai 5.
                  Contoh: g-2 (lebih rapat), g-5 (lebih renggang).
                  Contoh: gx-4 -> jarak kiri-kanan saja, gy-4 -> jarak atas-bawah saja.
        --}}
        <div class="row g-4">

            {{-- ===== KARTU 1: TOTAL PENGADUAN ===== --}}
            {{--
                col-md-3 = lebar 3/12 (seperempat) di layar sedang (tablet) ke atas -> 4 kartu sebaris.
                           Di HP otomatis penuh dan bertumpuk ke bawah.
                Contoh: col-md-6 -> 2 kartu per baris. col-md-4 -> 3 kartu per baris.
                Contoh: ganti "col-6 col-md-3" -> di HP jadi 2 kartu per baris.
            --}}
            <div class="col-md-3">

                {{--
                    card = kotak putih dengan border dan sudut membulat.
                    card-stat = class buatan sendiri untuk gaya kartu statistik.
                                Atur di file CSS (cari ".card-stat"), misalnya warna garis tepi,
                                bayangan, atau efek saat kursor diarahkan (hover).
                    Contoh: tambah "shadow-sm" -> ada bayangan tipis. Tambah "border-0" -> tanpa garis tepi.
                    Contoh: tambah "h-100" -> semua kartu tingginya sama.
                --}}
                <div class="card card-stat">

                    {{-- card-body = area isi card (sudah punya padding bawaan) --}}
                    <div class="card-body">

                        {{--
                            d-flex                  = teks angka dan ikon berjajar ke samping.
                            justify-content-between = teks di kiri, ikon di kanan (ujung ke ujung).
                                                      Contoh: justify-content-start (semua ke kiri),
                                                      justify-content-center (tengah).
                            align-items-center      = isi rata tengah secara vertikal.
                                                      Contoh: align-items-start (rata atas), align-items-end (rata bawah).
                            Contoh: tambah "flex-row-reverse" -> ikon pindah ke kiri, teks ke kanan.
                        --}}
                        <div class="d-flex justify-content-between align-items-center">

                            {{-- Sisi kiri: judul dan angka --}}
                            <div>
                                {{--
                                    h6 = ukuran judul kecil. text-muted = warna abu-abu.
                                    mb-2 = margin bawah 2, jarak ke angka di bawahnya.
                                    Contoh: text-dark (hitam), text-primary (biru). mb-0 (rapat), mb-3 (renggang).
                                --}}
                                <h6 class="text-muted mb-2">Total Pengaduan</h6>

                                {{--
                                    h3 = ukuran angka besar. Contoh: h2 (lebih besar), h4 (lebih kecil).
                                    mb-0 = margin bawah 0 (hilangkan jarak bawah).
                                    Contoh: tambah "fw-bold" -> angka jadi tebal.
                                    {{ $totalPengaduan }} = jumlah semua pengaduan milik customer yang login, dihitung di controller.
                                --}}
                                <h3 class="mb-0">{{ $totalPengaduan }}</h3>
                            </div>

                            {{--
                                Sisi kanan: ikon.
                                icon = class buatan sendiri. Atur ukuran ikon di CSS (cari ".icon" atau ".card-stat .icon").
                                        Contoh di CSS: .card-stat .icon { font-size: 2.5rem; } -> ikon lebih besar.
                                text-primary = warna ikon biru.
                                Contoh ganti warna: text-success (hijau), text-danger (merah), text-secondary (abu-abu).
                            --}}
                            <div class="icon text-primary">
                                {{-- bi-file-earmark-text = ikon dokumen. Contoh ganti ikon: bi-folder --}}
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== KARTU 2: MENUNGGU ===== --}}
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Menunggu</h6>
                                {{-- Jumlah pengaduan berstatus "menunggu", dihitung di controller --}}
                                <h3 class="mb-0">{{ $menunggu }}</h3>
                            </div>
                            {{-- text-warning = ikon kuning (sama dengan badge status Menunggu). bi-clock = ikon jam --}}
                            <div class="icon text-warning">
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== KARTU 3: DIPROSES ===== --}}
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Diproses</h6>
                                {{-- Jumlah pengaduan berstatus "diproses", dihitung di controller --}}
                                <h3 class="mb-0">{{ $diproses }}</h3>
                            </div>
                            {{-- text-info = ikon biru muda. bi-arrow-repeat = ikon panah berputar --}}
                            <div class="icon text-info">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== KARTU 4: SELESAI ===== --}}
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Selesai</h6>
                                {{-- Jumlah pengaduan berstatus "selesai", dihitung di controller --}}
                                <h3 class="mb-0">{{ $selesai }}</h3>
                            </div>
                            {{-- text-success = ikon hijau. bi-check-circle = ikon centang --}}
                            <div class="icon text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--
            ===== CARD AJAKAN BUAT PENGADUAN =====
            mt-4 = margin atas 4, jarak dari baris kartu di atasnya. Skala 0 sampai 5.
            Contoh: mt-2 (lebih dekat), mt-5 (lebih jauh).
        --}}
        <div class="mt-4">
            <div class="card">
                <div class="card-body">

                    {{--
                        card-title = gaya judul di dalam card.
                        me-2 pada ikon = margin kanan 2, jarak ikon ke teks.
                        Contoh ganti ikon: bi-chat-left-text, bi-exclamation-circle.
                    --}}
                    <h5 class="card-title">
                        <i class="bi bi-megaphone me-2"></i>Buat Pengaduan
                    </h5>

                    {{-- card-text = gaya paragraf di card. text-muted = abu-abu. Contoh: text-dark (hitam) --}}
                    <p class="card-text text-muted">Silakan buat pengaduan jika Anda mengalami masalah.</p>

                    {{--
                        Tombol menuju halaman buat pengaduan.
                        btn-primary = biru. Contoh ganti warna: btn-success (hijau), btn-danger (merah).
                        Contoh: tambah "btn-lg" -> tombol lebih besar. btn-outline-primary -> hanya garis tepi.
                    --}}
                    <a href="{{ route('customer.pengaduan.create') }}" class="btn btn-primary">
                        {{-- me-1 = jarak kanan ikon ke teks --}}
                        <i class="bi bi-plus-circle me-1"></i>Buat Pengaduan Baru
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== DAFTAR PENGADUAN TERBARU =====
             Daftar pengaduan terbaru milik customer yang sedang login. --}}
        <div class="mt-4">
            <div class="card">

                {{-- card-header = bagian judul di atas card (tanpa tombol, jadi tidak perlu d-flex) --}}
                <div class="card-header">
                    {{-- bi-clock-history = ikon jam dengan riwayat. mb-0 = tanpa margin bawah --}}
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pengaduan Saya</h5>
                </div>

                <div class="card-body">

                    {{-- Kalau belum ada pengaduan, tampilkan pesan kosong. Kalau ada, tampilkan tabel --}}
                    @if($recentPengaduan->isEmpty())
                        {{--
                            text-muted = abu-abu. text-center = rata tengah. mb-0 = tanpa margin bawah.
                            Contoh: text-start (kiri), text-danger (merah).
                        --}}
                        <p class="text-muted text-center mb-0">Belum ada pengaduan.</p>
                    @else
                        {{--
                            table-responsive = tabel bisa digeser (scroll) ke samping di layar kecil/HP
                            supaya tidak merusak tampilan halaman.
                        --}}
                        <div class="table-responsive">

                            {{--
                                table-hover = baris berubah warna saat kursor diarahkan.
                                Contoh tambahan: table-striped (baris selang-seling), table-bordered, table-sm (lebih rapat).
                            --}}
                            <table class="table table-hover">
                                {{-- Judul kolom tabel. Contoh: tambah class "table-dark" pada thead -> header hitam --}}
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Pengaduan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Perulangan pengaduan terbaru. $index dipakai untuk nomor urut --}}
                                    @foreach($recentPengaduan as $index => $pengaduan)
                                    <tr>
                                        {{-- $index mulai dari 0, jadi ditambah 1 supaya nomor mulai dari 1 --}}
                                        <td>{{ $index + 1 }}</td>

                                        {{-- Tanggal pengaduan dibuat. Contoh ubah format: 'd M Y' -> 26 Okt 2026 --}}
                                        <td>{{ $pengaduan->created_at->format('d/m/Y') }}</td>

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
                                                    Contoh ganti warna: bg-danger (merah), bg-secondary (abu-abu).
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
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== PESAN SELAMAT DATANG ===== --}}
        <div class="mt-4">
            {{--
                alert alert-info = kotak informasi berwarna biru muda.
                Contoh ganti warna: alert-success (hijau), alert-warning (kuning), alert-primary (biru tua).
                Contoh: tambah "alert-dismissible fade show" + tombol btn-close -> bisa ditutup.
            --}}
            <div class="alert alert-info">
                {{-- bi-info-circle = ikon info. me-2 = margin kanan 2, jarak ikon ke teks --}}
                <i class="bi bi-info-circle me-2"></i>

                {{--
                    auth()->user()->nama = nama user yang sedang login.
                    <strong> = teks tebal. Contoh: ganti jadi <em> -> teks miring.
                --}}
                Selamat datang, <strong>{{ auth()->user()->nama }}</strong>! Anda login sebagai <strong>Customer</strong>.
            </div>
        </div>
    </div>
</div>
@endsection