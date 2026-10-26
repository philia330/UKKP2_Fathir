{{--
    =====================================================================
    CATATAN UMUM
    =====================================================================
    - Komentar bentuk kurung-kurawal-kurawal-strip-strip (seperti yang sedang kamu baca ini)
      adalah komentar Blade, tidak ikut tampil di HTML / "View Page Source".
    - Halaman ini adalah FORM EDIT USER, hanya untuk admin.
    - Struktur dan class-nya hampir sama dengan form Tambah User. Bedanya:
        1. Form dikirim ke route 'admin.users.update' dengan method PUT (@method('PUT')).
        2. Input sudah terisi data user lama, lewat old('field', $user->field).
        3. Password bersifat opsional (boleh dikosongkan jika tidak ingin diganti).
        4. Tombol utamanya bertuliskan "Update", bukan "Simpan".
    - Variabel $user dikirim dari controller, contoh: return view('admin.users.edit', compact('user'));
    - Semua class di file ini adalah class Bootstrap. Cara mengubahnya: ganti nama class-nya.
    - Skala spasi Bootstrap: 0 = 0px, 1 = 4px, 2 = 8px, 3 = 16px, 4 = 24px, 5 = 48px.
    =====================================================================
--}}

{{-- Memakai layout utama "layouts.app" (isi: navbar, <head>, CSS/JS Bootstrap, dll). --}}
@extends('layouts.app')

{{-- Judul tab browser. Contoh ubah: 'Ubah Data User - Sistem Pengaduan' --}}
@section('title', 'Edit User - Sistem Pengaduan')

{{-- Awal isi halaman, akan dimasukkan ke @yield('content') di layouts.app --}}
@section('content')

{{--
    WADAH UTAMA: sidebar dan konten berdampingan.
    d-flex = anak elemen disusun sejajar horizontal (kiri-kanan).
    Contoh ubah:
      - "d-flex flex-row-reverse" -> sidebar pindah ke KANAN, konten ke KIRI
      - "d-flex flex-column"      -> sidebar di atas, konten di bawah
--}}
<div class="d-flex">

    {{-- Memasukkan sidebar khusus admin (resources/views/partials/sidebar-admin.blade.php) --}}
    @include('partials.sidebar-admin')

    {{-- Form ini digunakan untuk memperbarui data user yang sudah ada. --}}
    {{--
        AREA KONTEN UTAMA.
        main-content = class CSS buatan sendiri (cek di file CSS/layout kamu).
        flex-grow-1  = lebar otomatis mengisi sisa ruang setelah sidebar.
        p-4          = padding 24px di semua sisi.
                       Contoh: p-2 (rapat, 8px), p-5 (lega, 48px), px-5 (hanya kiri-kanan)
    --}}
    <div class="main-content flex-grow-1 p-4">

        {{--
            KARTU PEMBUNGKUS FORM.
            card      = kotak putih dengan garis tepi dan sudut membulat.
            card-body = ruang padding di dalam kartu.
            Contoh ubah:
              - "card shadow-sm"        -> tambah bayangan tipis
              - "card border-0 shadow"  -> tanpa garis tepi, bayangan lebih tebal
              - "card border-warning"   -> garis tepi kuning, penanda mode edit
            Contoh batasi lebar kartu agar form tidak terlalu melebar di layar besar:
              <div class="card" style="max-width: 600px;">
            Contoh kartu di tengah halaman:
              <div class="card mx-auto" style="max-width: 600px;">
        --}}
        <div class="card">
            <div class="card-body">

                {{--
                    KOTAK ERROR VALIDASI (rangkuman semua error di bagian atas).
                    $errors->any() = true jika ada minimal satu error validasi.
                    Kotak ini hanya muncul setelah submit yang gagal.
                --}}
                @if ($errors->any())
                    {{--
                        alert alert-danger = kotak peringatan berwarna MERAH.
                        Contoh ganti warna:
                          alert-warning -> kuning
                          alert-info    -> biru muda
                    --}}
                    <div class="alert alert-danger">
                        {{--
                            ul   = daftar berpoin.
                            mb-0 = tanpa margin bawah, supaya tidak ada ruang kosong berlebih di dalam kotak.
                            ps-3 = padding KIRI 16px, mengatur jarak poin dari tepi kiri.
                                   Contoh: ps-0 (rapat), ps-5 (menjorok jauh)
                                   Contoh tanpa poin: tambah "list-unstyled"
                        --}}
                        <ul class="mb-0 ps-3">
                            {{-- Mengulang semua pesan error, masing-masing jadi satu poin --}}
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{--
                    FORM UTAMA.
                    action = route bernama 'admin.users.update'. Parameter $user dikirim agar
                             URL berisi id user yang sedang diedit, contoh: /admin/users/5
                    method = POST. Browser hanya mendukung GET dan POST,
                             jadi method sebenarnya (PUT) ditambahkan lewat @method('PUT') di bawah.
                --}}
                <form action="{{ route('admin.users.update', $user) }}" method="POST">

                    {{-- Token keamanan wajib Laravel (perlindungan CSRF). Tanpa ini submit akan error 419. --}}
                    @csrf

                    {{--
                        Mengubah method POST menjadi PUT agar cocok dengan Route::put / Route::resource.
                        Jika baris ini dihapus, update akan gagal (error 405 Method Not Allowed).
                    --}}
                    @method('PUT')

                    {{--
                        =============== INPUT NAMA ===============
                        mb-3 = margin bawah 16px, jarak antar kolom form.
                               Contoh: mb-2 (lebih rapat), mb-4 (lebih lega)
                    --}}
                    <div class="mb-3">
                        {{--
                            label      = teks judul input. for="nama" menyambung ke id="nama" di bawah,
                                         jadi klik label akan memfokuskan input.
                            form-label = style label Bootstrap (ada jarak ke input).
                            span text-danger = tanda bintang (*) berwarna MERAH sebagai penanda wajib diisi.
                                         Contoh: text-warning (kuning), text-primary (biru)
                        --}}
                        <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>

                        {{--
                            input type="text"  = kolom teks biasa.
                            name="nama"        = nama field yang diterima controller: $request->nama
                            form-control       = style input Bootstrap (lebar penuh, sudut membulat).
                            @error('nama') is-invalid @enderror
                                               = jika field 'nama' gagal validasi, tambahkan class is-invalid
                                                 sehingga garis input jadi MERAH.
                            old('nama', $user->nama)
                                               = nilai awal input:
                                                 - jika validasi gagal, pakai isian terakhir admin (old)
                                                 - jika baru dibuka, pakai data dari database ($user->nama)
                            required           = browser mencegah submit jika kosong.
                            Contoh ubah ukuran: "form-control form-control-lg" (besar) / "form-control-sm" (kecil)
                            Contoh kunci input agar tidak bisa diubah: tambah atribut "readonly" atau "disabled"
                            Contoh batasi lebar: style="max-width: 400px;"
                        --}}
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" required>

                        {{--
                            PESAN ERROR di bawah input (teks merah kecil).
                            invalid-feedback hanya tampil jika input di atasnya punya class is-invalid.
                            $message = isi pesan error dari validasi.
                        --}}
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- =============== INPUT EMAIL =============== --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        {{--
                            type="email" = browser otomatis mengecek format email (harus ada @).
                            Catatan: rule validasi email di controller sebaiknya memakai unique dengan
                            pengecualian id user ini (Rule::unique('users')->ignore($user->id)),
                            kalau tidak, email milik sendiri dianggap "sudah dipakai".
                        --}}
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- =============== INPUT NO. TELEPON (opsional) =============== --}}
                    <div class="mb-3">
                        {{-- Tidak ada tanda bintang dan tidak ada "required" karena kolom ini boleh dikosongkan. --}}
                        <label for="no_telp" class="form-label">No. Telepon</label>
                        {{--
                            Contoh jika ingin keyboard angka muncul di HP: ganti type="text" jadi type="tel"
                            Contoh batasi panjang: maxlength="15"
                        --}}
                        <input type="text" name="no_telp" id="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $user->no_telp) }}">
                        @error('no_telp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- =============== INPUT PASSWORD BARU (opsional) =============== --}}
                    <div class="mb-3">
                        {{-- Label tanpa bintang karena password hanya diisi jika admin ingin menggantinya. --}}
                        <label for="password" class="form-label">Password Baru</label>
                        {{--
                            type="password" = karakter yang diketik disembunyikan (titik-titik).
                            Tidak ada "required" dan tidak ada value, jadi password lama TIDAK ditampilkan
                            (memang tidak bisa, karena di database tersimpan dalam bentuk hash).
                            placeholder = teks abu-abu petunjuk yang hilang saat mengetik.
                                          Contoh ubah: placeholder="Isi hanya jika ingin ganti password"
                            Agar ini berfungsi, controller harus mengabaikan password jika kosong,
                            contoh: 'password' => 'nullable|min:8|confirmed'
                        --}}
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- =============== INPUT KONFIRMASI PASSWORD BARU (opsional) =============== --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        {{--
                            name="password_confirmation" = nama ini WAJIB persis begini agar rule
                            'confirmed' di validasi Laravel bisa mencocokkannya dengan field password.
                            Tidak ada @error di sini karena error "tidak cocok" ditampilkan di field password.
                        --}}
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    {{-- =============== PILIHAN ROLE =============== --}}
                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        {{--
                            select      = dropdown pilihan.
                            form-select = style dropdown Bootstrap (padanan form-control untuk select).
                            @error ... is-invalid = garis merah jika role gagal validasi.
                            Contoh ubah ukuran: "form-select form-select-lg" / "form-select-sm"
                        --}}
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            {{--
                                Tidak ada opsi kosong "-- Pilih Role --" seperti di form Tambah, karena
                                user yang diedit sudah pasti punya role.

                                Setiap opsi: jika old('role', $user->role) sama dengan value-nya,
                                tambahkan "selected" supaya role yang sedang berlaku langsung terpilih.
                                  - old('role', ...) = pilihan terakhir jika validasi gagal
                                  - $user->role      = role dari database jika baru dibuka
                                Contoh menambah role baru, misalnya "supervisor":
                                  <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                (jangan lupa tambahkan juga di aturan validasi controller)
                            --}}
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                            <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{--
                        =============== TOMBOL ===============
                        TOMBOL UPDATE.
                        type="submit" = mengirim form ke route admin.users.update.
                        btn           = style tombol dasar.
                        btn-primary   = warna BIRU terisi.
                                        Contoh ganti: btn-warning (kuning, cocok untuk edit),
                                        btn-success (hijau), btn-danger (merah), btn-dark (hitam)
                                        Contoh garis saja: btn-outline-primary
                        bi-save       = ikon disket. Contoh ganti: bi-check-lg, bi-pencil-square
                        me-1          = margin KANAN ikon 4px (jarak ikon ke teks).
                                        Contoh: me-2 (lebih jauh)
                        Contoh ukuran: tambah "btn-lg" (besar) / "btn-sm" (kecil)
                        Contoh tombol selebar form: tambah "w-100"
                    --}}
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Update
                    </button>

                    {{--
                        TOMBOL KEMBALI.
                        Berupa link <a> yang diberi tampilan tombol, menuju daftar user (admin.users.index).
                        Perubahan yang belum disimpan akan hilang jika tombol ini diklik.
                        btn-secondary = warna ABU-ABU. Contoh: btn-light (putih), btn-outline-secondary (garis saja)
                        bi-arrow-left = ikon panah kiri.
                        Contoh jarak dari tombol Update: tambah "ms-2" (margin kiri 8px)
                        Contoh pindah ke kanan form: bungkus kedua tombol dengan
                          <div class="d-flex justify-content-end gap-2"> ... </div>
                    --}}
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection