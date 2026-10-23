# Migration dan Model

## 1. Hapus migration bawaan (VS Code Explorer)

Di folder `database/migrations`, hapus file:

- `...create_users_table.php`
- `...create_cache_table.php`
- `...create_jobs_table.php`

## 2. Buat migration (CMD)

```bat
cd /d D:\AplikasiPengaduan
php artisan make:migration create_application_tables
```

File baru yang dibuat:

```
database/migrations/xxxx_xx_xx_xxxxxx_create_application_tables.php
```

Isi file ini dengan kode migration Anda (ganti seluruh isi).

## 3. Buat model (CMD)

```bat
php artisan make:model Kategori
php artisan make:model Pengaduan
```

File yang terbentuk / diedit:

```
app/Models/User.php        (sudah ada, ganti isi)
app/Models/Kategori.php    (baru, ganti isi)
app/Models/Pengaduan.php   (baru, ganti isi)
```

## 4. Jalankan migration (CMD)

Nyalakan MySQL di Laragon, lalu:

```bat
cd /d D:\AplikasiPengaduan
php artisan config:clear
php artisan migrate
```

## 5. Cek

Database `pengaduan_db` berisi tabel `users`, `kategori`, `pengaduan`, dan tabel pendukung.