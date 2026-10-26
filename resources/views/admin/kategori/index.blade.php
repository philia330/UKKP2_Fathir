{{-- Memakai layout utama (layouts/app.blade.php) sebagai kerangka halaman --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Data Kategori - Sistem Pengaduan' --}}
@section('title', 'Kategori - Sistem Pengaduan')

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
        Halaman ini berfungsi sebagai daftar master kategori laporan yang bisa dipakai customer saat membuat pengaduan.

        main-content = class buatan sendiri, atur lebar/margin di file CSS (cari ".main-content").
        flex-grow-1  = konten melebar mengisi sisa ruang setelah sidebar.
                       Contoh: hapus -> konten hanya selebar isinya.
        p-4          = jarak dalam (padding) ke semua sisi. Skala 0 sampai 5.
                       Contoh: p-2 (lebih rapat), p-5 (lebih lega), px-4 (kiri-kanan saja), py-4 (atas-bawah saja).
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{-- Pesan sukses dari controller, misalnya setelah tambah/edit/hapus kategori --}}
        @if (session('success'))
            {{--
                alert alert-success = kotak notifikasi berwarna hijau.
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

            {{--
                card-header = bagian judul di bagian atas card.
                d-flex                = isi header (judul dan tombol) berjajar ke samping.
                justify-content-between = judul di kiri, tombol di kanan (ujung ke ujung).
                                          Contoh: justify-content-start (semua ke kiri),
                                          justify-content-end (semua ke kanan), justify-content-center (tengah).
                align-items-center    = isi header rata tengah secara vertikal.
                                        Contoh: align-items-start (rata atas), align-items-end (rata bawah).
                Contoh warna: tambah "bg-primary text-white" -> header biru dengan teks putih.
            --}}
            <div class="card-header d-flex justify-content-between align-items-center">

                {{--
                    h5 = ukuran judul. Contoh: h4 (lebih besar), h6 (lebih kecil).
                    mb-0 = margin bawah 0 supaya judul pas rata tengah dengan tombol.
                --}}
                <h5 class="mb-0">
                    {{--
                        bi bi-tags = ikon label/tag.
                        me-2 = jarak kanan ikon ke teks. Contoh ganti ikon: bi-list-ul, bi-folder.
                    --}}
                    <i class="bi bi-tags me-2"></i>Daftar Kategori
                </h5>

                {{--
                    Tombol menuju halaman tambah kategori.
                    btn-primary = tombol biru. Contoh ganti warna: btn-success (hijau), btn-dark (hitam).
                    btn-sm = ukuran kecil. Contoh: hapus btn-sm -> ukuran normal, ganti btn-lg -> besar.
                    Contoh: btn-outline-primary -> hanya garis tepi biru tanpa isi warna.
                --}}
                <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary btn-sm">
                    {{-- Ikon plus. me-1 = jarak kanan ikon ke teks --}}
                    <i class="bi bi-plus-circle me-1"></i>Tambah Kategori
                </a>
            </div>

            {{-- card-body = area isi card (sudah punya padding bawaan) --}}
            <div class="card-body">

                {{--
                    table-responsive = tabel bisa digeser (scroll) ke samping di layar kecil/HP
                    supaya tidak merusak tampilan halaman.
                    Contoh: table-responsive-md -> scroll hanya aktif di layar di bawah ukuran md.
                --}}
                <div class="table-responsive">

                    {{--
                        table       = gaya dasar tabel Bootstrap.
                        table-hover = baris berubah warna saat kursor diarahkan.
                        Contoh tambahan: table-striped (baris selang-seling warna),
                        table-bordered (ada garis di semua sisi sel), table-sm (lebih rapat).
                    --}}
                    <table class="table table-hover">

                        {{-- Bagian judul kolom tabel. Contoh: tambah class "table-dark" pada thead -> header hitam --}}
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Jumlah Pengaduan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        {{-- Bagian isi tabel, diisi data dari database --}}
                        <tbody>
                            {{--
                                @forelse = perulangan data, sekalian menyediakan tampilan kalau data kosong (@empty).
                                $index dipakai untuk nomor urut.
                            --}}
                            @forelse($kategoris as $index => $kategori)
                            <tr>
                                {{--
                                    Nomor urut yang tetap lanjut antar halaman pagination.
                                    firstItem() = nomor data pertama di halaman ini (halaman 2 mulai dari 11, dst).
                                --}}
                                <td>{{ $kategoris->firstItem() + $index }}</td>

                                {{-- Nama kategori dari database --}}
                                <td>{{ $kategori->nama }}</td>

                                {{-- Deskripsi kategori. Kalau kosong (null) tampil tanda "-" --}}
                                <td>{{ $kategori->deskripsi ?? '-' }}</td>

                                {{-- Jumlah pengaduan yang memakai kategori ini (lewat relasi pengaduans di model) --}}
                                <td>{{ $kategori->pengaduans->count() }}</td>

                                {{-- Kolom tombol aksi: lihat detail, edit, hapus --}}
                                <td>
                                    {{--
                                        Tombol detail.
                                        btn-sm = ukuran kecil. btn-info = biru muda.
                                        Contoh ganti warna: btn-primary (biru tua), btn-secondary (abu-abu).
                                        title = tulisan kecil yang muncul saat kursor diarahkan (tooltip).
                                    --}}
                                    <a href="{{ route('admin.kategori.show', $kategori) }}" class="btn btn-sm btn-info" title="Detail">
                                        {{-- Ikon mata. Contoh ganti ikon: bi-search, bi-info-circle --}}
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Tombol edit. btn-warning = kuning. Contoh: btn-success (hijau) --}}
                                    <a href="{{ route('admin.kategori.edit', $kategori) }}" class="btn btn-sm btn-warning" title="Edit">
                                        {{-- Ikon pensil. Contoh ganti ikon: bi-pencil-square --}}
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{--
                                        Form hapus kategori (hapus harus lewat form karena memakai method DELETE).
                                        d-inline = form tampil sebaris dengan tombol lain (tidak turun ke baris baru).
                                        Contoh: hapus "d-inline" -> tombol Hapus pindah ke baris bawah.
                                    --}}
                                    <form action="{{ route('admin.kategori.destroy', $kategori) }}" method="POST" class="d-inline">
                                        {{-- Token keamanan Laravel, wajib ada agar tidak error 419 --}}
                                        @csrf
                                        {{-- Mengubah method POST menjadi DELETE sesuai aturan route destroy --}}
                                        @method('DELETE')

                                        {{--
                                            Tombol hapus. btn-danger = merah.
                                            onclick confirm = muncul kotak konfirmasi sebelum form dikirim.
                                            Kalau user klik "Batal", penghapusan dibatalkan.
                                            Contoh: ubah teks konfirmasi jadi "Kategori akan dihapus permanen, lanjutkan?"
                                        --}}
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                            {{-- Ikon tempat sampah --}}
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Tampilan kalau data kategori masih kosong --}}
                            @empty
                            <tr>
                                {{--
                                    colspan="5" = sel menggabungkan 5 kolom (sesuai jumlah kolom tabel).
                                    Kalau kolom tabel ditambah/dikurangi, angka ini ikut disesuaikan.
                                    text-center = teks rata tengah. Contoh: text-start (kiri), text-end (kanan).
                                    text-muted = warna teks abu-abu.
                                    Contoh: text-danger (merah), fw-bold (tebal), fs-5 (lebih besar).
                                --}}
                                <td colspan="5" class="text-center text-muted">Belum ada kategori.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{--
                    Wadah nomor halaman (pagination).
                    justify-content-center = nomor halaman di tengah.
                    Contoh: justify-content-end -> di kanan, justify-content-start -> di kiri.
                    Contoh: tambah "mt-3" -> ada jarak di atas pagination.
                --}}
                <div class="d-flex justify-content-center">
                    {{-- Menampilkan tombol halaman (1, 2, 3, Next, Previous) dari paginate() di controller --}}
                    {{ $kategoris->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection