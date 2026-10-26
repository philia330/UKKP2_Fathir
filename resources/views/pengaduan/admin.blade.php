{{-- Memakai layout utama (layouts/app.blade.php). Semua isi di bawah dimasukkan ke @yield('content') di layout itu. --}}
@extends('layouts.app')

{{-- Judul tab browser. Contoh: ganti jadi 'Semua Pengaduan' kalau mau judul lain. --}}
@section('title', 'Data Pengaduan - Sistem Pengaduan')

@section('content')
{{-- WRAPPER UTAMA (sidebar + konten)
     - d-flex : anak-anaknya berjajar menyamping (sidebar KIRI, konten KANAN) --}}
<div class="d-flex">

    {{-- SIDEBAR: memanggil partials/sidebar-admin.blade.php (menu navigasi admin) --}}
    @include('partials.sidebar-admin')

    {{-- AREA KONTEN UTAMA: halaman ini menampilkan semua pengaduan dari customer untuk pengawasan admin.
         - main-content : class buatan sendiri (di layouts/app.blade.php), hanya mengatur tinggi minimal = tinggi layar
         - flex-grow-1  : konten mengambil sisa lebar layar setelah dipakai sidebar
         - p-4          : padding ukuran 4. Contoh: p-2 = rapat, p-5 = lega, px-4 = hanya kiri-kanan --}}
    <div class="main-content flex-grow-1 p-4">

        {{-- ===== PESAN SUKSES =====
             Muncul hanya jika ada pesan 'success' dari controller (misalnya setelah status berhasil diubah).
             - alert alert-success : kotak hijau. Contoh: alert-danger = merah, alert-warning = kuning, alert-info = biru muda
             - alert-dismissible   : kotak bisa ditutup dengan tombol X
             - fade show           : efek menghilang halus dan tampil saat halaman dimuat
             - role="alert"        : atribut aksesibilitas untuk pembaca layar --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                {{-- btn-close : tombol silang (X) di pojok KANAN alert. data-bs-dismiss="alert" menutup alert saat diklik --}}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ===== KARTU BERISI TABEL PENGADUAN =====
             - card : kotak putih dengan border dan sudut bulat
             - card-body : area dalam kartu dengan padding bawaan
             Contoh: tambah "shadow-sm" pada class card agar ada bayangan tipis, "border-0" untuk menghilangkan garis tepi --}}
        <div class="card">
            <div class="card-body">
                {{-- Judul tabel
                     - h5 : ukuran font sedang. Contoh: h4 = lebih besar
                     - mb-3 : margin bawah ukuran 3 (jarak ke tabel). Contoh: mb-4 = lebih jauh
                     - me-2 : jarak KANAN ikon ke teks --}}
                <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Semua Pengaduan</h5>

                {{-- table-responsive : tabel bisa digeser menyamping di layar kecil (HP) supaya layout tidak rusak
                     Penting karena tabel ini punya 8 kolom --}}
                <div class="table-responsive">
                    {{-- table : gaya tabel dasar Bootstrap
                         table-hover : baris berubah warna saat kursor diarahkan
                         align-middle : isi sel rata TENGAH secara vertikal (supaya foto, badge, dan teks sejajar).
                                        Contoh: align-top = rata ATAS, align-bottom = rata BAWAH
                         Contoh tambahan: table-striped = baris selang-seling warna, table-sm = tabel lebih rapat --}}
                    <table class="table table-hover align-middle">
                        {{-- thead : baris judul kolom. Contoh: <thead class="table-dark"> = header hitam, "table-primary" = biru muda --}}
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Customer</th>
                                <th>Kategori</th>
                                <th>Pengaduan</th>
                                <th>Foto</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                                {{-- Contoh rata kanan satu kolom: tambah class text-end pada th dan td kolom tersebut --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @forelse : seperti foreach, tapi punya bagian @empty jika datanya kosong.
                                 $pengaduans : data pengaduan dari controller (sudah dipecah per halaman / pagination).
                                 Setiap pengaduan ditampung di variabel $item. --}}
                            @forelse ($pengaduans as $item)
                                <tr>
                                    {{-- Nomor urut yang lanjut antar halaman.
                                         $loop->iteration = urutan di halaman ini (1, 2, 3...)
                                         + (halaman sekarang - 1) x jumlah data per halaman
                                         Contoh: halaman 2 dengan 10 data per halaman, baris pertama jadi nomor 11 (bukan 1 lagi) --}}
                                    <td>{{ $loop->iteration + ($pengaduans->currentPage() - 1) * $pengaduans->perPage() }}</td>

                                    {{-- Nama customer lewat relasi user di model Pengaduan --}}
                                    <td>{{ $item->user->nama }}</td>

                                    {{-- KATEGORI: jika ada, tampil sebagai badge abu-abu; jika kosong tampil "-".
                                         badge bg-secondary : label kecil abu-abu.
                                         Contoh: bg-primary = biru, bg-dark = hitam, tambah "rounded-pill" agar berbentuk kapsul --}}
                                    <td>
                                        @if($item->kategori)
                                            <span class="badge bg-secondary">{{ $item->kategori->nama }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Isi pengaduan dipotong 60 karakter supaya tabel tidak terlalu panjang.
                                         Contoh: ganti 60 jadi 100 untuk lebih panjang. Isi lengkap dilihat lewat tombol detail --}}
                                    <td>{{ Str::limit($item->pengaduan, 60) }}</td>

                                    {{-- FOTO: jika ada foto, tampil thumbnail yang bisa diklik (buka gambar penuh di tab baru).
                                         asset('storage/...') : alamat file foto di folder storage publik.
                                         target="_blank" : buka di tab baru.
                                         Style gambar:
                                           width & height 50px : ukuran thumbnail kotak. Contoh: 80px = lebih besar, 30px = lebih kecil
                                           object-fit: cover   : foto dipotong rapi mengisi kotak tanpa gepeng. Contoh: contain = foto utuh tanpa dipotong
                                           border-radius: 4px  : sudut membulat. Contoh: 50% = foto jadi lingkaran, 0 = siku --}}
                                    <td>
                                        @if ($item->foto)
                                            <a href="{{ asset('storage/' . $item->foto) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto pengaduan" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- STATUS: warna badge dipilih sesuai status.
                                         match(...) memilih class warna: menunggu = kuning (teks hitam), diproses = biru muda, selesai = hijau.
                                         Contoh: ganti 'bg-info' jadi 'bg-primary' agar "Diproses" berwarna biru tua.
                                         ucfirst : huruf pertama dibuat kapital (menunggu jadi Menunggu) --}}
                                    <td>
                                        @php
                                            $badge = match($item->status) {
                                                'menunggu' => 'bg-warning text-dark',
                                                'diproses' => 'bg-info',
                                                'selesai' => 'bg-success',
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ ucfirst($item->status) }}</span>
                                    </td>

                                    {{-- Tanggal & jam laporan masuk. Format 'd M Y H:i' contohnya 26 Okt 2026 14:30.
                                         Contoh: 'd/m/Y' = 26/10/2026 (tanpa jam), 'd F Y' = 26 October 2026 (bulan penuh) --}}
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>

                                    {{-- AKSI: tombol lihat detail.
                                         - btn btn-sm btn-outline-primary : tombol kecil dengan garis biru dan latar kosong.
                                           Contoh: btn-primary = biru penuh, btn-outline-success = garis hijau, hapus btn-sm = ukuran normal
                                         - onclick="showDetail(id)" : memanggil fungsi JavaScript di modal-detail untuk membuka pop-up detail
                                         - title : teks kecil yang muncul saat kursor diarahkan ke tombol
                                         - bi-eye : ikon mata. Contoh: bi-search atau bi-info-circle --}}
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showDetail({{ $item->id }})" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            {{-- Jika tidak ada data pengaduan sama sekali, tampil satu baris pesan.
                                 colspan="8" : sel melebar menutupi 8 kolom (sesuai jumlah kolom tabel; ubah jika kolom ditambah/dikurangi).
                                 text-center : teks rata TENGAH. Contoh: text-start = KIRI, text-end = KANAN
                                 text-muted : warna abu-abu --}}
                            @empty
                                <tr><td colspan="8" class="text-center text-muted">Belum ada pengaduan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION: tombol halaman (1, 2, 3, Next, dst) otomatis dari Laravel.
                     Contoh: tambah class "justify-content-center" di pembungkus agar tombol halaman di TENGAH --}}
                {{ $pengaduans->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL: memanggil file pop-up detail pengaduan (resources/views/pengaduan/_modal-detail.blade.php).
     File itu juga berisi fungsi showDetail() yang dipanggil tombol mata di atas. Harus tetap di-include supaya tombol berfungsi. --}}
@include('pengaduan._modal-detail')
@endsection