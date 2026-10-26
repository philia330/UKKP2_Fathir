{{--
    =====================================================================
    CATATAN UMUM
    =====================================================================
    - Komentar bentuk kurung-kurawal-kurawal-strip-strip (seperti yang sedang kamu baca ini)
      adalah komentar Blade, tidak ikut tampil di HTML / "View Page Source".
    - Halaman ini menampilkan data akun PETUGAS yang sedang login (versi petugas).
    - Bedanya dengan profil lain: warna dominan BIRU MUDA (text-info dan bg-info)
      dan sidebar yang dipakai adalah sidebar-petugas.
        admin    -> biru tua (text-primary)
        petugas  -> biru muda (text-info)
        customer -> hijau (text-success)
    - Skala spasi Bootstrap: 0 = 0px, 1 = 4px, 2 = 8px, 3 = 16px, 4 = 24px, 5 = 48px.
    - Variabel $user dikirim dari controller, contoh: return view('profile', ['user' => auth()->user()]);
    =====================================================================
--}}

{{-- Memakai layout utama "layouts.app" (isi: navbar, <head>, CSS/JS Bootstrap, dll). --}}
@extends('layouts.app')

{{-- Judul tab browser. Contoh ubah: 'Profil Saya - Sistem Pengaduan' --}}
@section('title', 'Profile - Sistem Pengaduan')

{{-- Awal isi halaman, akan dimasukkan ke @yield('content') di layouts.app --}}
@section('content')

