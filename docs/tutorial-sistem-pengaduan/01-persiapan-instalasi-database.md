# Persiapan, Instalasi Laravel, dan Database

## 1. Cek alat (CMD)

```bat
php -v
composer --version
```

Nyalakan **MySQL** di Laragon.

## 2. Buat project (CMD)

```bat
cd /d D:\
composer create-project laravel/laravel AplikasiPengaduan "^12.0"
cd /d D:\AplikasiPengaduan
php artisan --version
```

## 3. Buat database (HeidiSQL / phpMyAdmin)

Buat database kosong: `pengaduan_db` (collation `utf8mb4_unicode_ci`)

## 4. Edit file `.env`

Buka `D:\AplikasiPengaduan\.env`, ubah bagian ini:

```dotenv
APP_NAME="Sistem Pengaduan"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengaduan_db
DB_USERNAME=root
DB_PASSWORD=
```

## 5. Generate key (CMD)

```bat
cd /d D:\AplikasiPengaduan
php artisan key:generate
php artisan config:clear
```

## 6. Buka project di VS Code

**File > Open Folder** → `D:\AplikasiPengaduan`