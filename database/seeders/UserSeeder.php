<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Membuat akun awal untuk setiap role,
     * supaya bisa langsung dipakai testing tanpa
     * harus daftar manual satu-satu.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'Admin',
            'email' => 'admin@mail.com',
            'no_telp' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'nama' => 'Petugas',
            'email' => 'petugas@mail.com',
            'no_telp' => '081234567891',
            'role' => 'petugas',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'nama' => 'Customer',
            'email' => 'customer@mail.com',
            'no_telp' => '081234567892',
            'role' => 'customer',
            'password' => Hash::make('password'),
        ]);
    }
}