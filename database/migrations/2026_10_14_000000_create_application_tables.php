<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat seluruh tabel aplikasi dalam satu file migrasi.
     */
    public function up(): void
    {
        // Tabel users menyimpan akun, kredensial login, dan role setiap pengguna.
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID unik untuk setiap pengguna.
            $table->string('nama'); // Nama lengkap pengguna.
            $table->string('email')->unique(); // Email untuk login dan harus unik.
            $table->string('no_telp', 20)->nullable(); // Nomor telepon boleh tidak diisi.
            $table->string('role')->default('customer'); // Role bawaan akun baru.
            $table->timestamp('email_verified_at')->nullable(); // Waktu verifikasi email jika digunakan.
            $table->string('password'); // Password disimpan dalam bentuk hash oleh aplikasi.
            $table->rememberToken(); // Token untuk fitur mengingat sesi login.
            $table->timestamps(); // Waktu data dibuat dan diperbarui.
        });

        // Tabel password_reset_tokens menyimpan token untuk proses lupa password.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email menjadi identitas unik permintaan reset.
            $table->string('token'); // Token verifikasi untuk reset password.
            $table->timestamp('created_at')->nullable(); // Waktu token dibuat.
        });

        // Tabel sessions menyimpan sesi login pengguna.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // ID unik untuk setiap sesi browser.
            $table->foreignId('user_id')->nullable()->index(); // User pemilik sesi, jika sudah login.
            $table->string('ip_address', 45)->nullable(); // Alamat IP pengguna.
            $table->text('user_agent')->nullable(); // Informasi browser/perangkat pengguna.
            $table->longText('payload'); // Isi data sesi yang disimpan Laravel.
            $table->integer('last_activity')->index(); // Waktu aktivitas terakhir untuk sesi.
        });

        // Tabel cache menyimpan data sementara jika cache menggunakan database.
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary(); // Nama unik data cache.
            $table->mediumText('value'); // Nilai data yang disimpan di cache.
            $table->integer('expiration'); // Waktu kedaluwarsa data cache.
        });

        // Tabel cache_locks mengatur kunci agar proses cache tidak saling bertabrakan.
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary(); // Nama unik kunci cache.
            $table->string('owner'); // Pemilik kunci yang sedang memakai cache.
            $table->integer('expiration'); // Waktu kedaluwarsa kunci.
        });

        // Tabel jobs menyimpan pekerjaan yang diproses melalui queue Laravel.
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // ID unik untuk setiap pekerjaan.
            $table->string('queue')->index(); // Nama antrean pekerjaan.
            $table->longText('payload'); // Data pekerjaan yang harus dijalankan.
            $table->unsignedTinyInteger('attempts'); // Jumlah percobaan pemrosesan.
            $table->unsignedInteger('reserved_at')->nullable(); // Waktu pekerjaan mulai diambil.
            $table->unsignedInteger('available_at'); // Waktu pekerjaan siap dijalankan.
            $table->unsignedInteger('created_at'); // Waktu pekerjaan dimasukkan ke antrean.
        });

        // Tabel job_batches menyimpan kelompok pekerjaan queue yang dijalankan bersama.
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary(); // ID unik untuk kelompok pekerjaan.
            $table->string('name'); // Nama kelompok pekerjaan.
            $table->integer('total_jobs'); // Jumlah seluruh pekerjaan dalam kelompok.
            $table->integer('pending_jobs'); // Jumlah pekerjaan yang belum selesai.
            $table->integer('failed_jobs'); // Jumlah pekerjaan yang gagal.
            $table->longText('failed_job_ids'); // Daftar ID pekerjaan yang gagal.
            $table->mediumText('options')->nullable(); // Pengaturan tambahan untuk kelompok.
            $table->integer('cancelled_at')->nullable(); // Waktu kelompok dibatalkan, jika ada.
            $table->integer('created_at'); // Waktu kelompok dibuat.
            $table->integer('finished_at')->nullable(); // Waktu semua pekerjaan selesai.
        });

        // Tabel failed_jobs mencatat pekerjaan queue yang gagal diproses.
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id(); // ID unik untuk catatan pekerjaan gagal.
            $table->string('uuid')->unique(); // UUID unik pekerjaan.
            $table->text('connection'); // Koneksi queue yang digunakan.
            $table->text('queue'); // Nama antrean pekerjaan.
            $table->longText('payload'); // Data pekerjaan yang gagal.
            $table->longText('exception'); // Rincian error saat pekerjaan gagal.
            $table->timestamp('failed_at')->useCurrent(); // Waktu kegagalan dicatat.
        });

        // Tabel kategori berisi pilihan jenis masalah untuk pengaduan.
        Schema::create('kategori', function (Blueprint $table) {
            $table->id(); // ID unik kategori.
            $table->string('nama')->unique(); // Nama kategori harus berbeda.
            $table->text('deskripsi')->nullable(); // Penjelasan kategori boleh kosong.
            $table->timestamps(); // Waktu kategori dibuat dan diperbarui.
        });

        // Tabel pengaduan menyimpan laporan yang dikirim customer.
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id(); // ID unik untuk setiap pengaduan.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pemilik laporan; laporan ikut terhapus jika user dihapus.
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->onDelete('set null'); // Kategori boleh kosong dan di-null-kan jika kategori dihapus.
            $table->text('pengaduan'); // Isi atau uraian masalah yang dilaporkan.
            $table->string('foto')->nullable(); // Lokasi foto bukti, jika disertakan.
            $table->enum('status', ['menunggu', 'diproses', 'selesai'])->default('menunggu'); // Status awal dan pilihan status laporan.
            $table->timestamps(); // Waktu pengaduan dibuat dan diperbarui.
        });
    }

    /**
     * Menghapus tabel dari belakang ke depan agar relasi foreign key tetap aman.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan'); // Hapus tabel anak sebelum tabel kategori dan user.
        Schema::dropIfExists('kategori'); // Hapus tabel kategori setelah pengaduan.
        Schema::dropIfExists('failed_jobs'); // Hapus catatan queue yang gagal.
        Schema::dropIfExists('job_batches'); // Hapus kelompok queue.
        Schema::dropIfExists('jobs'); // Hapus antrean queue.
        Schema::dropIfExists('cache_locks'); // Hapus kunci cache.
        Schema::dropIfExists('cache'); // Hapus data cache.
        Schema::dropIfExists('sessions'); // Hapus sesi login.
        Schema::dropIfExists('password_reset_tokens'); // Hapus token reset password.
        Schema::dropIfExists('users'); // Hapus tabel user paling akhir.
    }
};
