{{-- Memakai layout utama (layouts/app.blade.php) sebagai kerangka halaman --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Beranda Admin - Sistem Pengaduan' --}}
@section('title', 'Dashboard Admin - Sistem Pengaduan')

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
        Konten utama dashboard admin. Di sini admin melihat gambaran umum sistem dan statistik utama.

        main-content = class buatan sendiri, atur lebar/margin di file CSS (cari ".main-content").
        flex-grow-1  = konten melebar mengisi sisa ruang setelah sidebar.
                       Contoh: hapus -> konten hanya selebar isinya.
        p-4          = jarak dalam (padding) ke semua sisi. Skala 0 sampai 5.
                       Contoh: p-2 (lebih rapat), p-5 (lebih lega), px-4 (kiri-kanan saja), py-4 (atas-bawah saja).
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{--
            ===== BARIS KARTU STATISTIK =====
            Kartu statistik ini membantu admin memantau jumlah user dan laporan yang ada di sistem.

            row = baris grid Bootstrap (tempat kolom diletakkan, total 12 kolom per baris).
            g-4 = jarak (gap) antar kolom, horizontal dan vertikal. Skala 0 sampai 5.
                  Contoh: g-2 (lebih rapat), g-5 (lebih renggang).
                  Contoh: gx-4 -> jarak kiri-kanan saja, gy-4 -> jarak atas-bawah saja.
        --}}
        <div class="row g-4">

            {{-- ===== KARTU 1: TOTAL USER ===== --}}
            {{--
                col-md-3 = lebar 3/12 (seperempat) di layar sedang (tablet) ke atas -> 4 kartu sebaris.
                           Di HP otomatis penuh dan bertumpuk ke bawah.
                Contoh: col-md-6 -> 2 kartu per baris. col-md-4 -> 3 kartu per baris.
                Contoh: tambah "col-sm-6" -> di layar kecil jadi 2 kartu per baris (tidak bertumpuk 1).
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
                                <h6 class="text-muted mb-2">Total User</h6>

                                {{--
                                    h3 = ukuran angka besar. Contoh: h2 (lebih besar), h4 (lebih kecil).
                                    mb-0 = margin bawah 0 (hilangkan jarak bawah).
                                    Contoh: tambah "fw-bold" -> angka jadi tebal.
                                    {{ $totalUsers }} = jumlah semua user, dihitung di controller dashboard.
                                --}}
                                <h3 class="mb-0">{{ $totalUsers }}</h3>
                            </div>

                            {{--
                                Sisi kanan: ikon.
                                icon = class buatan sendiri. Atur ukuran ikon di CSS (cari ".icon" atau ".card-stat .icon").
                                        Contoh di CSS: .card-stat .icon { font-size: 2.5rem; } -> ikon lebih besar.
                                text-primary = warna ikon biru.
                                Contoh ganti warna: text-success (hijau), text-danger (merah), text-secondary (abu-abu).
                            --}}
                            <div class="icon text-primary">
                                {{-- bi-people = ikon orang banyak. Contoh ganti ikon: bi-person-lines-fill --}}
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== KARTU 2: TOTAL CUSTOMER ===== --}}
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Customer</h6>
                                {{-- Jumlah user dengan role customer, dihitung di controller --}}
                                <h3 class="mb-0">{{ $totalCustomers }}</h3>
                            </div>
                            {{-- text-success = ikon hijau. bi-person-check = ikon orang dengan centang --}}
                            <div class="icon text-success">
                                <i class="bi bi-person-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== KARTU 3: TOTAL PETUGAS ===== --}}
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Petugas</h6>
                                {{-- Jumlah user dengan role petugas, dihitung di controller --}}
                                <h3 class="mb-0">{{ $totalPetugas }}</h3>
                            </div>
                            {{-- text-info = ikon biru muda. bi-person-badge = ikon orang dengan lencana --}}
                            <div class="icon text-info">
                                <i class="bi bi-person-badge"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== KARTU 4: TOTAL PENGADUAN ===== --}}
            <div class="col-md-3">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Pengaduan</h6>
                                {{-- Jumlah semua pengaduan yang masuk ke sistem, dihitung di controller --}}
                                <h3 class="mb-0">{{ $totalPengaduan }}</h3>
                            </div>
                            {{-- text-warning = ikon kuning. bi-file-earmark-text = ikon dokumen --}}
                            <div class="icon text-warning">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--
            ===== PESAN SELAMAT DATANG =====
            mt-4 = margin atas 4, jarak dari baris kartu di atasnya. Skala 0 sampai 5.
            Contoh: mt-2 (lebih dekat), mt-5 (lebih jauh).
        --}}
        <div class="mt-4">

            {{--
                alert alert-info = kotak informasi berwarna biru muda.
                Contoh ganti warna: alert-success (hijau), alert-warning (kuning), alert-primary (biru tua).
                Contoh: tambah "alert-dismissible fade show" + tombol btn-close -> bisa ditutup.
            --}}
            <div class="alert alert-info">
                {{-- bi-info-circle = ikon info. me-2 = margin kanan 2, jarak ikon ke teks. Contoh: me-3 (lebih jauh) --}}
                <i class="bi bi-info-circle me-2"></i>

                {{--
                    auth()->user()->nama = nama user yang sedang login.
                    <strong> = teks tebal. Contoh: ganti jadi <em> -> teks miring.
                --}}
                Selamat datang, <strong>{{ auth()->user()->nama }}</strong>! Anda login sebagai <strong>Admin</strong>.
            </div>
        </div>
    </div>
</div>
@endsection