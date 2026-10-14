# 2. Migration dan Model

## Apa itu migration?

Migration adalah file perintah untuk membuat atau mengubah tabel database. Pada aplikasi ini, semua tabel dibuat dalam **satu file migration**, sama seperti susunan migration aplikasi yang sudah ada.

## Tahap 1 — Menyiapkan satu file migration

**Tujuan:** membuat satu file yang membuat semua tabel aplikasi.
**Persiapan:** bab 1 selesai. Pastikan `.env` memakai database MySQL `pengaduan_db`.
**Terminal:** Command Prompt (CMD).
**Direktori:** `D:\AplikasiPengaduan`.

Laravel baru biasanya sudah memiliki migration bawaan untuk user, cache, dan queue. Agar tidak terjadi migration ganda, hapus file migration bawaan di `database/migrations` sebelum membuat file gabungan:

- File yang namanya berakhiran `create_users_table.php`
- File yang namanya berakhiran `create_cache_table.php`
- File yang namanya berakhiran `create_jobs_table.php`

Hapus file-file tersebut dari VS Code Explorer. Jangan hapus folder `database/migrations` dan jangan menghapus file di folder `vendor`. Jika migration bawaan sudah pernah berjalan pada SQLite, tidak masalah: tutorial ini memakai database MySQL `pengaduan_db` yang berbeda.

Di CMD, buat **satu** migration:

```bat
cd /d D:\AplikasiPengaduan
php artisan make:migration create_application_tables
```

CMD akan membuat satu file baru di `database/migrations`, dengan nama mirip `2026_10_14_000000_create_application_tables.php`. Angka tanggal/jam di awal bisa berbeda. Buka file baru itu, lalu ganti seluruh isinya dengan kode lengkap berikut.

**Lokasi file:** `D:\AplikasiPengaduan\database\migrations\nama_file_yang_dibuat_artisan.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menyimpan akun admin, petugas, dan customer.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('no_telp', 20)->nullable();
            $table->string('role')->default('customer');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Menyediakan tabel token reset password bawaan Laravel.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Menyimpan sesi login jika SESSION_DRIVER memakai database.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Menyediakan tabel untuk penyimpanan cache Laravel.
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Mengunci data cache saat sedang diproses.
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // Menyediakan tabel untuk pekerjaan antrean Laravel.
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        // Menyimpan informasi kelompok pekerjaan antrean.
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        // Mencatat pekerjaan antrean yang gagal.
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Menyimpan jenis atau kategori pengaduan.
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Menyimpan pengaduan dan hubungan ke user serta kategori.
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->onDelete('set null');
            $table->text('pengaduan');
            $table->string('foto')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Hapus tabel pengaduan lebih dulu karena terhubung ke user dan kategori.
        Schema::dropIfExists('pengaduan');
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
```

**Arti tabelnya:**

- `users`: akun dan role pengguna.
- `kategori`: daftar jenis pengaduan.
- `pengaduan`: isi laporan, pemilik, kategori, foto, dan status.
- `sessions`, `cache`, `jobs`, serta tabel pendukungnya: tabel yang disediakan untuk fitur Laravel.

Pada tabel `pengaduan`, `user_id` menghubungkan laporan ke akun. `kategori_id` menghubungkan laporan ke kategori. Jika akun dihapus, laporannya ikut terhapus. Jika kategori dihapus, laporan tetap ada tetapi kategorinya dikosongkan.

**Hasil yang diharapkan:** hanya ada **satu file migration** buatan project di `database/migrations`, yaitu file `create_application_tables` yang berisi semua tabel di atas.

**Cara memeriksa:** di VS Code Explorer buka `database/migrations`. Pastikan file migration bawaan users/cache/jobs sudah dihapus dan file gabungan sudah ada. Jangan membuat migration kategori atau pengaduan terpisah.

**Error umum:** jika CMD mengatakan folder tidak ditemukan, pastikan sudah menjalankan `cd /d D:\AplikasiPengaduan`. Jika ada lebih dari satu file migration project yang membuat tabel-tabel tadi, hapus duplikat sebelum menjalankan migration.

**Checklist sebelum lanjut:**

- [ ] CMD berada di `D:\AplikasiPengaduan`.
- [ ] Perintah `make:migration` sudah membuat satu file gabungan.
- [ ] Kode lengkap sudah disalin ke file tersebut.
- [ ] Migration bawaan pembuat tabel users/cache/jobs tidak tersisa.

## Tahap 2 — Membuat Model

