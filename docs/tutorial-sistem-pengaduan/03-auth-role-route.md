# Register, Login, Role, Middleware, dan Route

## 1. Buat controller dan middleware (CMD)

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller AuthController
php artisan make:middleware RoleMiddleware
```

File yang terbentuk / diedit:

```
app/Http/Controllers/AuthController.php     (baru, ganti isi)
app/Http/Middleware/RoleMiddleware.php      (baru, ganti isi)
bootstrap/app.php                           (sudah ada, ganti isi: daftarkan alias 'role')
routes/web.php                              (sudah ada, ganti isi)
```

## 2. Bersihkan cache route (CMD)

```bat
php artisan route:clear
```

## 3. Cek route (CMD)

```bat
php artisan route:list
```

Catatan: kalau error `Class ... not found`, itu normal sampai controller lain (`DashboardController`, dll.) dibuat.