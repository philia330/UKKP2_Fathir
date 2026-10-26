{{-- Memakai layout utama (layouts/app.blade.php) sebagai kerangka halaman --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Daftar Customer - Sistem Pengaduan' --}}
@section('title', 'Data Customer - Sistem Pengaduan')

{{-- Isi utama halaman yang dimasukkan ke @yield('content') di layout --}}
@section('content')

{{--
    d-flex = sidebar dan konten sejajar ke samping (kiri-kanan).
    Contoh: hapus "d-flex" -> sidebar dan konten jadi tersusun atas-bawah.
    Contoh: "d-flex flex-row-reverse" -> sidebar pindah ke sisi kanan.
--}}
<div class="d-flex">

    {{-- Memanggil menu sidebar PETUGAS dari partials/sidebar-petugas.blade.php --}}
    @include('partials.sidebar-petugas')

    {{--
        Halaman ini menampilkan daftar customer yang bisa dikelola oleh petugas.

        main-content = class buatan sendiri, atur lebar/margin di file CSS (cari ".main-content").
        flex-grow-1  = konten melebar mengisi sisa ruang setelah sidebar.
                       Contoh: hapus -> konten hanya selebar isinya.
        p-4          = jarak dalam (padding) ke semua sisi. Skala 0 sampai 5.
                       Contoh: p-2 (lebih rapat), p-5 (lebih lega), px-4 (kiri-kanan saja), py-4 (atas-bawah saja).
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{-- Pesan sukses dari controller, misalnya setelah berhasil menambah customer --}}
        @if (session('success'))
            {{--
                alert alert-success = kotak notifikasi hijau.
                Contoh ganti warna: alert-danger (merah), alert-warning (kuning), alert-info (biru).
                alert-dismissible = alert bisa ditutup dengan tombol X.
                fade show = alert muncul dengan efek transisi halus.
                role="alert" = penanda aksesibilitas untuk screen reader.
            --}}
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{--
                    bi bi-check-circle = ikon centang dari Bootstrap Icons.
                    me-2 = margin kanan (end) 2, jarak ikon ke teks.
                    Contoh: me-1 (lebih dekat), me-3 (lebih jauh). Ganti ikon: bi-info-circle.
                --}}
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                {{-- Tombol X untuk menutup alert. data-bs-dismiss="alert" = perintah Bootstrap untuk menutup --}}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{--
            card = kotak putih dengan border dan sudut membulat.
            Contoh: tambah "shadow" -> ada bayangan. Tambah "border-0" -> tanpa garis tepi.
            Contoh: tambah "mx-auto" + "w-75" -> card selebar 75% dan berada di tengah.
        --}}
        <div class="card">

            {{-- card-body = area isi card (sudah punya padding bawaan). Di sini judul ditaruh di body, bukan card-header --}}
            <div class="card-body">

                {{--
                    Baris judul + tombol tambah.
                    d-flex                  = judul dan tombol berjajar ke samping.
                    justify-content-between = judul di kiri, tombol di kanan (ujung ke ujung).
                                              Contoh: justify-content-start (semua ke kiri),
                                              justify-content-end (semua ke kanan), justify-content-center (tengah).
                    align-items-center      = isi rata tengah secara vertikal.
                                              Contoh: align-items-start (rata atas), align-items-end (rata bawah).
                    mb-3                    = margin bawah 3, jarak ke tabel. Contoh: mb-1 (rapat), mb-5 (renggang).
                --}}
                <div class="d-flex justify-content-between align-items-center mb-3">

                    {{--
                        h5 = ukuran judul. Contoh: h4 (lebih besar), h6 (lebih kecil).
                        mb-0 = margin bawah 0 supaya judul rata tengah dengan tombol.
                    --}}
                    <h5 class="mb-0">
                        {{-- bi-people = ikon orang banyak. me-2 = jarak kanan ikon ke teks. Contoh ganti ikon: bi-person-lines-fill --}}
                        <i class="bi bi-people me-2"></i>Daftar Customer
                    </h5>

                    {{--
                        Tombol menuju halaman tambah customer.
                        btn-primary = biru. Contoh ganti warna: btn-success (hijau), btn-dark (hitam).
                        btn-sm = ukuran kecil. Contoh: hapus btn-sm -> normal, ganti btn-lg -> besar.
                    --}}
                    <a href="{{ route('petugas.customers.create') }}" class="btn btn-primary btn-sm">
                        {{-- bi-plus-lg = ikon plus. me-1 = jarak kanan ikon ke teks --}}
                        <i class="bi bi-plus-lg me-1"></i>Tambah Customer
                    </a>
                </div>

                {{--
                    table-responsive = tabel bisa digeser (scroll) ke samping di layar kecil/HP
                    supaya tidak merusak tampilan halaman.
                    Contoh: table-responsive-md -> scroll hanya aktif di layar di bawah ukuran md.
                --}}
                <div class="table-responsive">

                    {{--
                        table       = gaya dasar tabel Bootstrap.
                        table-hover = baris berubah warna saat kursor diarahkan.
                        align-middle = isi sel rata tengah secara vertikal.
                                       Contoh: align-top (rata atas), align-bottom (rata bawah).
                        Contoh tambahan: table-striped (baris selang-seling warna),
                        table-bordered (ada garis di semua sel), table-sm (lebih rapat).
                    --}}
                    <table class="table table-hover align-middle">

                        {{-- Judul kolom tabel. Contoh: tambah class "table-dark" pada thead -> header hitam --}}
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                            </tr>
                        </thead>

                        {{-- Isi tabel, diisi data dari database --}}
                        <tbody>
                            {{-- @forelse = perulangan data, sekalian menyediakan tampilan kalau data kosong (@empty) --}}
                            @forelse ($customers as $customer)
                                <tr>
                                    {{--
                                        Nomor urut yang tetap lanjut antar halaman pagination.
                                        $loop->iteration = urutan di halaman ini (mulai 1).
                                        (currentPage() - 1) * perPage() = jumlah data di halaman sebelumnya.
                                        Contoh: halaman 2 dengan 10 data/halaman -> nomor mulai dari 11.
                                    --}}
                                    <td>{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>

                                    {{-- Nama customer dari database --}}
                                    <td>{{ $customer->nama }}</td>

                                    {{-- Email customer --}}
                                    <td>{{ $customer->email }}</td>

                                    {{-- Nomor telepon. Kalau kosong (null) tampil tanda "-" --}}
                                    <td>{{ $customer->no_telp ?? '-' }}</td>
                                </tr>
                            @empty
                                {{--
                                    Tampilan kalau data customer masih kosong.
                                    colspan="4" = sel menggabungkan 4 kolom (sesuai jumlah kolom tabel).
                                    Kalau kolom tabel ditambah/dikurangi, angka ini ikut disesuaikan.
                                    text-center = teks rata tengah. Contoh: text-start (kiri), text-end (kanan).
                                    text-muted = warna teks abu-abu. Contoh: text-danger (merah), fw-bold (tebal).
                                --}}
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data customer.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{--
                    Menampilkan tombol halaman (1, 2, 3, Next, Previous) dari paginate() di controller.
                    Di sini tidak dibungkus div, jadi posisinya di kiri (bawaan).
                    Contoh: bungkus dengan <div class="d-flex justify-content-center"> -> pagination di tengah.
                --}}
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection