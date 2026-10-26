{{--
    SIDEBAR PETUGAS (partial): dipanggil lewat @include('partials.sidebar-petugas') dari view petugas, misalnya dashboard petugas.
    Fungsinya membantu petugas menangani laporan dan data customer.
    - nav            : tag HTML khusus area navigasi
    - sidebar        : class buatan sendiri di layouts/app.blade.php (latar abu muda, tinggi penuh layar, garis di sisi kanan)
    - col-md-2       : lebar 2 dari 12 kolom di layar medium ke atas (sekitar 16%).
                       Contoh: col-md-3 = sidebar lebih lebar, col-md-1 = sangat sempit
    - p-3            : padding (jarak isi ke tepi) ukuran 3 dari skala 0-5.
                       Contoh: p-2 = lebih rapat, p-4 = lebih lega, px-3 = hanya kiri-kanan, py-3 = hanya atas-bawah
--}}
<nav class="sidebar col-md-2 p-3">

    {{-- JUDUL SIDEBAR: penanda bahwa ini panel petugas.
         - h5   : ukuran font sedang. Contoh: ganti tag jadi h4 untuk lebih besar, h6 untuk lebih kecil
         - mb-4 : margin bawah ukuran 4 (jarak ke daftar menu). Contoh: mb-2 = lebih dekat, mb-5 = lebih jauh
         - bi bi-person-badge : ikon orang dengan lencana (Bootstrap Icons). Contoh: ganti jadi bi-headset untuk ikon headset
         - me-2 : margin KANAN ikon ukuran 2 (jarak ikon ke teks). Contoh: me-3 = lebih jauh, ms-2 = jarak di KIRI ikon --}}
    <h5 class="mb-4">
        <i class="bi bi-person-badge me-2"></i>Petugas Panel
    </h5>

    {{-- DAFTAR MENU
         - nav          : gaya daftar navigasi Bootstrap (menghapus bullet list)
         - flex-column  : menu tersusun dari ATAS ke BAWAH.
                          Contoh: hapus "flex-column" supaya menu berjajar menyamping (KIRI ke KANAN) --}}
    <ul class="nav flex-column">

        {{-- MENU 1: DASHBOARD. Ringkasan pengaduan yang perlu ditangani.
             - nav-link : gaya link menu (padding, margin, hover diatur di CSS layout)
             - Kondisi request()->routeIs(...) : jika route saat ini 'petugas.dashboard', tambahkan class 'active'
               sehingga menu tampil biru/menyala dan petugas tahu sedang di halaman mana.
               Contoh: ganti nama route-nya untuk menandai menu lain
             - href : alamat tujuan dari nama route
             - bi-speedometer2 : ikon speedometer. me-2 : jarak kanan ikon ke teks --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}" href="{{ route('petugas.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>

        {{-- MENU 2: TAMBAH CUSTOMER. Petugas menambah atau mengelola data customer.
             Tanda bintang di 'petugas.customers.*' artinya semua route yang diawali petugas.customers
             (index, create, edit, dst) tetap menandai menu ini aktif.
             Catatan: href mengarah ke halaman daftar (index), bukan langsung ke form tambah.
             Contoh: ganti teks menu jadi "Customer" kalau mau namanya lebih sesuai dengan halaman daftar.
             bi-person-plus : ikon orang dengan tanda tambah. --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('petugas.customers.*') ? 'active' : '' }}" href="{{ route('petugas.customers.index') }}">
                <i class="bi bi-person-plus me-2"></i>Tambah Customer
            </a>
        </li>

        {{-- MENU 3: PENGADUAN. Semua pengaduan yang harus dipantau dan diperbarui statusnya.
             Memakai 'petugas.pengaduan.*' jadi tetap menyala di halaman detail atau ubah status. --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('petugas.pengaduan.*') ? 'active' : '' }}" href="{{ route('petugas.pengaduan.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i>Pengaduan
            </a>
        </li>

        {{-- MENU 4: PROFILE. Data akun petugas yang sedang login.
             Tanpa tanda bintang karena hanya satu halaman (route 'petugas.profile'). --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('petugas.profile') ? 'active' : '' }}" href="{{ route('petugas.profile') }}">
                <i class="bi bi-person-circle me-2"></i>Profile
            </a>
        </li>

        {{-- MENU LOGOUT
             - mt-3 : margin ATAS ukuran 3 supaya terpisah dari menu di atasnya.
                      Contoh: mt-5 = lebih jauh, mt-0 = menempel, mb-3 = jarak di bawah, ms-3 = jarak di kiri
             - Memakai form method POST (bukan link biasa) agar aman dan tidak bisa dipicu lewat link biasa. --}}
        <li class="nav-item mt-3">
            <form action="{{ route('logout') }}" method="POST">
                {{-- Token keamanan CSRF. Wajib ada di setiap form POST Laravel, kalau dihapus muncul error 419 --}}
                @csrf
                {{-- Tombol dibuat tampil seperti link menu:
                     - nav-link       : gaya sama dengan menu lain
                     - border-0       : hilangkan garis tepi tombol. Contoh: hapus agar tombol punya garis
                     - bg-transparent : latar tombol transparan (tidak abu-abu bawaan tombol).
                                        Contoh: ganti bg-light = latar abu muda, bg-danger = latar merah
                     - w-100          : lebar 100% mengikuti sidebar. Contoh: w-50 = setengah lebar, w-auto = sesuai isi
                     - text-start     : teks rata KIRI. Contoh: text-center = TENGAH, text-end = rata KANAN
                     - text-danger    : teks merah (menandai aksi keluar).
                                        Contoh: text-dark = hitam, text-secondary = abu, text-warning = kuning --}}
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </li>
    </ul>
</nav>