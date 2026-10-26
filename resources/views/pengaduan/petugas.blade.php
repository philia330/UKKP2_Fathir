{{--
    =====================================================================
    CATATAN UMUM
    =====================================================================
    - Komentar bentuk {{-- ... --}} adalah komentar Blade. Komentar ini TIDAK
      ikut tampil di HTML / "View Page Source", jadi aman untuk penjelasan.
    - Di file ini kamu akan menemukan dua jenis CSS:
        1. Class Bootstrap (contoh: p-4, mb-3, bg-success) -> tinggal ganti nama class-nya.
        2. Inline style (contoh: style="width: 50px;")     -> ganti nilainya langsung.
    - Skala spasi Bootstrap: 0 = 0px, 1 = 4px, 2 = 8px, 3 = 16px, 4 = 24px, 5 = 48px.
    =====================================================================
--}}

{{-- Memakai layout utama "layouts.app" (isi: navbar, <head>, CSS/JS Bootstrap, dll). --}}
@extends('layouts.app')

{{-- Mengisi judul tab browser. Contoh ubah: 'Daftar Pengaduan - Sistem Pengaduan' --}}
@section('title', 'Data Pengaduan - Sistem Pengaduan')

{{-- Awal isi halaman. Bagian ini akan dimasukkan ke @yield('content') di layouts.app --}}
@section('content')

