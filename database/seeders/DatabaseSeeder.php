<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Menjalankan seeder untuk mengisi data awal.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}