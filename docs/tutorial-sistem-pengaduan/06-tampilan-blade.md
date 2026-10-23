# Tampilan Blade, Bootstrap 5, dan Ikon

## 1. Buat folder dan file (VS Code Explorer)

Di dalam `resources/views`, buat folder dan file berikut. Isi tiap file dengan kode Anda.

```
resources/views/
├── layouts/
│   └── app.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── dashboard/
│   ├── admin.blade.php
│   ├── petugas.blade.php
│   └── customer.blade.php
├── users/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── kategori/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── customers/
│   ├── index.blade.php
│   └── create.blade.php
├── profile/
│   └── show.blade.php
└── pengaduan/
    ├── create.blade.php
    ├── customer-index.blade.php
    └── manage.blade.php
```

Total: 6 folder baru (`layouts`, `auth`, `dashboard`, `users`, `kategori`, `customers`, `profile`, `pengaduan`) dan 18 file.

## 2. Bersihkan cache tampilan (CMD)

```bat
cd /d D:\AplikasiPengaduan
php artisan view:clear
```