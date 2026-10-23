# Seeder, Akun Pengujian, Menjalankan Aplikasi, dan Pengujian

## 1. Buat seeder (CMD)

```bat
cd /d D:\AplikasiPengaduan
php artisan make:seeder UserSeeder
php artisan make:seeder KategoriSeeder
```

File yang terbentuk / diedit (semuanya ganti seluruh isi):

```
database/seeders/UserSeeder.php       (baru)
database/seeders/KategoriSeeder.php   (baru)
database/seeders/DatabaseSeeder.php   (sudah ada)
```

## 2. Jalankan seeder (CMD)

```bat
php artisan db:seed
```

Kalau database perlu direset total:

```bat
php artisan migrate:fresh --seed
```

## 3. Jalankan aplikasi (CMD)

Nyalakan MySQL di Laragon, lalu:

```bat
cd /d D:\AplikasiPengaduan
php artisan serve
```

Buka alamat yang tampil di terminal (biasanya `http://127.0.0.1:8000`).

## 4. Pengujian (browser)

1. Register customer baru, lalu login
2. Login admin, cek dashboard
3. Admin: CRUD user dan kategori
4. Petugas: lihat dan tambah customer
5. Customer: buat pengaduan, dengan dan tanpa foto
6. Petugas/admin: ubah status pengaduan
7. Profile: ubah nama dan nomor telepon
8. Batas akses: customer membuka `/admin/users` harus 403
9. Logout: halaman terproteksi tidak bisa dibuka lagi