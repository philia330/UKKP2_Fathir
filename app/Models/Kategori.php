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

// HasFactory = TRAIT (paket fitur siap pakai) untuk membuat data palsu/contoh
// lewat "factory". Biasanya dipakai saat seeding atau testing.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Model = class induk bawaan Laravel (Eloquent ORM).
// Semua fitur database (ambil, tambah, ubah, hapus data) berasal dari sini.
use Illuminate\Database\Eloquent\Model;

// HasMany = tipe relasi "satu punya banyak".
// Dipakai di sini sebagai return type (jenis nilai kembalian) method pengaduans().
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ============================================================
 * CLASS: Kategori (MODEL)
 * ============================================================
 * Class = "cetakan" yang berisi properti (variable) dan method (fungsi).
 *
 * Model = "wakil" sebuah tabel database di dalam kode PHP.
 * Model Kategori mewakili tabel "kategori". Dengan model ini kita bisa
 * mengelola data tanpa menulis query SQL manual, contohnya:
 *   Kategori::all();                          -> ambil semua kategori
 *   Kategori::create(['nama' => 'Fasilitas']);-> tambah kategori baru
 *   Kategori::find(1);                        -> cari kategori dengan id 1
 *
 * "extends Model" artinya class ini MEWARISI semua fitur dari class Model
 * bawaan Laravel (pewarisan / inheritance). Itulah kenapa class ini
 * kodenya pendek tapi sudah bisa berbuat banyak.
 *
 * Kategori dipakai untuk mengelompokkan pengaduan, misalnya:
 * "Fasilitas", "Kebersihan", "Keamanan", dan sebagainya.
 */
class Kategori extends Model
{
    // ------------------------------------------------------------
    // TRAIT: HasFactory
    // ------------------------------------------------------------
    // "use" di DALAM class artinya memasang TRAIT.
    // Trait = paket method siap pakai yang "ditempelkan" ke class.
    // (Beda dengan "use" di atas file yang artinya import class.)
    //
    // HasFactory menambahkan kemampuan: Kategori::factory()
    // untuk membuat data contoh secara otomatis (dipakai di seeder/test).
    use HasFactory;

    /**
     * ------------------------------------------------------------
     * PROPERTI (VARIABLE milik class): $table
     * ------------------------------------------------------------
     * Properti = variable yang berada di dalam class.
     * "protected" artinya hanya bisa diakses oleh class ini dan
     * turunannya, tidak bisa diubah sembarangan dari luar.
     *
     * Fungsi : memberi tahu Laravel nama tabel yang dipakai model ini.
     *
     * Kenapa perlu ditulis?
     * Secara otomatis Laravel menebak nama tabel dengan cara: nama class
     * dijadikan huruf kecil lalu ditambah "s" (jamak bahasa Inggris).
     * Jadi Kategori akan ditebak menjadi tabel "kategoris".
     * Padahal tabel kita bernama "kategori" (tanpa "s"),
     * maka nama tabel harus ditulis manual di sini.
     * Tanpa baris ini, akan muncul error "Table 'kategoris' doesn't exist".
     */
    protected $table = 'kategori';

    /**
     * ------------------------------------------------------------
     * PROPERTI (VARIABLE bertipe array): $fillable
     * ------------------------------------------------------------
     * Array = variable yang menyimpan banyak nilai sekaligus.
     *
     * Fungsi : daftar kolom yang BOLEH diisi sekaligus lewat "mass assignment".
     *
     * Mass assignment = mengisi banyak kolom dalam satu perintah, contohnya:
     *   Kategori::create([
     *       'nama'      => 'Fasilitas',
     *       'deskripsi' => 'Pengaduan terkait sarana dan prasarana',
     *   ]);
     *
     * Kenapa perlu dibatasi? Ini untuk KEAMANAN. Bayangkan ada user nakal
     * yang menambahkan kolom tambahan di form. Laravel hanya akan menerima
     * kolom yang tertulis di $fillable, selain itu diabaikan.
     * Jadi $fillable itu seperti "daftar tamu yang boleh masuk".
     *
     * Kolom yang boleh diisi di sini:
     *   - 'nama'      -> nama kategori
     *   - 'deskripsi' -> penjelasan singkat kategori
     * (Kolom id, created_at, dan updated_at diisi otomatis oleh Laravel.)
     */
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    /**
     * ------------------------------------------------------------
     * METHOD RELASI: pengaduans()
     * ------------------------------------------------------------
     * Method = fungsi di dalam class.
     * "public" artinya boleh dipanggil dari luar class.
     * ": HasMany" = return type, artinya method ini mengembalikan relasi "punya banyak".
     *
     * Fungsi : menghubungkan Kategori dengan Pengaduan.
     *
     * Hubungannya: SATU kategori punya BANYAK pengaduan (one to many).
     * Contoh: kategori "Fasilitas" bisa berisi 20 pengaduan.
     *
     * CARA KERJA:
     *   $this->hasMany(Pengaduan::class)
     *     - $this           = objek kategori yang sedang dipakai.
     *     - hasMany(...)    = "kategori ini punya banyak ...".
     *     - Pengaduan::class= model tujuan relasi (tabel pengaduans).
     *
     *   Laravel mencari pengaduan yang kolom penghubungnya cocok dengan id kategori.
     *   Nama kolom penghubung ditebak otomatis: "kategori_id" (nama model + _id),
     *   jadi tabel pengaduans harus punya kolom kategori_id.
     *
     * Pasangan relasinya ada di model Pengaduan, yaitu method kategori()
     * dengan belongsTo (pengaduan MILIK satu kategori). Itu yang dipakai
     * di controller lewat Pengaduan::with(['user', 'kategori']).
     *
     * CONTOH PEMAKAIAN:
     *   $kategori = Kategori::find(1);
     *   $kategori->pengaduans;          -> semua pengaduan milik kategori id 1
     *   $kategori->pengaduans->count(); -> jumlah pengaduannya
     *   Kategori::withCount('pengaduans')->get(); -> semua kategori + jumlah pengaduannya
     *
     * Perhatikan: nama method memakai bentuk jamak ("pengaduans") karena
     * hasilnya banyak data. Saat dipanggil sebagai properti tidak memakai
     * tanda kurung: $kategori->pengaduans.
     */
    public function pengaduans(): HasMany
    {
        // Mengembalikan relasi "satu kategori punya banyak pengaduan".
        return $this->hasMany(Pengaduan::class);
    }
}