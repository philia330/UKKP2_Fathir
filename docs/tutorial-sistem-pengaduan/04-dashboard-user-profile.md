# Dashboard, CRUD User/Customer, dan Profile

## 1. Buat controller (CMD)

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller DashboardController
php artisan make:controller Admin/UserController --resource
php artisan make:controller Petugas/CustomerController
php artisan make:controller ProfileController
```

File yang terbentuk (semuanya ganti seluruh isi):

```
app/Http/Controllers/DashboardController.php
app/Http/Controllers/Admin/UserController.php       (folder Admin dibuat otomatis)
app/Http/Controllers/Petugas/CustomerController.php (folder Petugas dibuat otomatis)
app/Http/Controllers/ProfileController.php
```

## 2. Cek route (CMD)

```bat
php artisan route:list
```

Catatan: kalau error `Class ... not found`, itu normal sampai `PengaduanController` dan `Customer\PengaduanController` dibuat di bab berikutnya.