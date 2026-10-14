<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengaduan')</title>

    <!-- Bootstrap 5 CSS -->
    <!-- CSS utama dibuat dari Bootstrap agar tampilan halaman konsisten di semua role. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <!-- Ikon icon digunakan untuk sidebar dan tombol agar terlihat lebih jelas. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <!-- Semua styling khusus untuk sidebar, card statistik, dan layout utama ditaruh di sini. -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-bg: #f8f9fa;
        }

        body {
            min-height: 100vh;
        }

        .sidebar {
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            margin: 0.25rem 0;
        }

        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .sidebar .nav-link i {
            width: 24px;
        }

        .main-content {
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .card-stat {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }

        .card-stat:hover {
            transform: translateY(-2px);
        }

        .card-stat .icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        @yield('custom_styles')
    </style>
</head>
<body>
    <!-- Konten utama halaman akan diisi oleh setiap view yang memakai layout ini. -->
    @yield('content')

    <!-- Bootstrap 5 JS -->
    <!-- Script ini memungkinkan fitur dropdown, modal, dan komponen UI Bootstrap bekerja. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
