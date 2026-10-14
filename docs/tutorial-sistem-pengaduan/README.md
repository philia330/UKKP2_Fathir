# Tutorial Sistem Pengaduan dengan Laravel 12

Panduan praktik Serkom RPL ini menjelaskan cara membangun aplikasi Sistem Pengaduan dari awal. Ikuti bab secara berurutan dan salin kode ke project Anda sendiri. Dokumen ini **bukan** project Laravel dan seluruh perintah di dalamnya adalah instruksi untuk pembaca, bukan perintah yang dijalankan saat menyusun dokumentasi.

## Target hasil

Aplikasi memiliki tiga role:

| Role | Kemampuan |
|---|---|
| Admin | Dashboard, CRUD user dan kategori, melihat semua pengaduan, mengubah status, melihat/mengubah profil sendiri |
| Petugas | Dashboard, melihat dan menambah customer, melihat semua pengaduan, mengubah status, melihat/mengubah profil sendiri |
| Customer | Mendaftar, dashboard pribadi, mengirim pengaduan dengan foto opsional, melihat pengaduan miliknya saja, melihat/mengubah profil sendiri |

Status pengaduan: `menunggu`, `diproses`, dan `selesai`.

## Lokasi dan cara menjalankan

Root project yang dipakai konsisten di seluruh bab:

```text
D:\AplikasiPengaduan
```

Project berada di luar folder `www` Laragon. Laragon hanya dipakai untuk menyalakan layanan MySQL. Laravel dijalankan melalui `php artisan serve` dari root project; virtual host Laragon tidak diperlukan.

Semua perintah ditujukan untuk **Command Prompt (CMD)**. `cd /d` berpindah direktori sekaligus mengganti drive. Kecuali disebut lain, jalankan semua perintah setelah berada di `D:\AplikasiPengaduan`.

## Urutan belajar

1. [Persiapan, instalasi Laravel, database MySQL, dan `.env`](01-persiapan-instalasi-database.md)
2. [Migration dan Model](02-migration-model.md)
3. [Register, login, role, middleware, dan route](03-auth-role-route.md)
4. [Dashboard, pengelolaan user/customer, dan profile](04-dashboard-user-profile.md)
5. [Kategori, pengaduan, status, dan upload foto](05-kategori-pengaduan-upload.md)
6. [Layout, Bootstrap, seluruh halaman Blade, dan CSS](06-tampilan-blade.md)
7. [Seeder, akun lokal, menjalankan aplikasi, dan checklist pengujian](07-seeder-jalankan-uji.md)
8. [Troubleshooting dan persiapan wawancara Serkom](08-troubleshooting-wawancara.md)

## Cara mengikuti setiap bab

- Buat atau edit file pada path yang ditulis; path relatif dihitung dari `D:\AplikasiPengaduan`.
- Jangan menghapus bagian kode dari blok yang ditandai sebagai isi lengkap file.
- Jalankan migration hanya setelah struktur migration dan konfigurasi database sudah diperiksa.
- Akun/password contoh seeder hanya untuk pengujian lokal. Jangan memakainya di server publik.
- Saat menguji pembatasan akses, gunakan jendela privat atau logout dahulu agar sesi antar-role tidak tertukar.

## Peta alur aplikasi

```text
Browser -> routes/web.php -> middleware auth + role -> controller
        -> Model Eloquent -> MySQL
        -> resources/views (Blade) -> Browser
```

Form browser mengirim request `POST`, `PUT`, atau `DELETE` dengan token CSRF. Controller memvalidasi input sebelum menyimpan data. Foto disimpan pada `storage/app/public/foto` dan diakses melalui symbolic link `public/storage`. Password selalu di-hash oleh Laravel.

## Ringkasan database

- `users`: nama, email, nomor telepon, role, password.
- `kategori`: nama dan deskripsi kategori.
- `pengaduan`: pemilik (`user_id`), kategori (`kategori_id`), isi, path foto, status.
- Tabel pendukung Laravel seperti session, cache, dan queue tetap berasal dari migration bawaan project.

## Catatan kompatibilitas

Tutorial menggunakan Laravel Framework `^12.0` dan PHP `^8.2`. Bootstrap 5 dan Bootstrap Icons dimuat dari CDN; komputer perlu koneksi internet untuk memuat aset tersebut. Tutorial tidak memasang starter kit autentikasi, karena halaman register/login dibuat sendiri agar alur role mudah dipelajari.
