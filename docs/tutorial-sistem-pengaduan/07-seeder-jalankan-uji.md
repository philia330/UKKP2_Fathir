# 7. Seeder, akun pengujian, menjalankan aplikasi, dan pengujian

## Tahap 26 — Membuat data kategori dan akun lokal

**Tujuan:** menyediakan kategori dan tiga akun role untuk pengujian tanpa mendaftarkan akun satu per satu.  
**Persiapan:** tabel MySQL berhasil dibuat melalui `php artisan migrate`.  
**Terminal:** CMD. **Direktori:** root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:seeder UserSeeder
php artisan make:seeder KategoriSeeder
```

Isi lengkap **`database/seeders/UserSeeder.php`**:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun ini hanya untuk uji coba lokal, bukan untuk server publik.
        $accounts = [
            ['nama' => 'Admin Lokal', 'email' => 'admin@pengaduan.test', 'no_telp' => '081234567890', 'role' => 'admin', 'password' => 'password123'],
            ['nama' => 'Petugas Lokal', 'email' => 'petugas@pengaduan.test', 'no_telp' => '081234567891', 'role' => 'petugas', 'password' => 'password123'],
            ['nama' => 'Customer Lokal', 'email' => 'customer@pengaduan.test', 'no_telp' => '081234567892', 'role' => 'customer', 'password' => 'password123'],
        ];

        foreach ($accounts as $account) {
            // Gunakan email sebagai kunci supaya seeding ulang tidak membuat duplikat akun.
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'nama' => $account['nama'],
                    'no_telp' => $account['no_telp'],
                    'role' => $account['role'],
                    'password' => Hash::make($account['password']),
                ]
            );
        }
    }
}
```

Isi lengkap **`database/seeders/KategoriSeeder.php`**:

```php
<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori awal muncul di dropdown form pengaduan customer.
        foreach ([
            ['nama' => 'Fasilitas', 'deskripsi' => 'Kerusakan atau masalah fasilitas.'],
            ['nama' => 'Pelayanan', 'deskripsi' => 'Keluhan terkait pelayanan.'],
            ['nama' => 'Teknologi Informasi', 'deskripsi' => 'Gangguan perangkat atau sistem informasi.'],
        ] as $kategori) {
            Kategori::updateOrCreate(['nama' => $kategori['nama']], ['deskripsi' => $kategori['deskripsi']]);
        }
    }
}
```

Ganti isi lengkap **`database/seeders/DatabaseSeeder.php`**:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Jalankan seeder akun dan kategori ketika perintah db:seed dipanggil.
        $this->call([
            UserSeeder::class,
            KategoriSeeder::class,
        ]);
    }
}
```

Jalankan dari CMD root project:

```bat
cd /d D:\AplikasiPengaduan
php artisan db:seed
```

**Akun lokal untuk pengujian:**

| Role | Email | Password |
|---|---|---|
| Admin | `admin@pengaduan.test` | `password123` |
| Petugas | `petugas@pengaduan.test` | `password123` |
| Customer | `customer@pengaduan.test` | `password123` |

Seeder memakai `updateOrCreate`, sehingga dapat dijalankan lagi tanpa membuat duplikat email. Password contoh ini hanya untuk mesin lokal. Jangan menyimpan password contoh pada website publik.

**Hasil yang diharapkan:** perintah seeder berhasil, tabel `users` memuat satu akun untuk setiap role, tabel `kategori` memuat tiga kategori.  
**Error umum:** `Class "Database\Seeders\UserSeeder" not found` biasanya karena namespace atau nama file seeder salah; jalankan `composer dump-autoload` setelah file baru dibuat. Jika email duplikat sebelumnya, `updateOrCreate` tetap memperbarui akun yang email-nya sama.

**Checklist:** [ ] seeder dipanggil dari `DatabaseSeeder` [ ] password di-hash [ ] tiga role tersedia [ ] kategori tersedia [ ] kredensial hanya dipakai lokal.

## Tahap 27 — Menjalankan aplikasi

**Tujuan:** menjalankan server pengembangan Laravel tanpa memindahkan project ke `www`.  
**Persiapan:** migration dan seeder berhasil; MySQL Laragon menyala.  
**Terminal:** CMD/terminal VS Code pertama.  
**Direktori:** root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan serve
```