{{--
    WADAH UTAMA: sidebar (kiri) + konten (kanan) dibuat berdampingan.
    d-flex = susunan anak elemen menjadi sejajar horizontal (kiri-kanan).
    Contoh ubah:
      - "d-flex flex-column"  -> sidebar pindah ke atas, konten di bawah (vertikal)
      - "d-flex flex-row-reverse" -> sidebar pindah ke KANAN, konten di KIRI
--}}
<div class="d-flex">

    {{-- Memasukkan file sidebar khusus petugas (resources/views/partials/sidebar-petugas.blade.php) --}}
    @include('partials.sidebar-petugas')

    {{-- Halaman ini menampilkan daftar pengaduan yang bisa dipantau dan diproses oleh petugas. --}}

    {{--
        AREA KONTEN UTAMA (sebelah sidebar).
        main-content = class CSS buatan sendiri (cek di file css/layout kamu).
        flex-grow-1  = lebar otomatis mengisi sisa ruang setelah sidebar.
                       Contoh: hapus class ini -> konten hanya selebar isinya.
        p-4          = padding (jarak dalam) 24px di semua sisi.
                       Contoh ubah:
                         p-2   -> lebih rapat (8px)
                         p-5   -> lebih lega (48px)
                         px-5  -> hanya kiri-kanan yang lebar
                         py-3  -> hanya atas-bawah (16px)
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{--
            NOTIFIKASI SUKSES.
            Muncul hanya jika controller mengirim session('success'), contoh:
            return redirect()->back()->with('success', 'Status berhasil diubah');
        --}}
        @if(session('success'))
            {{--
                alert alert-success    = kotak peringatan berwarna HIJAU.
                  Contoh ganti warna:
                    alert-danger  -> merah   (untuk error)
                    alert-warning -> kuning  (untuk peringatan)
                    alert-info    -> biru muda (untuk info)
                alert-dismissible      = memberi ruang untuk tombol close (X) di kanan.
                fade show              = animasi memudar saat ditutup & tampil di awal.
                role="alert"           = untuk aksesibilitas (screen reader).
            --}}
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{-- Menampilkan teks pesan dari session --}}
                {{ session('success') }}

                {{-- Tombol X untuk menutup notifikasi (data-bs-dismiss="alert" = fitur bawaan Bootstrap JS) --}}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{--
            KARTU PEMBUNGKUS TABEL.
            card      = kotak putih dengan border dan sudut membulat.
            card-body = ruang padding di dalam kartu.
            Contoh ubah:
              - "card shadow-sm"            -> tambah bayangan tipis
              - "card border-0 shadow"      -> tanpa garis tepi, bayangan lebih tebal
              - "card bg-light"             -> latar kartu abu-abu muda
        --}}
        <div class="card">
            <div class="card-body">

                {{--
                    JUDUL TABEL.
                    h5   = ukuran heading level 5.
                           Contoh: <h3> lebih besar, <h6> lebih kecil.
                    mb-3 = margin bawah 16px (jarak ke tabel).
                           Contoh: mb-1 (rapat), mb-5 (renggang).
                    bi bi-file-earmark-text = ikon dari Bootstrap Icons.
                           Contoh ganti ikon: bi-list-check, bi-inbox, bi-clipboard-data
                    me-2 = margin KANAN ikon 8px (jarak ikon ke teks).
                           Contoh: me-1 (dekat), me-3 (jauh).
                           Kalau ikon mau di kanan teks, pakai ms-2 (margin kiri).
                    Tambah warna judul: class="mb-3 text-primary" (biru) / text-danger (merah)
                    Rata tengah judul : class="mb-3 text-center"
                    Rata kanan judul  : class="mb-3 text-end"
                --}}
                <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Semua Pengaduan</h5>

                {{--
                    table-responsive = tabel bisa digeser (scroll) ke samping di layar kecil/HP,
                                       sehingga tidak merusak layout.
                --}}
                <div class="table-responsive">

                    {{--
                        TABEL DATA.
                        table        = style tabel dasar Bootstrap.
                        table-hover  = baris berubah warna saat kursor diarahkan (hover).
                        align-middle = isi sel rata TENGAH secara vertikal.
                        Contoh tambah:
                          - table-striped  -> baris belang-seling (zebra)
                          - table-bordered -> semua sel diberi garis
                          - table-sm       -> tabel lebih rapat/kecil
                          - align-top      -> isi sel rata ATAS (ganti align-middle)
                    --}}
                    <table class="table table-hover align-middle">

                        {{-- HEADER TABEL (judul kolom). Contoh beri warna: <thead class="table-dark"> (hitam) / table-primary (biru) --}}
                        <thead>
                            <tr>
                                <th>No</th>             {{-- Nomor urut --}}
                                <th>Nama Customer</th>  {{-- Nama pengirim pengaduan --}}
                                <th>Kategori</th>       {{-- Kategori pengaduan --}}
                                <th>Pengaduan</th>      {{-- Isi pengaduan (dipotong 60 karakter) --}}
                                <th>Foto</th>           {{-- Thumbnail foto bukti --}}
                                <th>Status</th>         {{-- menunggu / diproses / selesai --}}
                                <th>Tanggal</th>        {{-- Waktu pengaduan dibuat --}}
                                <th>Aksi</th>           {{-- Tombol lihat detail --}}
                                {{--
                                    Contoh rata teks per kolom:
                                      <th class="text-center">Status</th>  -> rata tengah
                                      <th class="text-end">Aksi</th>       -> rata KANAN
                                    (isi <td> di bawahnya juga perlu diberi class yang sama)
                                    Contoh atur lebar kolom:
                                      <th style="width: 60px;">No</th>
                                --}}
                            </tr>
                        </thead>

                        <tbody>
                            {{--
                                PERULANGAN DATA.
                                @forelse = seperti foreach, tapi punya @empty jika data kosong.
                                $pengaduans dikirim dari controller (hasil paginate()).
                            --}}
                            @forelse ($pengaduans as $item)
                                <tr>
                                    {{--
                                        NOMOR URUT yang tetap lanjut di halaman pagination.
                                        Rumus: nomor di loop + (halaman sekarang - 1) x jumlah per halaman
                                        Contoh: halaman 2 dengan 10 data/halaman -> nomor mulai 11.
                                    --}}
                                    <td>{{ $loop->iteration + ($pengaduans->currentPage() - 1) * $pengaduans->perPage() }}</td>

                                    {{-- Nama customer, diambil dari relasi user di model Pengaduan --}}
                                    <td>{{ $item->user->nama }}</td>

                                    {{-- KATEGORI --}}
                                    <td>
                                        {{-- Jika pengaduan punya kategori, tampilkan sebagai badge --}}
                                        @if($item->kategori)
                                            {{--
                                                badge        = label kecil berbentuk pil.
                                                bg-secondary = warna latar ABU-ABU.
                                                Contoh ganti warna:
                                                  bg-primary (biru), bg-success (hijau),
                                                  bg-danger (merah), bg-dark (hitam)
                                                Contoh bentuk lebih bulat: tambah "rounded-pill"
                                                Contoh lebih besar: tambah "fs-6"
                                            --}}
                                            <span class="badge bg-secondary">{{ $item->kategori->nama }}</span>
                                        @else
                                            {{-- Jika tidak ada kategori, tampilkan tanda "-" berwarna abu-abu (text-muted) --}}
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- ISI PENGADUAN, dipotong maksimal 60 karakter + "...". Contoh: Str::limit($item->pengaduan, 100) agar lebih panjang --}}
                                    <td>{{ Str::limit($item->pengaduan, 60) }}</td>

                                    {{-- FOTO --}}
                                    <td>
                                        @if ($item->foto)
                                            {{-- Klik foto -> buka gambar ukuran penuh di tab baru (target="_blank") --}}
                                            <a href="{{ asset('storage/' . $item->foto) }}" target="_blank">
                                                {{--
                                                    STYLE THUMBNAIL (inline CSS):
                                                      width: 50px      -> LEBAR gambar.
                                                                          Contoh: 80px (lebih besar), 30px (lebih kecil)
                                                      height: 50px     -> TINGGI gambar.
                                                                          Samakan dengan width agar persegi.
                                                      object-fit: cover-> gambar dipotong otomatis agar memenuhi kotak
                                                                          tanpa gepeng. Contoh: "contain" = gambar utuh
                                                                          tapi bisa ada ruang kosong.
                                                      border-radius: 4px -> kelengkungan sudut.
                                                                          Contoh: 0 (kotak tajam), 50% (bulat/lingkaran),
                                                                          10px (lebih melengkung)
                                                    Contoh tambah garis tepi : border: 2px solid #0d6efd;
                                                    Contoh tambah bayangan   : box-shadow: 0 2px 6px rgba(0,0,0,.3);
                                                --}}
                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto pengaduan" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            </a>
                                        @else
                                            {{-- Tidak ada foto -> tampilkan "-" --}}
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- STATUS --}}
                                    <td>
                                        @php
                                            /*
                                                Menentukan WARNA badge sesuai status.
                                                  menunggu -> kuning (bg-warning) dengan teks gelap agar terbaca
                                                  diproses -> biru muda (bg-info)
                                                  selesai  -> hijau (bg-success)
                                                Contoh ubah warna:
                                                  'selesai' => 'bg-primary'   (jadi biru)
                                                  'menunggu' => 'bg-danger'   (jadi merah)
                                                Catatan: jika nanti ada status baru di database (misal 'ditolak'),
                                                tambahkan barisnya, contoh:
                                                  'ditolak' => 'bg-danger',
                                                kalau tidak, halaman akan error karena match tidak menemukan nilai.
                                            */
                                            $badge = match($item->status) {
                                                'menunggu' => 'bg-warning text-dark',
                                                'diproses' => 'bg-info',
                                                'selesai' => 'bg-success',
                                            };
                                        @endphp
                                        {{-- Menampilkan status dengan huruf awal kapital (ucfirst), warna dari variabel $badge --}}
                                        <span class="badge {{ $badge }}">{{ ucfirst($item->status) }}</span>
                                    </td>

                                    {{-- TANGGAL. Format: tanggal Bulan Tahun Jam:Menit. Contoh ubah: format('d/m/Y') -> 26/10/2026 --}}
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>

                                    {{-- AKSI --}}
                                    <td>
                                        {{--
                                            TOMBOL LIHAT DETAIL.
                                            btn              = style tombol dasar.
                                            btn-sm           = ukuran KECIL. Contoh: hapus -> normal, "btn-lg" -> besar.
                                            btn-outline-primary = tombol dengan garis tepi BIRU, latar transparan.
                                              Contoh ganti gaya/warna:
                                                btn-primary         -> biru penuh (terisi)
                                                btn-outline-success -> garis hijau
                                                btn-outline-danger  -> garis merah
                                            onclick="showDetail(id)" = menjalankan fungsi JavaScript showDetail()
                                              untuk membuka modal detail, dengan id pengaduan yang diklik.
                                            title            = teks tooltip saat kursor diarahkan ke tombol.
                                            bi-eye           = ikon mata. Contoh ganti: bi-search, bi-info-circle
                                        --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showDetail({{ $item->id }})" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                            {{-- Dijalankan jika $pengaduans kosong (belum ada data) --}}
                            @empty
                                {{--
                                    colspan="8" = sel digabung melebar ke 8 kolom (sesuai jumlah kolom tabel).
                                    Jika kamu menambah/menghapus kolom, ubah angka ini.
                                    text-center = teks rata tengah. Contoh: text-start (kiri), text-end (kanan)
                                    text-muted  = warna teks abu-abu.
                                --}}
                                <tr><td colspan="8" class="text-center text-muted">Belum ada pengaduan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{--
                    PAGINATION (nomor halaman 1, 2, 3, ...).
                    Otomatis dibuat Laravel dari paginate() di controller.
                    Contoh taruh di TENGAH:
                      <div class="d-flex justify-content-center">{{ $pengaduans->links() }}</div>
                    Contoh taruh di KANAN:
                      <div class="d-flex justify-content-end">{{ $pengaduans->links() }}</div>
                    (justify-content-start = kiri)
                --}}
                {{ $pengaduans->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Memasukkan modal (popup) detail pengaduan yang dibuka oleh fungsi showDetail() di atas --}}
@include('pengaduan._modal-detail')
@endsection