**Tujuan:** membuat Model agar controller dapat membaca dan menyimpan data tabel.
**Persiapan:** migration sudah dibuat dan disimpan.
**Terminal:** CMD.
**Direktori:** `D:\AplikasiPengaduan`.

Jalankan perintah berikut untuk membuat Model kategori dan pengaduan. Model `User` sudah dibuat oleh Laravel.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:model Kategori
php artisan make:model Pengaduan
```

### Model User

Buka `app/Models/User.php`, lalu ganti isinya:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Kolom-kolom yang boleh disimpan menggunakan User::create().
    protected $fillable = ['nama', 'email', 'no_telp', 'role', 'password'];

    // Jangan tampilkan password dan token sebagai data biasa.
    protected $hidden = ['password', 'remember_token'];

    // Laravel otomatis menyimpan password dalam bentuk hash.
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Satu user bisa memiliki banyak pengaduan.
    public function pengaduans(): HasMany
    {
        return $this->hasMany(Pengaduan::class);
    }
}
```

### Model Kategori

Buka `app/Models/Kategori.php`, lalu ganti isinya:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;

    // Nama tabel di database adalah kategori.
    protected $table = 'kategori';

    protected $fillable = ['nama', 'deskripsi'];

    // Satu kategori bisa dipakai oleh banyak pengaduan.
    public function pengaduans(): HasMany
    {
        return $this->hasMany(Pengaduan::class);
    }
}
```

### Model Pengaduan

Buka `app/Models/Pengaduan.php`, lalu ganti isinya:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengaduan extends Model
{
    // Nama tabel di database adalah pengaduan.
    protected $table = 'pengaduan';

    protected $fillable = [
        'user_id',
        'kategori_id',
        'pengaduan',
        'foto',
        'status',
    ];

    // Setiap pengaduan dimiliki satu user.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Setiap pengaduan memiliki satu kategori.
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }
}
```

**Penjelasan singkat:** `$fillable` menentukan kolom yang boleh diisi aplikasi. Fungsi `pengaduans`, `user`, dan `kategori` menghubungkan Model dengan tabel lain.

**Cara memeriksa:** pastikan file `User.php`, `Kategori.php`, dan `Pengaduan.php` tersimpan di `app/Models`.

**Error umum:** jika perintah `make:model` mengatakan file sudah ada, jangan membuat file kedua; buka dan edit file yang sudah ada. Jika nama tabel atau kolom tidak cocok, samakan dengan kode migration di atas.

**Checklist sebelum lanjut:**

- [ ] Tiga Model tersedia di `app/Models`.
- [ ] Nama tabel `kategori` dan `pengaduan` sesuai migration.
- [ ] Nama kolom `$fillable` sama dengan kolom database.
- [ ] Relasi user-kategori-pengaduan sudah disalin.

## Tahap 3 — Menjalankan migration

**Tujuan:** membuat tabel di database MySQL.
**Persiapan:** MySQL Laragon menyala, `.env` menunjuk ke `pengaduan_db`, dan hanya satu file migration gabungan tersedia.
**Terminal:** CMD.
**Direktori:** `D:\AplikasiPengaduan`.

Jalankan:

```bat
cd /d D:\AplikasiPengaduan
php artisan config:clear
php artisan migrate:status
php artisan migrate
```

`config:clear` membuat Laravel membaca ulang pengaturan `.env`. `migrate:status` menampilkan status migration. `migrate` menjalankan file migration dan membuat tabel.

**Hasil yang diharapkan:** CMD menampilkan migration berhasil. Di database `pengaduan_db` terdapat tabel `users`, `kategori`, `pengaduan`, dan tabel pendukung Laravel seperti `sessions`, `cache`, serta `jobs`.

**Cara menguji:** buka database `pengaduan_db` di HeidiSQL atau phpMyAdmin, lalu periksa daftar tabel.

**Error umum:**

- `Connection refused`: nyalakan MySQL di Laragon dan periksa `DB_HOST` serta `DB_PORT` pada `.env`.
- `Access denied`: periksa `DB_USERNAME` dan `DB_PASSWORD`.
- `Unknown database`: buat database `pengaduan_db` atau samakan nilai `DB_DATABASE`.
- `Table already exists`: pastikan file migration bawaan yang membuat tabel sama sudah dihapus dan tidak ada tabel lama di database. Jangan gunakan `migrate:fresh` jika database berisi data yang ingin disimpan.

**Checklist sebelum lanjut:**

- [ ] `php artisan migrate` selesai tanpa error.
- [ ] Semua tabel terlihat di database MySQL.
- [ ] `database/migrations` berisi satu file migration project.
- [ ] Model sudah cocok dengan tabel dan kolom.
