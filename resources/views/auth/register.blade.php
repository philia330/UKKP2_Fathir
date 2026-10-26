{{-- Memakai layout utama (layouts/app.blade.php) sebagai kerangka halaman --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Daftar Akun - Sistem Pengaduan' --}}
@section('title', 'Registrasi - Sistem Pengaduan')

{{-- Isi utama halaman yang dimasukkan ke @yield('content') di layout --}}
@section('content')

{{--
    container = pembungkus yang membuat konten berada di tengah dengan lebar maksimal sesuai ukuran layar.
    Contoh: ganti "container-fluid" -> lebar penuh layar.
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
                                 Catatan: form registrasi cukup panjang, kalau di layar kecil terpotong,
                                 ganti jadi "py-5" pada row dan hapus min-height.
    --}}
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">

        {{--
            Kolom yang berisi card registrasi (grid 12 kolom).
            col-md-6 = lebar 6/12 di layar sedang (tablet) ke atas. Di HP otomatis penuh.
            col-lg-5 = lebar 5/12 di layar besar (laptop) ke atas.
            Contoh: col-lg-4 -> card lebih sempit. col-lg-7 -> card lebih lebar.
        --}}
        <div class="col-md-6 col-lg-5">

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
                            bi bi-person-plus = ikon orang dengan tanda plus dari Bootstrap Icons.
                            font-size: 3rem = ukuran ikon. Contoh: 2rem (kecil), 5rem (besar).
                            color: #0d6efd  = warna ikon (biru Bootstrap).
                            Contoh warna: #198754 (hijau), #dc3545 (merah), #6c757d (abu-abu).
                            Contoh ganti ikon: bi-person-circle, bi-card-checklist.
                        --}}
                        <i class="bi bi-person-plus" style="font-size: 3rem; color: #0d6efd;"></i>

                        {{--
                            h4 = ukuran judul. Contoh: h3 (lebih besar), h5 (lebih kecil).
                            mt-2 = margin atas 2, jarak dari ikon. mb-1 = margin bawah 1, jarak ke teks di bawah.
                        --}}
                        <h4 class="mt-2 mb-1">Registrasi Customer</h4>

                        {{--
                            text-muted = warna teks abu-abu.
                            Contoh: text-primary (biru), text-dark (hitam). Tambah "small" -> huruf lebih kecil.
                        --}}
                        <p class="text-muted">Buat akun baru untuk membuat pengaduan</p>
                    </div>

                    {{-- Tampilkan pesan error validasi untuk mencegah pendaftaran dengan data yang tidak valid. --}}
                    @if ($errors->any())
                        {{--
                            alert alert-danger = kotak peringatan merah.
                            Contoh ganti warna: alert-warning (kuning), alert-info (biru).
                            py-2 = padding atas-bawah 2 (lebih tipis dari bawaan). Contoh: py-3 (lebih tebal).
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

                    {{--
                        Form registrasi. Data dikirim ke route 'register' dengan method POST.
                        action = tujuan pengiriman data (diatur di routes/web.php).
                    --}}
                    <form action="{{ route('register') }}" method="POST">
                        {{-- Token keamanan Laravel, wajib ada agar form tidak ditolak (error 419) --}}
                        @csrf

                        {{--
                            ===== INPUT NAMA LENGKAP =====
                            mb-3 = margin bawah 3, jarak ke input di bawahnya. Skala 0 sampai 5.
                            Contoh: mb-2 (rapat), mb-4 (renggang).
                        --}}
                        <div class="mb-3">
                            {{-- Label input. for="nama" menghubungkan label ke input ber-id "nama" --}}
                            <label for="nama" class="form-label">Nama Lengkap</label>

                            {{--
                                input-group = menggabungkan ikon dan input dalam satu baris.
                                Contoh: tambah "input-group-lg" -> lebih besar, "input-group-sm" -> lebih kecil.
                            --}}
                            <div class="input-group">
                                {{--
                                    input-group-text = kotak kecil berisi ikon di sisi KIRI input.
                                    Contoh: pindahkan span ini ke setelah <input> -> ikon pindah ke sisi KANAN.
                                    bi-person = ikon orang.
                                --}}
                                <span class="input-group-text"><i class="bi bi-person"></i></span>

                                {{--
                                    form-control = input selebar sisa ruang dengan gaya Bootstrap.
                                    is-invalid (muncul otomatis lewat @error) = border merah saat input bermasalah.
                                    value="{{ old('nama') }}" = mengisi ulang data lama saat validasi gagal,
                                                                supaya user tidak mengetik ulang.
                                    placeholder = teks petunjuk abu-abu. Contoh: "Contoh: Budi Santoso".
                                    required = browser menolak submit kalau kosong.
                                    autofocus = kursor langsung aktif di input ini saat halaman dibuka.
                                --}}
                                <input type="text"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       id="nama"
                                       name="nama"
                                       value="{{ old('nama') }}"
                                       placeholder="Masukkan nama lengkap"
                                       required
                                       autofocus>
                            </div>
                        </div>

                        {{-- ===== INPUT EMAIL ===== --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                {{-- bi-envelope = ikon amplop di sisi kiri input --}}
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>

                                {{-- type="email" = browser mengecek format email (harus ada tanda @) --}}
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="Masukkan email"
                                       required>
                            </div>
                        </div>

                        {{-- ===== INPUT NOMOR TELEPON (opsional) ===== --}}
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">Nomor Telepon</label>
                            <div class="input-group">
                                {{-- bi-telephone = ikon telepon di sisi kiri input --}}
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>

                                {{--
                                    Tidak ada atribut "required" karena kolom ini opsional.
                                    Contoh: tambah "required" -> nomor telepon jadi wajib diisi.
                                    Contoh: ganti type="tel" -> keyboard HP menampilkan angka.
                                --}}
                                <input type="text"
                                       class="form-control @error('no_telp') is-invalid @enderror"
                                       id="no_telp"
                                       name="no_telp"
                                       value="{{ old('no_telp') }}"
                                       placeholder="Masukkan nomor telepon">
                            </div>

                            {{--
                                small = ukuran huruf lebih kecil. text-muted = abu-abu.
                                Contoh: ganti "text-muted" jadi "text-danger" -> tulisan merah.
                            --}}
                            <small class="text-muted">Opsional</small>
                        </div>

                        {{-- ===== INPUT PASSWORD ===== --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                {{-- bi-lock = ikon gembok di sisi kiri input --}}
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>

                                {{--
                                    type="password" = karakter yang diketik disamarkan jadi titik.
                                    Tidak ada value/old() karena password sengaja tidak diisi ulang demi keamanan.
                                    placeholder "Minimal 6 karakter" hanya petunjuk, aturan sebenarnya ada di
                                    validasi controller (min:6). Kalau aturan diubah, ubah teks ini juga.
                                --}}
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Minimal 6 karakter"
                                       required>
                            </div>
                        </div>

                        {{-- ===== INPUT KONFIRMASI PASSWORD ===== --}}
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                {{-- bi-lock-fill = ikon gembok terisi (dibedakan dari input password di atas) --}}
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>

                                {{--
                                    name="password_confirmation" = nama ini WAJIB persis begitu supaya
                                    aturan "confirmed" di validasi Laravel bisa mencocokkan dengan field "password".
                                    Tidak ada @error di sini karena error kecocokan password muncul di field "password".
                                --}}
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Ulangi password"
                                       required>
                            </div>
                        </div>

                        {{--
                            Tombol daftar.
                            btn-primary = biru. Contoh ganti warna: btn-success (hijau), btn-dark (hitam).
                            w-100 = lebar 100% (selebar card). Contoh: hapus -> selebar teks saja,
                                    w-50 mx-auto -> setengah lebar dan di tengah.
                            Contoh: tambah "btn-lg" -> tombol lebih besar.
                            type="submit" = menekan tombol ini mengirim form.
                        --}}
                        <button type="submit" class="btn btn-primary w-100">
                            {{-- me-1 = jarak kanan ikon ke teks. Contoh ganti ikon: bi-person-check --}}
                            <i class="bi bi-check-circle me-1"></i> Daftar
                        </button>
                    </form>

                    {{-- hr = garis pemisah horizontal antara form dan teks login --}}
                    <hr>

                    {{--
                        text-center = rata tengah. mb-0 = tanpa margin bawah supaya tidak ada ruang kosong di dasar card.
                        Contoh: text-end -> rata kanan.
                    --}}
                    <p class="text-center mb-0">
                        Sudah punya akun?
                        {{-- Link menuju halaman login. Contoh: tambah class "fw-bold" -> link jadi tebal --}}
                        <a href="{{ route('login') }}">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection