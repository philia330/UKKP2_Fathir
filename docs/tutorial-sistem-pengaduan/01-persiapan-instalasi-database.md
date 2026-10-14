# 1. Persiapan, instalasi Laravel, dan database

## Tahap 1 — Persiapan Windows

**Tujuan:** memastikan alat yang diperlukan sudah tersedia.  
**Persiapan:** Windows, koneksi internet, ruang kosong pada drive `D:`, dan izin menulis folder.  
**Terminal:** CMD, dibuka dari Start Menu. Perintah berikut boleh dijalankan dari direktori mana pun.

Pastikan VS Code, Composer, PHP 8.2 atau lebih baru, serta Laragon sudah terpasang. PHP/Composer boleh berasal dari Laragon atau instalasi lain. Project Laravel tetap disimpan di `D:\AplikasiPengaduan`, bukan di folder Laragon.

```bat
php -v
composer --version
where php
where composer
```

Di Laragon, tekan **Start All** atau nyalakan setidaknya MySQL. Apache tidak wajib untuk tutorial ini. Jika Laragon dipasang di lokasi berbeda, gunakan shortcut Laragon atau pilih executable PHP yang benar pada pengaturan PATH Windows; jangan memindahkan project ke folder `www`.

**Penjelasan:** `where php` menunjukkan PHP yang akan dipakai CMD. Jika versi PHP yang tampil terlalu lama, atur PATH agar menunjuk PHP 8.2+ (contoh instalasi Laragon dapat memiliki folder `C:\laragon\bin\php\php-8.x.x`). Lokasi itu hanya contoh lokasi executable, bukan lokasi project.

**Uji dan hasil yang diharapkan:** `php -v` menampilkan PHP 8.2+, `composer --version` menampilkan versi Composer, dan MySQL berstatus berjalan di Laragon.

**Error umum:** jika `php` atau `composer` bukan perintah yang dikenal, tutup lalu buka CMD setelah mengatur PATH, atau gunakan terminal Laragon yang sudah menyiapkan PATH. Jangan lanjut sampai `php -v` dan `composer --version` berhasil.

**Checklist:** [ ] PHP 8.2+ [ ] Composer berjalan [ ] VS Code tersedia [ ] MySQL Laragon dapat dinyalakan.

## Tahap 2 — Membuat project Laravel 12

**Tujuan:** membuat project Laravel 12 di root terpisah dari Laragon.  
**Persiapan:** tahap 1 lulus dan drive D tersedia.  
**Terminal:** CMD.  
**Direktori:** mulai dari drive/folder mana pun; gunakan perintah `cd /d` berikut.

```bat
cd /d D:\
composer create-project laravel/laravel AplikasiPengaduan "^12.0"
cd /d D:\AplikasiPengaduan
php artisan --version
composer show laravel/framework
```

**Penjelasan:** constraint `"^12.0"` membatasi instalasi ke versi major 12 (versi 12.x yang memenuhi constraint), bukan Laravel versi major terbaru lainnya. Tanda kutip menjaga constraint tetap menjadi satu argumen di CMD. `cd /d` mengganti drive dan direktori sekaligus.

Project Laravel berada di `D:\AplikasiPengaduan`; lokasi tersebut berbeda dari lokasi executable dan layanan MySQL Laragon. Composer dapat menjalankan skrip bawaan Laravel setelah pembuatan project; skrip tersebut tidak memindahkan project ke `www`. Jika Composer melaporkan error, selesaikan sebelum meneruskan.

