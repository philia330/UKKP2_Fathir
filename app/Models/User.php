<?php

// ============================================================
// NAMESPACE
// Namespace = "alamat folder" class ini.
// File ini ada di folder: app/Models
// ============================================================
namespace App\Models;

// ============================================================
// IMPORT (use)
// "use" = memanggil class lain supaya bisa dipakai di file ini.
// ============================================================

// BARIS DI BAWAH SENGAJA DIMATIKAN (diawali tanda //, artinya komentar).
// MustVerifyEmail = fitur "wajib verifikasi email" setelah mendaftar.
// Karena dimatikan, user TIDAK perlu verifikasi email untuk bisa login.
// use Illuminate\Contracts\Auth\MustVerifyEmail;

// HasFactory = TRAIT (paket fitur siap pakai) untuk membuat data user palsu/contoh
// lewat "factory". Biasanya dipakai saat seeding atau testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// HasMany = tipe relasi "satu punya banyak".
// Dipakai sebagai return type (jenis nilai kembalian) method pengaduans().
use Illuminate\Database\Eloquent\Relations\HasMany;

// Authenticatable = class induk khusus untuk model yang BISA LOGIN.
// Isinya fitur login bawaan Laravel (cek password, ingat user, dan lain-lain).
// Ditulis "as Authenticatable" artinya diberi nama panggilan (alias),
// karena nama aslinya "User" sama dengan nama class kita.
use Illuminate\Foundation\Auth\User as Authenticatable;

// Notifiable = TRAIT agar user bisa menerima notifikasi (misalnya email).
use Illuminate\Notifications\Notifiable;

/**
 * ============================================================
 * CLASS: User (MODEL)
 * ============================================================
 * Class = "cetakan" yang berisi properti (variable) dan method (fungsi).
 *
 * Model User mewakili tabel "users", yaitu tempat semua akun disimpan:
 * admin, petugas, dan customer. Perbedaan akun dilihat dari kolom "role".
 *
 * "extends Authenticatable" artinya class ini MEWARISI fitur dari
 * Authenticatable. Authenticatable sendiri sudah turunan dari Model.
 * Jadi User punya DUA kemampuan sekaligus:
 *   1. Fitur database (ambil, tambah, ubah, hapus data).
 *   2. Fitur LOGIN (Auth::check(), Auth::user(), dan lain-lain).
 *
 * Model ini juga yang dibaca oleh RoleMiddleware lewat Auth::user()->role.
 */
class User extends Authenticatable
{
    // ------------------------------------------------------------
    // TRAIT: HasFactory dan Notifiable
    // ------------------------------------------------------------
    // "use" di DALAM class artinya memasang TRAIT.
    // Trait = paket method siap pakai yang "ditempelkan" ke class.
    // (Beda dengan "use" di atas file yang artinya import class.)
    //
    //   - HasFactory : menambah kemampuan User::factory() untuk membuat
    //                  data contoh (dipakai di seeder/test).
    //   - Notifiable : menambah kemampuan mengirim notifikasi ke user.
    //
    // Baris komentar @use di atasnya hanya petunjuk untuk editor kode (IDE)
    // supaya tahu factory mana yang dipakai. Tidak mempengaruhi program.
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * ------------------------------------------------------------
     * PROPERTI (VARIABLE bertipe array): $fillable
     * ------------------------------------------------------------
     * Properti = variable yang berada di dalam class.
     * "protected" artinya hanya bisa diakses oleh class ini dan
     * turunannya.
     *
     * Fungsi : daftar kolom yang BOLEH diisi sekaligus lewat "mass assignment".
     *
     * Mass assignment = mengisi banyak kolom dalam satu perintah, contohnya:
     *   User::create([
     *       'nama'     => 'Budi',
     *       'email'    => 'budi@mail.com',
     *       'no_telp'  => '08123456789',
     *       'role'     => 'customer',
     *       'password' => 'rahasia123',
     *   ]);
     *
     * Hanya kolom yang tertulis di sini yang diterima Laravel, kolom lain diabaikan.
     * Jadi $fillable itu seperti "daftar tamu yang boleh masuk".
     *
     * Arti tiap kolom:
     *   - 'nama'     -> nama lengkap user
     *   - 'email'    -> alamat email (biasanya dipakai untuk login)
     *   - 'no_telp'  -> nomor telepon
     *   - 'role'     -> peran user: admin, petugas, atau customer
     *   - 'password' -> kata sandi
     */
    protected $fillable = [
        'nama',
        'email',
        'no_telp',
        'role',
        'password',
    ];

    /**
     * ------------------------------------------------------------
     * PROPERTI (VARIABLE bertipe array): $hidden
     * ------------------------------------------------------------
     * Fungsi : daftar kolom yang DISEMBUNYIKAN saat data user diubah menjadi
     * JSON atau array.
     *
     * Contoh: saat controller menjalankan return response()->json($pengaduan)
     * dan di dalamnya ikut data user, kolom password tidak akan ikut terkirim
     * ke browser. Ini penting demi KEAMANAN supaya password tidak bocor.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * ------------------------------------------------------------
     * METHOD: casts()
     * ------------------------------------------------------------
     * Method = fungsi di dalam class.
     * Cast = aturan "ubah otomatis" sebuah kolom ke bentuk tertentu
     * saat disimpan atau dibaca.
     * ": array" = return type, artinya method ini mengembalikan array.
     *
     * Fungsi : menentukan perubahan otomatis untuk kolom tertentu.
     *
     * 'password' => 'hashed' artinya:
     *   Setiap kali kolom password DIISI, Laravel otomatis mengubahnya menjadi
     *   teks acak yang tidak bisa dibaca (hash) sebelum disimpan ke database.
     *   Contoh: "rahasia123" disimpan menjadi "$2y$12$Xk9...".
     *   Jadi password asli tidak pernah tersimpan polos di database.
     *
     * Akibatnya, saat menyimpan password, JANGAN memakai Hash::make() lagi
     * di controller. Kalau di-hash dua kali, password tidak akan cocok dan
     * user tidak bisa login.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * ------------------------------------------------------------
     * METHOD RELASI: pengaduans()
     * ------------------------------------------------------------
     * "public" artinya boleh dipanggil dari luar class.
     * ": HasMany" = return type, artinya method ini mengembalikan relasi "punya banyak".
     *
     * Fungsi : menghubungkan User dengan Pengaduan.
     *
     * Hubungannya: SATU user (customer) punya BANYAK pengaduan (one to many).
     *
     * CARA KERJA:
     *   $this->hasMany(Pengaduan::class)
     *     - $this             = objek user yang sedang dipakai.
     *     - hasMany(...)      = "user ini punya banyak ...".
     *     - Pengaduan::class  = model tujuan relasi (tabel pengaduan).
     *
     *   Laravel menebak kolom penghubung dari nama model ini + "_id",
     *   yaitu "user_id" di tabel pengaduan. Kolom itu sudah ada di
     *   $fillable model Pengaduan.
     *
     * Pasangan relasinya ada di model Pengaduan, yaitu method user()
     * dengan belongsTo (pengaduan MILIK satu user).
     *
     * CONTOH PEMAKAIAN:
     *   $user = User::find(1);
     *   $user->pengaduans;          -> semua pengaduan milik user id 1
     *   $user->pengaduans->count(); -> jumlah pengaduannya
     *   Auth::user()->pengaduans;   -> pengaduan milik user yang sedang login
     */
    public function pengaduans(): HasMany
    {
        // Mengembalikan relasi "satu user punya banyak pengaduan".
        return $this->hasMany(Pengaduan::class);
    }
}