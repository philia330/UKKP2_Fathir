{{-- Komentar Blade ini tidak muncul di View Page Source. Di dalam style dipakai komentar CSS biasa. --}}

{{-- Tipe dokumen HTML5 --}}
<!DOCTYPE html>
{{-- lang="id" : bahasa halaman Indonesia (membantu browser & mesin pencari). Contoh: "en" untuk Inggris --}}
<html lang="id">
<head>
    {{-- Encoding UTF-8 agar huruf/simbol Indonesia tampil benar --}}
    <meta charset="UTF-8">
    {{-- Membuat halaman responsif di HP (lebar mengikuti lebar layar). Jangan dihapus, nanti tampilan HP jadi kecil sekali --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Token keamanan CSRF Laravel, dipakai form/AJAX agar request POST diterima --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Judul tab browser. Mengambil @section('title') dari tiap view; kalau tidak ada, pakai 'Sistem Pengaduan' --}}
    <title>@yield('title', 'Sistem Pengaduan')</title>

    {{-- Bootstrap 5 CSS: sumber semua class seperti d-flex, p-4, text-primary, card, badge, dll.
         Contoh: ganti 5.3.3 ke versi lain untuk mengubah versi Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons: sumber ikon "bi bi-..." (sidebar, tombol, kartu statistik) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- CSS buatan sendiri: styling khusus sidebar, kartu statistik, dan layout utama --}}
    <style>
        /* ===== VARIABEL WARNA =====
           Dipakai ulang di bawah lewat var(--nama). Ubah di sini, semua tempat yang memakainya ikut berubah. */
        :root {
            /* Warna utama (menu sidebar aktif). Contoh: #dc3545 = merah, #198754 = hijau, #6f42c1 = ungu */
            --primary-color: #0d6efd;
            /* Warna latar sidebar. Contoh: #212529 = hitam (jangan lupa ubah warna teks link jadi putih), #e3f2fd = biru muda */
            --sidebar-bg: #f8f9fa;
        }

        body {
            /* Tinggi minimal halaman = 100% tinggi layar, supaya halaman pendek tetap penuh.
               Contoh: 100vh = penuh, 50vh = setengah layar */
            min-height: 100vh;
        }

        /* ===== SIDEBAR (panel menu kiri) ===== */
        .sidebar {
            /* Warna latar, mengambil dari variabel --sidebar-bg di atas */
            background-color: var(--sidebar-bg);
            /* Tinggi minimal sidebar = tinggi layar penuh */
            min-height: 100vh;
            /* Garis tipis di sisi KANAN sidebar sebagai pemisah dengan konten.
               Contoh: border-left = garis di sisi KIRI, 3px solid red = lebih tebal & merah, none = tanpa garis */
            border-right: 1px solid #dee2e6;
        }

        /* Link menu di dalam sidebar */
        .sidebar .nav-link {
            /* Warna teks menu. Contoh: white untuk teks putih (cocok untuk sidebar gelap) */
            color: #333;
            /* Jarak dalam: 0.75rem ATAS-BAWAH, 1rem KIRI-KANAN.
               Contoh: 1rem 1.5rem = menu lebih besar/lega, 0.5rem 0.75rem = lebih kecil/rapat */
            padding: 0.75rem 1rem;
            /* Kelengkungan sudut. Contoh: 0 = siku, 1rem = sangat bulat, 50rem = bentuk kapsul */
            border-radius: 0.25rem;
            /* Jarak luar: 0.25rem ATAS-BAWAH, 0 KIRI-KANAN (antar menu).
               Contoh: 0.5rem 0 = antar menu lebih jauh, 0.25rem 0.5rem = ada jarak di kiri-kanan */
            margin: 0.25rem 0;
        }

        /* Saat kursor diarahkan ke menu */
        .sidebar .nav-link:hover {
            /* Warna latar saat hover. Contoh: #cfe2ff = biru muda */
            background-color: #e9ecef;
        }

        /* Menu yang sedang dibuka (halaman aktif) */
        .sidebar .nav-link.active {
            /* Latar menu aktif, mengikuti --primary-color */
            background-color: var(--primary-color);
            /* Warna teks menu aktif. Contoh: yellow */
            color: white;
        }

        /* Ikon di depan teks menu */
        .sidebar .nav-link i {
            /* Lebar area ikon supaya teks semua menu sejajar rata kiri.
               Contoh: 32px = jarak ikon ke teks lebih lebar */
            width: 24px;
        }

        /* ===== AREA KONTEN ===== */
        .main-content {
            /* Tinggi minimal konten = tinggi layar. (Di sini TIDAK ada warna latar.
               Contoh: tambahkan "background-color: #f4f6f9;" untuk latar abu muda di area konten) */
            min-height: 100vh;
        }

        .navbar-brand {
            /* Ketebalan huruf logo/nama brand. Contoh: 400 = normal, 700 = tebal sekali */
            font-weight: 600;
        }

        /* ===== KARTU STATISTIK (Total, Menunggu, Selesai) ===== */
        .card-stat {
            /* Hilangkan garis tepi kartu bawaan Bootstrap. Contoh: "1px solid #0d6efd" = garis biru */
            border: none;
            /* Sudut kartu membulat. Contoh: 0 = siku, 1.5rem = sangat bulat */
            border-radius: 0.75rem;
            /* Bayangan: geser-X(0) geser-Y(0.125rem) blur(0.25rem) warna hitam transparan 7.5%.
               Contoh: 0 0.5rem 1rem rgba(0,0,0,0.15) = bayangan lebih besar & tegas */
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            /* Animasi halus 0.2 detik saat kartu bergerak (hover). Contoh: 0.5s = lebih lambat */
            transition: transform 0.2s;
        }

        /* Saat kursor di atas kartu */
        .card-stat:hover {
            /* Kartu naik 2px ke ATAS. Contoh: translateY(-8px) = naik lebih tinggi, translateY(2px) = turun,
               translateX(5px) = geser ke KANAN, scale(1.05) = membesar 5% */
            transform: translateY(-2px);
        }

        /* Ikon besar di kanan kartu statistik */
        .card-stat .icon {
            /* Ukuran ikon. Contoh: 3rem = lebih besar, 1.5rem = lebih kecil */
            font-size: 2rem;
            /* Transparansi ikon (1 = pekat, 0 = hilang). Contoh: 1 = warna penuh, 0.4 = pudar */
            opacity: 0.8;
        }

        /* Titik sisip untuk CSS tambahan dari tiap view.
           Contoh: di view, isi section bernama custom_styles dengan aturan CSS biasa, misalnya .card-stat { background: #fff3cd; } */
        @yield('custom_styles')
    </style>
</head>
<body>
    {{-- Tempat isi halaman: tiap view yang pakai @extends('layouts.app') mengisinya lewat @section('content') --}}
    @yield('content')

    {{-- Bootstrap 5 JS (bundle sudah termasuk Popper): membuat dropdown, modal, tooltip, collapse, dll berfungsi.
         Diletakkan paling bawah supaya halaman tampil dulu baru script dimuat --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>