**Hasil yang diharapkan:** `php artisan --version` menampilkan `Laravel Framework 12.x.x`; `composer show laravel/framework` menunjukkan versi 12.x. Struktur project akan memuat `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `tests`, `artisan`, `composer.json`, dan `.env`.

**Uji:** jangan lanjut jika perintah versi gagal atau major-nya bukan 12. Periksa `D:\AplikasiPengaduan` di File Explorer dan pastikan `artisan` ada di root tersebut.

**Error umum:**  
- `Could not open input file: artisan`: CMD belum di root project; jalankan `cd /d D:\AplikasiPengaduan`.  
- Composer memilih PHP lama: betulkan PATH lalu buka CMD baru dan ulangi hanya setelah memastikan project belum berhasil dibuat.  
- Folder tujuan sudah ada: jangan menimpa project lain; pilih dan pastikan folder tujuan yang benar sebelum mengulang.

**Checklist:** [ ] project berada di `D:\AplikasiPengaduan` [ ] tidak berada di `www` [ ] versi framework 12.x terverifikasi.

## Tahap 3 — Membuat database MySQL di Laragon

**Tujuan:** menyiapkan database kosong yang akan dipakai aplikasi.  
**Persiapan:** MySQL Laragon menyala.  
**Terminal:** tidak perlu terminal; gunakan HeidiSQL atau phpMyAdmin yang disediakan/dikonfigurasi pada Laragon.  
**Direktori:** tidak berlaku.

Buka HeidiSQL/phpMyAdmin melalui menu Laragon. Masuk ke server MySQL lokal dengan akun yang dikonfigurasi di komputer, lalu buat database bernama `pengaduan_db` dengan collation `utf8mb4_unicode_ci` jika pilihan itu tersedia. Jangan membuat tabel secara manual; Laravel akan membuat tabel melalui migration.

Jika nama database atau kredensial MySQL komputer berbeda, gunakan nilai yang benar pada `.env` di tahap berikutnya. Lokasi instalasi Laragon bisa berbeda-beda; yang penting layanan MySQL aktif dan CMD dapat terhubung ke host/port yang ditulis di `.env`.

**Hasil yang diharapkan:** database kosong `pengaduan_db` terlihat di daftar database.  
**Uji:** segarkan daftar database; pastikan tidak ada tabel yang dibuat manual.  
**Error umum:** jika koneksi MySQL ditolak, cek status MySQL, port (umumnya 3306), username, dan password di HeidiSQL/phpMyAdmin.  
**Checklist:** [ ] MySQL berjalan [ ] `pengaduan_db` dibuat [ ] tidak membuat tabel secara manual.

## Tahap 4 — Konfigurasi `.env`

**Tujuan:** mengarahkan project ke MySQL Laragon.  
**Persiapan:** database tersedia; project sudah dibuat.  
**Terminal:** CMD hanya untuk membersihkan cache konfigurasi setelah edit.  
**Direktori:** `D:\AplikasiPengaduan`.

Buka `D:\AplikasiPengaduan\.env` dengan VS Code. Ubah bagian konfigurasi database menjadi berikut; jika password MySQL lokal tidak kosong, isi `DB_PASSWORD` sesuai konfigurasi komputer:

```dotenv
APP_NAME="Sistem Pengaduan"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengaduan_db
DB_USERNAME=root
DB_PASSWORD=
```

Pertahankan pengaturan lain yang sudah ada di `.env`, seperti `CACHE_STORE`, `SESSION_DRIVER`, dan `QUEUE_CONNECTION`; jangan menyalin atau membagikan nilai rahasia. Jika `APP_KEY` kosong, dari CMD root project jalankan:

```bat
cd /d D:\AplikasiPengaduan
php artisan key:generate
php artisan config:clear
```

**Penjelasan:** `.env` berisi konfigurasi khusus mesin lokal. Jangan mengubah `config/database.php` untuk sekadar mengganti koneksi, dan jangan commit `.env` ke Git.

**Uji:** setelah migration pada bab 2 selesai, koneksi akan teruji langsung. Sebelum migration, periksa kembali nama database, host, port, username, dan password.

**Error umum:** `Access denied` berarti username/password salah; `Unknown database` berarti nama DB belum dibuat atau berbeda; `Connection refused` biasanya MySQL mati atau port tidak tepat. Sesuaikan `.env`, lalu jalankan `php artisan config:clear`.

**Checklist:** [ ] `DB_CONNECTION=mysql` [ ] nama database cocok [ ] `.env` tidak dibagikan [ ] APP_KEY telah tersedia.

## Tahap 5 — Persiapan direktori dan perintah Artisan

**Tujuan:** membiasakan pola kerja agar semua perintah berikutnya dijalankan pada project yang tepat.  
**Persiapan:** buka VS Code melalui **File > Open Folder** dan pilih `D:\AplikasiPengaduan`.  
**Terminal:** CMD di VS Code (Terminal > New Terminal) atau CMD biasa.  
**Direktori:** selalu root `D:\AplikasiPengaduan`.

```bat
cd /d D:\AplikasiPengaduan
dir artisan
php artisan --version
```

Baris prompt CMD seharusnya berakhir dengan `D:\AplikasiPengaduan>`. Perintah Artisan pada bab-bab selanjutnya mengasumsikan kondisi itu, kecuali instruksi menyebut lain.

**Hasil yang diharapkan:** file `artisan` terlihat dan Laravel 12.x tampil.  
**Error umum:** jika folder yang dibuka bukan root, pilih folder project yang tepat; jangan jalankan Artisan dari `D:\` atau folder `www`.  
**Checklist:** [ ] VS Code membuka root project [ ] terminal berada di root [ ] Artisan mendeteksi Laravel 12.
