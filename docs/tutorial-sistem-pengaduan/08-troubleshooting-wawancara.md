# 8. Troubleshooting dan panduan wawancara Serkom

## Troubleshooting

| Gejala | Penyebab yang umum | Langkah pemeriksaan/perbaikan |
|---|---|---|
| `php` bukan perintah yang dikenal | PHP belum masuk PATH CMD | Periksa `where php`; tambahkan folder PHP 8.2+ ke PATH atau gunakan terminal Laragon, lalu buka CMD baru. |
| `composer` bukan perintah yang dikenal | Composer belum terpasang atau PATH belum diperbarui | Periksa Composer dari CMD baru; pastikan PHP yang dipakai Composer adalah PHP 8.2+. |
| `Could not open input file: artisan` | CMD bukan di root Laravel | Jalankan `cd /d D:\AplikasiPengaduan`, lalu periksa `dir artisan`. |
| `Laravel Framework 13.0.0` atau versi bukan 12 | Constraint versi instalasi tidak sesuai | Verifikasi `composer.json` pada project; kebutuhan tutorial adalah `laravel/framework` `^12.0`. |
| `No application encryption key has been specified` | `APP_KEY` kosong | Dari root jalankan `php artisan key:generate`, kemudian mulai ulang server. |
| `SQLSTATE[HY000] [2002]` / connection refused | MySQL mati, host atau port keliru | Nyalakan MySQL Laragon; cocokkan `DB_HOST` dan `DB_PORT` dengan pengaturan server lokal. |
| `Access denied for user` | Kredensial tidak sesuai | Periksa `DB_USERNAME`/`DB_PASSWORD` di `.env`; sesuaikan dengan akun server MySQL. |
| `Unknown database 'pengaduan_db'` | Database belum dibuat atau namanya berbeda | Buat database di HeidiSQL/phpMyAdmin atau samakan `DB_DATABASE`. |
| Perubahan `.env` tidak terbaca | Cache konfigurasi masih aktif | Dari root jalankan `php artisan config:clear`. Jangan mengedit file `.env.example` untuk konfigurasi mesin. |
| `Table already exists` | Migration ganda atau tabel telah dibuat di target database | Periksa `php artisan migrate:status`, nama file migration, dan database yang dipilih. Jangan menjalankan `migrate:fresh` pada data yang harus dipertahankan. |
| `Base table or view not found: 1146 Table 'pengaduan_db.users' doesn't exist` | Migration belum dijalankan atau gagal | Periksa output `php artisan migrate`, urutan kategori sebelum pengaduan, dan koneksi `.env`. |
| `Target class [role] does not exist` | Alias middleware tidak terdaftar | Pastikan `bootstrap/app.php` mengandung alias `role` ke `RoleMiddleware`. |
| `View [users.index] not found` | File/folder Blade salah nama atau lokasi | Cocokkan titik pada `view('users.index')` dengan file `resources/views/users/index.blade.php`. |
| `Route [admin.users.index] not defined` | Nama `route()` berbeda dari nama route | Bandingkan nama pada Blade dengan prefix dan `->name()` di `routes/web.php`. |
| Form menampilkan 419 Page Expired | Token CSRF hilang atau sesi kedaluwarsa | Pastikan semua form POST/PUT/DELETE memiliki `@csrf`; muat ulang halaman dan kirim kembali. |
| Email sudah dipakai | Email harus unik | Pakai email berbeda atau edit akun yang sudah ada. |
| Foto gagal validasi | File bukan JPG/PNG atau ukuran di atas 2 MB | Gunakan gambar JPG/PNG kurang dari batas; periksa aturan `mimes` dan `max`. |
| Foto tersimpan, tetapi tautan gagal | Symbolic link belum ada | Jalankan `php artisan storage:link` dari root; pastikan link `public/storage` tersedia. |
| `Address already in use` | Port Laravel sedang dipakai program lain | Jalankan `php artisan serve --port=8001`; buka alamat yang dicetak terminal. |
| Layout tanpa style/icon | Komputer tidak dapat mengakses CDN | Periksa koneksi internet/browser; tutorial ini memuat Bootstrap dan Bootstrap Icons dari CDN. |
| `403 Forbidden` | Role akun tidak cocok dengan URL yang dibuka | Login dengan role yang sesuai. Ini perilaku pembatasan akses yang diharapkan. |
| Data customer bercampur | Query tidak difilter pemilik | Pastikan controller customer selalu memakai `where('user_id', Auth::id())`; jangan mengambil semua pengaduan. |

Perintah pemeriksaan yang aman dari CMD root project:

```bat
cd /d D:\AplikasiPengaduan
php artisan --version
php artisan route:list
php artisan migrate:status
php artisan config:clear
```

Jalankan hanya perintah yang sesuai dengan masalah. `config:clear` hanya membersihkan cache konfigurasi; `migrate:fresh` bukan perintah pemecahan masalah umum karena menghapus semua tabel.

## Panduan wawancara Serkom

Gunakan jawaban dengan bahasa sendiri. Pahami konsep dan tunjukkan implementasi yang relevan, jangan menghafal tanpa mengerti.

### 1. Apa fungsi Sistem Pengaduan?

Sistem ini memungkinkan customer mengirim laporan masalah, melampirkan foto bukti, dan memantau status. Petugas memeriksa laporan dan mengubah status menjadi menunggu, diproses, atau selesai. Admin mengelola akun dan kategori serta memiliki akses ke semua laporan.

### 2. Apa peran Model, View, dan Controller pada aplikasi ini?

- **Model** (`User`, `Kategori`, `Pengaduan`) mewakili data, tabel, dan relasinya.
- **View** Blade menyajikan halaman HTML dan form.
- **Controller** menerima request, memvalidasi data, memanggil Model, lalu memilih View atau redirect.
- **Route** menghubungkan URL dan HTTP method ke action controller.

### 3. Bagaimana aplikasi membatasi akses role?

Route dilindungi `auth` untuk memastikan pengguna sudah login dan `role:admin`, `role:petugas`, atau `role:customer` untuk memeriksa role. Middleware `RoleMiddleware` membalas 403 jika role tidak cocok. Menyembunyikan tombol/menu saja tidak cukup untuk keamanan.

### 4. Mengapa register umum selalu membuat role customer?

Role ditentukan server sebagai `customer`, bukan diambil dari input form. Jika form publik dapat mengirim `role=admin`, pengunjung bisa mencoba menaikkan hak aksesnya sendiri.

### 5. Bagaimana password disimpan?

Password diubah menjadi hash dengan `Hash::make()` sebelum disimpan. Saat login, `Auth::attempt()` membandingkan password input dengan hash. Aplikasi tidak menyimpan password asli.

### 6. Bagaimana menjaga kerahasiaan laporan customer?

Controller customer memfilter query dengan `where('user_id', Auth::id())`. Kolom `user_id` untuk laporan baru juga diambil dari sesi login, bukan dari input form. Middleware role mencegah role lain membuka area customer.

### 7. Apa fungsi migration dan foreign key?

Migration mencatat perubahan struktur database sebagai kode yang dapat dijalankan konsisten. `pengaduan.user_id` menghubungkan laporan dengan akun; `kategori_id` menghubungkan laporan dengan kategori. Foreign key membantu menjaga integritas relasi.

### 8. Bagaimana upload foto bekerja?

Form menggunakan `enctype="multipart/form-data"`. Request memvalidasi file sebagai gambar JPG/PNG dengan batas 2 MB, lalu menyimpannya pada disk `public`. Database menyimpan path, bukan isi file; `storage:link` menyediakan akses URL melalui `public/storage`.

### 9. Mengapa aplikasi memakai `php artisan serve` walau MySQL berasal dari Laragon?

MySQL dan web server adalah layanan yang berbeda. Laragon dapat menjalankan MySQL sementara server pengembangan Laravel menjalankan aplikasi dari direktori project mana pun, termasuk `D:\AplikasiPengaduan`. Folder `www` dan virtual host tidak dibutuhkan dalam alur ini.

### 10. Apa itu CSRF dan bagaimana form melindunginya?

CSRF adalah pemalsuan request lintas situs. Blade `@csrf` menambahkan token validasi ke form yang mengubah data. Form update dan delete juga memakai `@method('PUT')` atau `@method('DELETE')` karena HTML form biasa hanya mengirim GET/POST.

### 11. Mengapa validasi dilakukan di controller?

Validasi di server tetap berjalan walaupun aturan HTML di browser dilewati. Aturan seperti `required`, `unique`, `exists`, serta whitelist status memastikan data tidak kosong, tidak duplikat, memiliki relasi valid, dan sesuai pilihan aplikasi.

### 12. Apa yang dilakukan jika role salah mendapat 403?

Periksa role akun di database/seeder, logout lalu login dengan akun yang benar, dan pastikan URL yang dibuka sesuai prefix role. Respons 403 untuk role yang tidak berhak merupakan perilaku yang diharapkan.

## Checklist akhir dokumentasi

- [ ] Root project contoh selalu `D:\AplikasiPengaduan`, tidak di dalam `www`.
- [ ] Semua perintah ditujukan untuk CMD dan `cd /d` dipakai ketika menentukan root.
- [ ] Versi yang diminta adalah Laravel 12.x.
- [ ] MySQL dijalankan melalui Laragon dan migration dijalankan secara manual oleh pembaca.
- [ ] Register, login, logout, role, middleware, dashboard, user/customer, profile, kategori, pengaduan, status, dan foto dibahas.
- [ ] Form, route, method controller, nama kolom, dan model memakai nama yang konsisten.
- [ ] Prosedur menjalankan server memakai `php artisan serve`.
- [ ] Pengujian mencakup alur fitur dan pembatasan akses.
