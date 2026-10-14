<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Provider utama aplikasi.
 *
 * File ini dipakai untuk mendaftarkan layanan aplikasi dan
 * melakukan pengaturan awal sebelum request diproses.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Mendaftarkan layanan tambahan aplikasi.
     *
     * Biasanya di sini kita menyiapkan binding, singleton,
     * atau konfigurasi service yang dibutuhkan project.
     */
    public function register(): void
    {
        // Saat ini belum ada layanan khusus yang perlu didaftarkan.
    }

    /**
     * Menjalankan konfigurasi awal setelah semua layanan aplikasi siap.
     *
     * Di sini bisa ditambahkan logika boot seperti view composer,
     * middleware global, atau pengaturan tambahan framework.
     */
    public function boot(): void
    {
        // Saat ini belum ada konfigurasi tambahan yang dibutuhkan.
    }
}
