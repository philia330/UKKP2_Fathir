{{--
    SIDEBAR CUSTOMER (partial): dipanggil lewat @include dari view customer. Hanya berisi menu navigasi kiri untuk role customer.
    - nav            : tag HTML khusus area navigasi
    - sidebar        : class buatan sendiri di layouts/app.blade.php (latar abu muda, tinggi penuh layar, garis di sisi kanan)
    - col-md-2       : lebar 2 dari 12 kolom di layar medium ke atas (sekitar 16%).
                       Contoh: col-md-3 = sidebar lebih lebar, col-md-1 = sangat sempit
    - p-3            : padding (jarak isi ke tepi) ukuran 3 dari skala 0-5.
                       Contoh: p-2 = lebih rapat, p-4 = lebih lega, px-3 = hanya kiri-kanan, py-3 = hanya atas-bawah
--}}
<nav class="sidebar col-md-2 p-3">

    {{-- JUDUL SIDEBAR: penanda bahwa ini panel customer.
         - h5   : ukuran font sedang. Contoh: ganti tag jadi h4 untuk lebih besar, h6 untuk lebih kecil
         - mb-4 : margin bawah ukuran 4 (jarak ke daftar menu). Contoh: mb-2 = lebih dekat, mb-5 = lebih jauh
         - bi bi-person : ikon orang (Bootstrap Icons). Contoh: ganti jadi bi-house untuk ikon rumah
         - me-2 : margin KANAN ikon ukuran 2 (jarak ikon ke teks). Contoh: me-3 = lebih jauh, ms-2 = jarak di KIRI ikon --}}
    <h5 class="mb-4">
        <i class="bi bi-person me-2"></i>Customer Panel
    </h5>

    {{-- DAFTAR MENU
         - nav          : gaya daftar navigasi Bootstrap (menghapus bullet list)
         - flex-column  : menu tersusun dari ATAS ke BAWAH.
                          Contoh: hapus "flex-column" supaya menu berjajar menyamping (KIRI ke KANAN) --}}
    <ul class="nav flex-column">

        {{-- MENU 1: DASHBOARD. Halaman ringkasan untuk customer.
             - nav-link : gaya link menu (padding, margin, hover diatur di CSS layout)
             - Kondisi request()->routeIs(...) : jika route saat ini 'customer.dashboard', tambahkan class 'active'
               sehingga menu tampil biru/menyala dan user tahu sedang di halaman mana.
               Contoh: ganti nama route-nya untuk menandai menu lain
             - href : alamat tujuan dari nama route
             - bi-speedometer2 : ikon speedometer. me-2 : jarak kanan ikon ke teks --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>

        {{-- MENU 2: PENGADUAN SAYA. Daftar laporan yang pernah dibuat customer sendiri.
             Memakai route 'customer.pengaduan.index' (tanpa tanda bintang), jadi menu ini hanya menyala di halaman daftar.
             Contoh: ubah jadi 'customer.pengaduan.*' supaya juga menyala di halaman lain yang berawalan itu,
             tapi hati-hati: menu "Buat Pengaduan" di bawah akan ikut menyala bersamaan. --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.pengaduan.index') ? 'active' : '' }}" href="{{ route('customer.pengaduan.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i>Pengaduan Saya
            </a>
        </li>

        {{-- MENU 3: BUAT PENGADUAN. Mengarah ke form pengaduan baru.
             bi-plus-circle : ikon tambah (+) dalam lingkaran, menandakan aksi membuat data baru. --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.pengaduan.create') ? 'active' : '' }}" href="{{ route('customer.pengaduan.create') }}">
                <i class="bi bi-plus-circle me-2"></i>Buat Pengaduan
            </a>
        </li>

        {{-- MENU 4: PROFILE. Data akun customer yang sedang login. --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}">
                <i class="bi bi-person-circle me-2"></i>Profile
            </a>
        </li>

        {{-- MENU LOGOUT
             - mt-3 : margin ATAS ukuran 3 supaya terpisah dari menu di atasnya.
                      Contoh: mt-5 = lebih jauh, mt-0 = menempel, mb-3 = jarak di bawah, ms-3 = jarak di kiri
             - Memakai form method POST (bukan link biasa) demi keamanan, sesuai aturan Laravel. --}}
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