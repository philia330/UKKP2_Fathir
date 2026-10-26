{{-- Memakai layout utama (layouts/app.blade.php) sebagai kerangka halaman --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Masuk - Sistem Pengaduan' --}}
@section('title', 'Login - Sistem Pengaduan')

{{-- Isi utama halaman yang dimasukkan ke @yield('content') di layout --}}
@section('content')

{{--
    container = pembungkus yang membuat konten berada di tengah dengan lebar maksimal sesuai ukuran layar.
    Contoh: ganti "container-fluid" -> lebar penuh layar.
    Contoh: ganti "container-sm" -> container hanya aktif mulai ukuran sm ke atas.
--}}
<div class="container">

    {{--
        row = baris grid Bootstrap (tempat kolom diletakkan).
        justify-content-center = kolom berada di tengah secara horizontal.
                                 Contoh: justify-content-start (kiri), justify-content-end (kanan).
        align-items-center     = kolom berada di tengah secara vertikal (karena tinggi row dibuat setinggi layar).
                                 Contoh: align-items-start (menempel atas), align-items-end (menempel bawah).
        min-height: 100vh      = tinggi minimal setinggi layar (100% tinggi viewport).
                                 Contoh: 50vh -> setengah tinggi layar, form jadi lebih ke atas.
                                 Contoh: 80vh -> form agak naik sedikit dari tengah.
    --}}
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">

        {{--
            Kolom yang berisi card login. Lebar ditentukan sesuai ukuran layar (grid 12 kolom).
            col-md-5  = lebar 5/12 di layar sedang (tablet) ke atas. Di HP otomatis penuh.
            col-lg-4  = lebar 4/12 di layar besar (laptop) ke atas.
            Contoh: col-lg-3 -> card lebih sempit. col-lg-6 -> card lebih lebar.
        --}}
        <div class="col-md-5 col-lg-4">

            {{--
                card = kotak putih dengan border dan sudut membulat.
                shadow-sm = bayangan tipis. Contoh: shadow (sedang), shadow-lg (tebal), hapus -> tanpa bayangan.
                Contoh: tambah "border-0" -> tanpa garis tepi.
            --}}
            <div class="card shadow-sm">

                {{--
                    card-body = area isi card.
                    p-4 = padding (jarak dalam) ke semua sisi. Skala 0 sampai 5.
                    Contoh: p-3 (lebih rapat), p-5 (lebih lega), px-5 (kiri-kanan saja).
                --}}
                <div class="card-body p-4">

                    {{--
                        Bagian header: ikon, judul, dan keterangan.
                        text-center = semua isi rata tengah. Contoh: text-start (kiri), text-end (kanan).
                        mb-4 = margin bawah 4, jarak ke bagian di bawahnya. Contoh: mb-2 (rapat), mb-5 (renggang).
                    --}}
                    <div class="text-center mb-4">

                        {{--
                            bi bi-shield-lock = ikon perisai dengan gembok dari Bootstrap Icons.
                            font-size: 3rem = ukuran ikon. Contoh: 2rem (kecil), 5rem (besar).
                            color: #0d6efd  = warna ikon (biru Bootstrap).
                            Contoh warna: #198754 (hijau), #dc3545 (merah), #6c757d (abu-abu).
                            Contoh ganti ikon: bi-person-circle, bi-key.
                        --}}
                        <i class="bi bi-shield-lock" style="font-size: 3rem; color: #0d6efd;"></i>

                        {{--
                            h4 = ukuran judul. Contoh: h3 (lebih besar), h5 (lebih kecil).
                            mt-2 = margin atas 2, jarak dari ikon. mb-1 = margin bawah 1, jarak ke teks di bawah.
                        --}}
                        <h4 class="mt-2 mb-1">Sistem Pengaduan</h4>

                        {{--
                            text-muted = warna teks abu-abu.
                            Contoh: text-primary (biru), text-dark (hitam), text-danger (merah).
                            Contoh: tambah "small" -> ukuran huruf lebih kecil.
                        --}}
                        <p class="text-muted">Silakan login untuk melanjutkan</p>
                    </div>

                    {{-- Tampilkan pesan error validasi jika email atau password salah. --}}
                    @if ($errors->any())
                        {{--
                            alert alert-danger = kotak peringatan merah.
                            Contoh ganti warna: alert-warning (kuning), alert-info (biru).
                            py-2 = padding atas-bawah 2 (lebih tipis dari bawaan).
                            Contoh: py-3 (lebih tebal), hapus -> ukuran bawaan Bootstrap.
                        --}}
                        <div class="alert alert-danger py-2">
                            {{--
                                mb-0 = margin bawah 0 (hilangkan jarak bawah list).
                                ps-3 = padding kiri (start) 3, jarak bullet dari tepi kiri.
                                Contoh: ps-4 -> lebih menjorok ke kanan. list-unstyled -> tanpa bullet.
                            --}}
                            <ul class="mb-0 ps-3">
                                {{-- Mengulang setiap pesan error menjadi satu item list --}}
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Tampilkan pesan sukses setelah registrasi atau logout. --}}
                    @if (session('success'))
                        {{-- alert-success = kotak hijau. py-2 = padding atas-bawah tipis --}}
                        <div class="alert alert-success py-2">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{--
                        Form login. Data dikirim ke route 'login' dengan method POST.
                        action = tujuan pengiriman data (diatur di routes/web.php).
                    --}}
                    <form action="{{ route('login') }}" method="POST">
                        {{-- Token keamanan Laravel, wajib ada agar form tidak ditolak (error 419) --}}
                        @csrf

                        {{--
                            mb-3 = margin bawah 3, jarak ke input di bawahnya. Skala 0 sampai 5.
                            Contoh: mb-2 (rapat), mb-4 (renggang).
                        --}}
                        <div class="mb-3">
                            {{-- Label untuk input email. for="email" menghubungkan label ke input ber-id "email" --}}
                            <label for="email" class="form-label">Email</label>

                            {{--
                                input-group = menggabungkan ikon dan input dalam satu baris.
                                Contoh: tambah "input-group-lg" -> lebih besar, "input-group-sm" -> lebih kecil.
                            --}}
                            <div class="input-group">
                                {{--
                                    input-group-text = kotak kecil berisi ikon di sisi KIRI input.
                                    Contoh: pindahkan span ini ke setelah <input> -> ikon pindah ke sisi KANAN.
                                    bi-envelope = ikon amplop.
                                --}}
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>

                                {{--
                                    form-control = input selebar sisa ruang dengan gaya Bootstrap.
                                    is-invalid (muncul otomatis lewat @error) = border merah saat email bermasalah.
                                    type="email" = browser mengecek format email.
                                    value="{{ old('email') }}" = mengisi ulang email lama saat validasi gagal,
                                                                 supaya user tidak mengetik ulang.
                                    placeholder = teks petunjuk abu-abu. Contoh: "nama@email.com".
                                    required = browser menolak submit kalau kosong.
                                    autofocus = kursor langsung aktif di input ini saat halaman dibuka.
                                --}}
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="Masukkan email"
                                       required
                                       autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            {{-- Label untuk input password --}}
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                {{-- Ikon gembok di sisi kiri input --}}
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>

                                {{--
                                    type="password" = karakter yang diketik disamarkan jadi titik.
                                    Tidak ada value/old() karena password sengaja tidak diisi ulang demi keamanan.
                                --}}
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Masukkan password"
                                       required>
                            </div>
                        </div>

                        {{--
                            Tombol login.
                            btn-primary = biru. Contoh ganti warna: btn-success (hijau), btn-dark (hitam).
                            w-100 = lebar 100% (selebar card). Contoh: hapus -> selebar teks saja,
                                    w-50 mx-auto -> setengah lebar dan di tengah.
                            Contoh: tambah "btn-lg" -> tombol lebih besar.
                            type="submit" = menekan tombol ini mengirim form.
                        --}}
                        <button type="submit" class="btn btn-primary w-100">
                            {{-- me-1 = jarak kanan ikon ke teks. Contoh ganti ikon: bi-door-open --}}
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </button>
                    </form>

                    {{-- hr = garis pemisah horizontal antara form dan teks daftar --}}
                    <hr>

                    {{--
                        text-center = rata tengah. mb-0 = tanpa margin bawah supaya tidak ada ruang kosong di dasar card.
                        Contoh: text-end -> rata kanan.
                    --}}
                    <p class="text-center mb-0">
                        Belum punya akun?
                        {{-- Link menuju halaman registrasi. Contoh: tambah class "fw-bold" -> link jadi tebal --}}
                        <a href="{{ route('register') }}">Daftar di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection