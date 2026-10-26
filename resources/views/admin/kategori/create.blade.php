{{-- =====================================================================
     VIEW   : Halaman "Tambah Kategori" (khusus Admin)
     TUJUAN : Admin mengisi form untuk membuat kategori baru, supaya
              pengaduan bisa dikelompokkan dengan rapi.

     ALUR KERJA:
       1. Admin klik tombol tambah kategori, halaman ini tampil.
       2. Admin mengisi nama (wajib) dan deskripsi (boleh kosong).
       3. Klik "Simpan", data dikirim lewat POST ke route admin.kategori.store.
       4. Controller memvalidasi dan menyimpan data ke tabel kategori.
       5. Kalau validasi GAGAL, user kembali ke halaman ini, lalu pesan
          error dan isian lama ditampilkan lagi.
       6. Klik "Kembali" untuk ke daftar kategori tanpa menyimpan.

     CATATAN CSS: semua class di file ini berasal dari BOOTSTRAP 5,
     kecuali "main-content" (class buatan sendiri, CSS-nya ada di layout
     atau file css proyek, bukan di file ini).
     ===================================================================== --}}


{{-- ---------------------------------------------------------------------
     @extends = "pakai kerangka halaman" dari file lain.
     'layouts.app' artinya file resources/views/layouts/app.blade.php.
     Layout itu berisi bagian yang sama di semua halaman (head, navbar,
     link CSS/JS Bootstrap). Jadi file ini cukup berisi isi halamannya saja.
     --------------------------------------------------------------------- --}}
@extends('layouts.app')

{{-- ---------------------------------------------------------------------
     @section('title', '...') = mengisi "slot" bernama title di layout.
     Biasanya tampil sebagai tulisan di TAB browser.
     Contoh ubah: 'Tambah Kategori - Sistem Pengaduan' jadi 'Kategori Baru'.
     --------------------------------------------------------------------- --}}
@section('title', 'Tambah Kategori - Sistem Pengaduan')

{{-- ---------------------------------------------------------------------
     @section('content') ... @endsection = isi utama halaman.
     Isinya akan dimasukkan ke slot "content" di layout (@yield('content')).
     --------------------------------------------------------------------- --}}
@section('content')

{{-- ---------------------------------------------------------------------
     WADAH LUAR (sidebar di kiri, konten di kanan)

     d-flex : membuat isi di dalamnya berjajar MENYAMPING (kiri ke kanan).
              Mengubah: ARAH/SUSUNAN.
              Contoh: hapus d-flex, sidebar dan konten jadi menumpuk atas-bawah.
              Contoh: tambah flex-row-reverse, sidebar pindah ke KANAN.
              Contoh: tambah flex-column, tersusun ke bawah.
     --------------------------------------------------------------------- --}}
<div class="d-flex">

    {{-- @include = menyisipkan file lain ke sini.
         'partials.sidebar-admin' = file resources/views/partials/sidebar-admin.blade.php
         Isinya menu samping khusus admin, dibuat terpisah supaya bisa
         dipakai ulang di banyak halaman. --}}
    @include('partials.sidebar-admin')

    {{-- -----------------------------------------------------------------
         AREA KONTEN UTAMA (sebelah kanan sidebar)
         Form ini dipakai admin untuk membuat kategori baru agar pengaduan
         bisa dikelompokkan dengan rapi.

         main-content : class BUATAN SENDIRI (bukan Bootstrap). Biasanya
                        berisi pengaturan lebar/jarak/warna latar area konten.
                        Untuk mengubahnya, cari ".main-content" di file CSS
                        atau di tag style pada layouts/app.blade.php.
                        Contoh isi CSS-nya (hanya contoh):
                        .main-content { background: #f8f9fa; }  warna latar abu muda
                        .main-content { margin-left: 250px; }   geser konten ke kanan 250px
         flex-grow-1  : mengisi SISA lebar layar. Mengubah: UKURAN (lebar).
                        Contoh: hapus flex-grow-1, lebar konten hanya sebesar isinya.
         p-4          : padding (jarak DALAM) ke semua sisi = 1.5rem (sekitar 24px).
                        Mengubah: JARAK dalam.
                        Skala: p-0 (nol), p-1 (4px), p-2 (8px), p-3 (16px),
                               p-4 (24px), p-5 (48px).
                        Contoh: p-2 lebih rapat, p-5 lebih lega.
                        Contoh: px-4 hanya kiri dan kanan, py-4 hanya atas dan bawah.
                        Contoh: ps-4 hanya KIRI, pe-4 hanya KANAN,
                                pt-4 hanya ATAS, pb-4 hanya BAWAH.
         ----------------------------------------------------------------- --}}
    <div class="main-content flex-grow-1 p-4">

        {{-- -------------------------------------------------------------
             card : kotak putih dengan garis tepi dan sudut agak membulat.
                    Mengubah: TAMPILAN kotak.
                    Contoh: tambah shadow-sm (bayangan tipis), shadow (sedang),
                            shadow-lg (besar).
                    Contoh: tambah border-0 untuk menghilangkan garis tepi.
                    Contoh: tambah bg-light (latar abu muda), rounded-4 (sudut
                            lebih bulat).
                    Contoh: tambah w-75 supaya lebarnya 75% saja.
             ------------------------------------------------------------- --}}
        <div class="card">

            {{-- card-body : ruang dalam kartu (sudah ada padding bawaan 1rem).
                 Isi form ditaruh di sini supaya tidak menempel ke tepi kartu.
                 Contoh: tambah p-4 untuk padding lebih besar. --}}
            <div class="card-body">

                {{-- =====================================================
                     BAGIAN 1: KOTAK PESAN ERROR VALIDASI
                     $errors = variable otomatis dari Laravel (ada di semua
                     view), berisi daftar kesalahan dari validasi controller.
                     $errors->any() = "apakah ada error?"
                     Kalau tidak ada error, seluruh kotak merah ini tidak muncul.
                     ===================================================== --}}
                @if ($errors->any())

                    {{-- alert alert-danger : kotak peringatan berwarna MERAH.
                         Mengubah: WARNA.
                         Contoh: alert-warning (kuning), alert-info (biru muda),
                                 alert-success (hijau), alert-primary (biru),
                                 alert-secondary (abu). --}}
                    <div class="alert alert-danger">

                        {{-- ul = daftar berpoin (bullet).
                             mb-0 : margin bawah (jarak LUAR bawah) = 0, supaya tidak ada
                                    ruang kosong berlebih di dasar kotak.
                                    Contoh: mb-3 memberi jarak bawah 16px.
                             ps-3 : padding start (jarak dalam sisi KIRI) = 16px, supaya
                                    bullet menjorok sedikit ke dalam.
                                    Contoh: ps-0 rapat ke kiri, ps-5 menjorok jauh.
                                    Contoh: tambah list-unstyled untuk menghilangkan bullet. --}}
                        <ul class="mb-0 ps-3">

                            {{-- @foreach = ulangi untuk setiap isi daftar.
                                 $errors->all() = semua pesan error (berupa array).
                                 $error = variable sementara yang berisi SATU pesan
                                 pada setiap putaran. --}}
                            @foreach ($errors->all() as $error)

                                {{-- {{ $error }} = tampilkan isi variable.
                                     Tanda {{ }} otomatis mengamankan teks dari kode berbahaya
                                     (anti XSS). --}}
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                {{-- =====================================================
                     BAGIAN 2: FORM
                     action = ALAMAT tujuan data dikirim.
                       route('admin.kategori.store') = membuat URL dari route
                       yang diberi nama admin.kategori.store (di routes/web.php).
                       Memakai nama route lebih aman daripada menulis URL manual,
                       karena kalau URL berubah, kode di sini tidak perlu diubah.
                     method="POST" = data dikirim lewat body permintaan (tidak
                       terlihat di URL). Dipakai untuk MENAMBAH/MENYIMPAN data.
                     ===================================================== --}}
                <form action="{{ route('admin.kategori.store') }}" method="POST">

                    {{-- @csrf = membuat token rahasia tersembunyi di form.
                         Gunanya mencegah serangan CSRF (pihak lain mengirim form
                         palsu atas nama admin).
                         WAJIB ada di setiap form POST. Kalau dihapus, akan muncul
                         error 419 Page Expired. --}}
                    @csrf


                    {{-- =================================================
                         BAGIAN 2A: INPUT "NAMA KATEGORI"
                         mb-3 : margin bawah 1rem (16px), jarak ke elemen DI BAWAHNYA.
                                Mengubah: JARAK luar bawah.
                                Contoh: mb-2 (8px) lebih rapat, mb-4 (24px) lebih lega.
                                Contoh: mt-3 untuk jarak ATAS.
                         ================================================= --}}
                    <div class="mb-3">

                        {{-- label = tulisan penjelas untuk input.
                             for="nama" harus SAMA dengan id input di bawah. Dengan begitu,
                                  kalau label diklik, kursor otomatis masuk ke input.
                             form-label : gaya label Bootstrap (ada jarak bawah kecil).
                             span text-danger : bintang (*) berwarna MERAH sebagai tanda
                                  "wajib diisi". text-danger mengubah: WARNA teks.
                                  Contoh: text-primary (biru), text-success (hijau),
                                          text-warning (kuning), text-secondary (abu). --}}
                        <label for="nama" class="form-label">Nama Kategori <span class="text-danger">*</span></label>

                        {{-- Penjelasan tiap bagian pada kotak isian di bawah:
                             type="text"        : jenis isian teks satu baris.
                                                  Contoh: type="email" untuk email,
                                                  type="number" untuk angka.
                             class="form-control" : membuat input selebar penuh dan
                                                  tampil rapi ala Bootstrap.
                                                  Mengubah: UKURAN dan TAMPILAN.
                                                  Contoh: form-control-lg (besar),
                                                          form-control-sm (kecil).
                             @error('nama') is-invalid @enderror :
                                                  jika field "nama" ada error, class
                                                  is-invalid ditambahkan, sehingga garis
                                                  tepi jadi MERAH. Jika tidak ada error,
                                                  tidak ditambahkan apa-apa.
                             id="nama"          : identitas elemen (dipakai label dan
                                                  JavaScript/CSS).
                             name="nama"        : NAMA KUNCI data yang dikirim ke server.
                                                  Harus sama dengan aturan validasi di
                                                  controller dan kolom $fillable di model
                                                  Kategori. Kalau beda, datanya tidak
                                                  akan tersimpan.
                             value="{{ old('nama') }}" :
                                                  old('nama') mengambil isian LAMA saat
                                                  validasi gagal, jadi user tidak perlu
                                                  mengetik ulang dari awal.
                             placeholder="..."  : tulisan abu-abu contoh yang hilang
                                                  saat mengetik.
                             required           : browser menolak kirim kalau kosong
                                                  (validasi di sisi browser). Validasi
                                                  sebenarnya tetap ada di controller. --}}
                        <input type="text"
                               class="form-control @error('nama') is-invalid @enderror"
                               id="nama"
                               name="nama"
                               value="{{ old('nama') }}"
                               placeholder="Masukkan nama kategori"
                               required>

                        {{-- @error('nama') ... @enderror = bagian di dalamnya hanya
                             tampil jika field "nama" bermasalah.
                             invalid-feedback : pesan kecil berwarna MERAH di bawah input
                                  (otomatis tampil karena input di atasnya punya is-invalid).
                                  Contoh: ingin pesan lain? ubah di controller pada
                                  bagian validasi, bukan di sini.
                             {{ $message }} : variable otomatis berisi pesan error
                                  untuk field "nama". --}}
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- =================================================
                         BAGIAN 2B: INPUT "DESKRIPSI"
                         Strukturnya sama dengan nama, hanya saja tidak wajib.
                         (tidak ada tanda * dan tidak ada atribut required)
                         ================================================= --}}
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>

                        {{-- textarea = kotak isian teks banyak baris.
                             rows="3" : tinggi awal 3 baris. Mengubah: UKURAN (tinggi).
                                        Contoh: rows="6" jadi lebih tinggi.
                             Isi lama ditulis DI ANTARA tag buka dan tutup, bukan
                             di atribut value (textarea tidak punya value).
                             PENTING: jangan beri spasi atau enter antara > dan
                             {{ old(...) }}, nanti spasi itu ikut masuk ke isian. --}}
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                  id="deskripsi"
                                  name="deskripsi"
                                  rows="3"
                                  placeholder="Masukkan deskripsi kategori (opsional)">{{ old('deskripsi') }}</textarea>

                        {{-- Pesan error untuk field deskripsi (tampil hanya jika ada error). --}}
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- =================================================
                         BAGIAN 3: TOMBOL AKSI
                         d-flex : tombol-tombol berjajar menyamping.
                         gap-2  : jarak ANTAR tombol = 0.5rem (8px).
                                  Mengubah: JARAK antar elemen.
                                  Contoh: gap-3 (16px) lebih lebar, gap-1 (4px) rapat.
                         Menggeser posisi tombol (tambahkan di class):
                                  justify-content-end     tombol ke KANAN
                                  justify-content-center  tombol di TENGAH
                                  justify-content-between tombol di ujung KIRI dan KANAN
                                  (tanpa tambahan, tombol ada di KIRI)
                         ================================================= --}}
                    <div class="d-flex gap-2">

                        {{-- TOMBOL SIMPAN
                             type="submit" : saat diklik, form dikirim ke alamat action.
                             btn         : gaya dasar tombol Bootstrap.
                             btn-primary : warna BIRU. Mengubah: WARNA tombol.
                                  Contoh: btn-success (hijau), btn-danger (merah),
                                          btn-warning (kuning), btn-info (biru muda),
                                          btn-dark (hitam).
                                  Contoh: btn-outline-primary = hanya garis tepi biru,
                                          tanpa warna isi.
                             Ukuran: tambah btn-sm (kecil) atau btn-lg (besar).
                             Tombol selebar penuh: tambah w-100. --}}
                        <button type="submit" class="btn btn-primary">

                            {{-- i = elemen untuk ikon (Bootstrap Icons).
                                 bi              : gaya dasar ikon.
                                 bi-check-circle : bentuk ikon centang dalam lingkaran.
                                                   Contoh: bi-save (ikon disket),
                                                   bi-plus-circle (tanda tambah).
                                                   Daftar ikon: icons.getbootstrap.com
                                 me-1            : margin end (jarak luar sisi KANAN) = 4px,
                                                   jarak antara ikon dan tulisan.
                                                   Mengubah: JARAK ke kanan.
                                                   Contoh: me-2 (8px) lebih lebar.
                                                   Contoh: kalau ikon dipindah ke kanan teks,
                                                   ganti jadi ms-1 (margin start = sisi KIRI). --}}
                            <i class="bi bi-check-circle me-1"></i>Simpan
                        </button>

                        {{-- TOMBOL KEMBALI
                             Memakai tag a (link), bukan button, karena tujuannya
                             PINDAH HALAMAN tanpa menyimpan apa pun.
                             href = route('admin.kategori.index') = alamat halaman
                                    daftar kategori.
                             btn-secondary : warna ABU (tombol pelengkap). Contoh ubah:
                                    btn-outline-secondary (hanya garis tepi), btn-light.
                             bi-arrow-left : ikon panah ke kiri. --}}
                        <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div> {{-- penutup card-body --}}
        </div> {{-- penutup card --}}
    </div> {{-- penutup main-content --}}
</div> {{-- penutup d-flex (wadah sidebar dan konten) --}}

{{-- Penutup @section('content'). Setelah ini, isi halaman dimasukkan
     ke layout. --}}
@endsection