{{--
    WADAH UTAMA: sidebar dan konten berdampingan.
    d-flex = anak elemen disusun sejajar horizontal (kiri-kanan).
    Contoh ubah:
      - "d-flex flex-row-reverse" -> sidebar pindah ke KANAN, konten ke KIRI
      - "d-flex flex-column"      -> sidebar di atas, konten di bawah
--}}
<div class="d-flex">

    {{-- Memasukkan sidebar khusus petugas (resources/views/partials/sidebar-petugas.blade.php) --}}
    @include('partials.sidebar-petugas')

    {{--
        AREA KONTEN UTAMA.
        main-content = class CSS buatan sendiri (cek di file CSS/layout kamu).
        flex-grow-1  = lebar otomatis mengisi sisa ruang setelah sidebar.
        p-4          = padding 24px di semua sisi.
                       Contoh: p-2 (rapat, 8px), p-5 (lega, 48px), px-5 (hanya kiri-kanan)
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{--
            KARTU PEMBUNGKUS.
            card      = kotak putih dengan garis tepi dan sudut membulat.
            card-body = ruang padding di dalam kartu.
            Contoh ubah:
              - "card shadow-sm"        -> tambah bayangan tipis
              - "card border-0 shadow"  -> tanpa garis tepi, bayangan lebih tebal
              - "card border-info"      -> garis tepi kartu berwarna biru muda (senada dengan role)
            Contoh batasi lebar kartu agar tidak terlalu melebar:
              <div class="card" style="max-width: 700px;">
            Contoh kartu di tengah halaman:
              <div class="card mx-auto" style="max-width: 700px;">
        --}}
        <div class="card">
            <div class="card-body">

                {{--
                    BAGIAN ATAS PROFIL: ikon + nama + badge role, disusun sejajar.
                    d-flex             = isi sejajar horizontal (ikon di kiri, teks di kanannya).
                    align-items-center = isi rata TENGAH secara vertikal.
                                         Contoh: align-items-start (rata atas), align-items-end (rata bawah)
                    mb-4               = margin bawah 24px (jarak ke tabel).
                                         Contoh: mb-2 (rapat), mb-5 (renggang)
                    Contoh rata tengah semua isi: tambah "justify-content-center"
                    Contoh rata kanan semua isi : tambah "justify-content-end"
                --}}
                <div class="d-flex align-items-center mb-4">

                    {{-- Profil petugas memakai warna biru agar mudah dibedakan dari admin dan customer. --}}
                    {{--
                        bi bi-person-circle = ikon orang dalam lingkaran (Bootstrap Icons).
                                              Contoh ganti: bi-person-badge, bi-person-workspace, bi-headset
                        text-info           = WARNA ikon BIRU MUDA (pembeda role petugas).
                                              Contoh: text-primary (biru tua), text-success (hijau),
                                              text-danger (merah), text-secondary (abu-abu)
                        style="font-size: 4rem;" = UKURAN ikon (ikon diperlakukan seperti teks).
                                              Contoh: 2rem (kecil), 6rem (besar), 80px (pakai piksel)
                    --}}
                    <i class="bi bi-person-circle text-info" style="font-size: 4rem;"></i>

                    {{--
                        Wadah nama + badge.
                        ms-3 = margin KIRI 16px (jarak dari ikon).
                               Contoh: ms-1 (dekat), ms-5 (jauh)
                               Kalau ikon dipindah ke kanan teks, ganti jadi me-3 (margin kanan).
                    --}}
                    <div class="ms-3">
                        {{--
                            NAMA USER.
                            h4   = ukuran heading level 4. Contoh: h2 (lebih besar), h6 (lebih kecil)
                            mb-0 = tanpa margin bawah, supaya badge menempel rapat di bawah nama.
                                   Contoh: mb-2 -> ada jarak 8px antara nama dan badge
                            Contoh tebal/warna: class="mb-0 fw-bold text-info"
                        --}}
                        <h4 class="mb-0">{{ $user->nama }}</h4>

                        {{--
                            BADGE ROLE (di halaman ini isinya "Petugas").
                            badge    = label kecil berbentuk pil.
                            bg-info  = latar BIRU MUDA. Warna ini sebaiknya sama dengan warna ikon di atas.
                                       Contoh: bg-primary (biru tua), bg-success (hijau), bg-dark (hitam)
                                       Kalau ganti warna ikon, ganti juga warna badge agar tetap senada.
                            Catatan: bg-info berwarna terang, jadi teks putih kurang kontras.
                                     Kalau teks sulit dibaca, tambah "text-dark" -> class="badge bg-info text-dark"
                            ucfirst() = huruf pertama dikapital, contoh "petugas" -> "Petugas".
                            Contoh bentuk lebih bulat: tambah "rounded-pill"
                            Contoh lebih besar: tambah "fs-6"
                        --}}
                        <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                    </div>
                </div>

                {{-- Data akun petugas selalu diambil dari user yang sedang login. --}}
                {{--
                    table            = style tabel dasar Bootstrap.
                    table-borderless = tanpa garis antar baris/kolom (tampilan bersih).
                    Contoh ubah:
                      - ganti jadi "table table-striped"  -> baris belang-seling (zebra)
                      - "table table-bordered"            -> semua sel diberi garis
                      - "table table-hover"               -> baris berubah warna saat kursor lewat
                      - "table table-sm"                  -> tabel lebih rapat
                --}}
                <table class="table table-borderless">
                    <tr>
                        {{--
                            Kolom label (Nama, Email, dst).
                            width="200" = LEBAR kolom label 200px, supaya semua nilai di kanan lurus sejajar.
                                          Contoh: width="120" (lebih sempit), width="300" (lebih lebar)
                            Catatan: atribut width di <th> sudah usang di HTML5. Cara modern:
                                          <th style="width: 200px;">
                            Contoh rata teks label:
                              <th class="text-end">   -> label rata KANAN (dekat nilai)
                              <th class="text-start"> -> label rata KIRI (bawaan)
                            Contoh warna label: <th class="text-muted"> (abu-abu)
                        --}}
                        <th width="200">Nama</th>
                        {{-- Nilai nama dari database --}}
                        <td>{{ $user->nama }}</td>
                    </tr>
                    <tr>
                        {{-- Label email (lebarnya mengikuti baris pertama, jadi tidak perlu width lagi) --}}
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        {{--
                            "?? '-'" = jika no_telp kosong (null), tampilkan tanda "-".
                            Contoh ubah teks pengganti: ?? 'Belum diisi'
                            Contoh beri warna abu-abu jika kosong:
                              <td class="{{ $user->no_telp ? '' : 'text-muted' }}">{{ $user->no_telp ?? '-' }}</td>
                        --}}
                        <td>{{ $user->no_telp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        {{-- Role dengan huruf awal kapital. Contoh: "petugas" -> "Petugas" --}}
                        <td>{{ ucfirst($user->role) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection