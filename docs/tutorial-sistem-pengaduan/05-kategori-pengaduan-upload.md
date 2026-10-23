# Kategori, Pengaduan, Status, dan Upload Foto

## 1. Buat controller (CMD)

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller Admin/KategoriController --resource
php artisan make:controller Customer/PengaduanController
php artisan make:controller PengaduanController
```

File yang terbentuk (semuanya ganti seluruh isi):

```
app/Http/Controllers/Admin/KategoriController.php      (folder Admin sudah ada)
app/Http/Controllers/Customer/PengaduanController.php  (folder Customer dibuat otomatis)
app/Http/Controllers/PengaduanController.php
```

## 2. Link storage untuk foto (CMD)

```bat
php artisan storage:link
```

## 3. Cek route (CMD)

```bat
php artisan route:list
```

Catatan: setelah semua controller dibuat, `route:list` seharusnya tidak error lagi.