Biarkan terminal tersebut tetap terbuka. Terminal akan menampilkan alamat lokal, biasanya `http://127.0.0.1:8000`. Buka alamat yang benar-benar tercetak di terminal dengan browser. Laragon digunakan untuk menyalakan MySQL; server aplikasi dijalankan Laravel dari `D:\AplikasiPengaduan`.

Tidak perlu Apache, folder `www`, atau konfigurasi virtual host Laragon. Untuk menghentikan server, fokuskan terminal server lalu tekan `Ctrl+C`.

**Hasil yang diharapkan:** halaman login terbuka. Login dengan salah satu akun pengujian di atas.  
**Error umum:** `Address already in use` berarti port 8000 sedang dipakai; jalankan `php artisan serve --port=8001` dan buka alamat yang ditampilkan. `SQLSTATE` berarti periksa MySQL dan `.env`. `No application encryption key` berarti jalankan `php artisan key:generate`, lalu mulai ulang server.

**Checklist:** [ ] MySQL hidup [ ] CMD root project [ ] `php artisan serve` berjalan [ ] browser membuka alamat yang tampil.

## Tahap 28 — Menguji fungsi dan pembatasan akses

**Tujuan:** memastikan fitur berjalan dan tidak ada akses silang antar-role.  
**Persiapan:** server aktif dan seeder telah dijalankan.  
**Terminal:** browser; terminal kedua tetap di root project jika perlu perintah Artisan.

Ikuti urutan pengujian berikut:

1. **Register:** logout; buka `/register`; daftar dengan email baru; pastikan akun dapat login sebagai customer.
2. **Login:** login sebagai `admin@pengaduan.test`; dashboard admin menampilkan jumlah user, customer, petugas, dan pengaduan.
3. **CRUD User:** sebagai admin, tambah user dengan role petugas; edit email atau nama; ubah password dengan mengisi kedua kolom password; hapus akun uji yang bukan akun login. Pastikan email duplikat ditolak.
4. **Kategori:** sebagai admin, buat kategori, ubah nama/deskripsi, hapus kategori. Pastikan kategori yang dihapus tidak membuat laporan lama hilang.
5. **Petugas:** login sebagai petugas; buka dashboard; daftar customer; tambah customer; pastikan role yang dibuat selalu customer.
6. **Customer dan privasi:** login sebagai customer; buat laporan dengan kategori dan isi minimal 10 karakter; periksa status awal `Menunggu`; buat laporan kedua tanpa foto.
7. **Upload:** kirim JPG/PNG di bawah 2 MB; klik tautan foto pada daftar customer dan daftar petugas/admin. Coba kirim file bukan gambar atau lebih dari 2 MB; validasi harus menolak.
8. **Perubahan status:** sebagai petugas, ubah satu laporan ke `Diproses`, lalu `Selesai`; login customer dan pastikan status yang baru terlihat.
9. **Profile:** ubah nama/nomor telepon masing-masing role; coba gunakan email yang sudah dipakai akun lain dan pastikan validasi menolak.
10. **Batas akses:** saat login customer, coba buka `/admin/users` dan `/petugas/dashboard`; seharusnya mendapat HTTP 403. Logout lalu coba buka `/customer/dashboard`; seharusnya diarahkan ke login.
11. **Logout:** tekan tombol Logout; pastikan kembali ke login dan URL terproteksi tidak dapat dibuka sebagai guest.

**Uji tambahan via CMD:** di terminal kedua, jalankan `php artisan route:list` untuk memastikan route tidak hilang. Jangan menjalankan `migrate:fresh` pada database yang berisi data yang ingin disimpan; perintah itu menghapus seluruh tabel.

**Checklist akhir:** [ ] register hanya membuat customer [ ] login sesuai role [ ] CRUD admin berjalan [ ] petugas hanya membuat customer [ ] pengaduan tersimpan dengan pemilik login [ ] foto bisa dibuka [ ] status berubah [ ] customer hanya melihat miliknya [ ] role lain/guest ditolak [ ] logout mengakhiri sesi.
