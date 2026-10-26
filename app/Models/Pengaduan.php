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

// Model = class induk bawaan Laravel (Eloquent ORM).
// Dari class inilah semua fitur database didapat
// (ambil, tambah, ubah, hapus data).
use Illuminate\Database\Eloquent\Model;

// BelongsTo = tipe relasi "milik satu".
// Dipakai sebagai return type (jenis nilai kembalian) method user() dan kategori().
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ============================================================
 * CLASS: Pengaduan (MODEL)
 * ============================================================
 * Class = "cetakan" yang berisi properti (variable) dan method (fungsi).
 *
 * Model = "wakil" sebuah tabel database di dalam kode PHP.
 * Model Pengaduan mewakili tabel "pengaduan", yaitu tempat semua laporan
 * dari customer disimpan. Dengan model ini kita bisa mengelola data
 * tanpa menulis query SQL manual, contohnya:
 *   Pengaduan::all();                   -> ambil semua pengaduan
 *   Pengaduan::find(5);                 -> cari pengaduan dengan id 5
 *   Pengaduan::create([...]);           -> tambah pengaduan baru
 *   $pengaduan->update(['status' => ...]); -> ubah data pengaduan
 *
 * "extends Model" artinya class ini MEWARISI semua fitur dari class Model
 * bawaan Laravel (pewarisan / inheritance).
 *
 * Model ini adalah model PUSAT di aplikasi: ia terhubung ke User (siapa yang
 * melapor) dan Kategori (jenis laporannya).
 */
class Pengaduan extends Model
{
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
     * Secara otomatis Laravel menebak nama tabel dari nama class dengan cara:
     * huruf kecil + ditambah "s". Jadi Pengaduan akan ditebak menjadi
     * tabel "pengaduans". Padahal tabel kita bernama "pengaduan" (tanpa "s"),
     * maka nama tabel harus ditulis manual di sini.
     * Tanpa baris ini akan muncul error "Table 'pengaduans' doesn't exist".
     */
    protected $table = 'pengaduan';

    /**
     * ------------------------------------------------------------
     * PROPERTI (VARIABLE bertipe array): $fillable
     * ------------------------------------------------------------
     * Array = variable yang menyimpan banyak nilai sekaligus.
     *
     * Fungsi : daftar kolom yang BOLEH diisi sekaligus lewat "mass assignment".
     *
     * Mass assignment = mengisi banyak kolom dalam satu perintah, contohnya:
     *   Pengaduan::create([
     *       'user_id'     => 1,
     *       'kategori_id' => 2,
     *       'pengaduan'   => 'Lampu kelas rusak',
     *       'status'      => 'menunggu',
     *   ]);
     *
     * Kenapa dibatasi? Demi KEAMANAN. Laravel hanya menerima kolom yang
     * tertulis di $fillable, kolom lain diabaikan. Jadi $fillable itu seperti
     * "daftar tamu yang boleh masuk".
     *
     * Arti tiap kolom:
     *   - 'user_id'     -> id user (customer) yang membuat pengaduan
     *   - 'kategori_id' -> id kategori pengaduan
     *   - 'pengaduan'   -> isi/teks laporan
     *   - 'foto'        -> foto bukti laporan (biasanya yang disimpan nama/path file-nya)
     *   - 'status'      -> status laporan: menunggu, diproses, atau selesai
     *
     * Hal penting: 'status' ada di $fillable. Itulah sebabnya perintah
     * $pengaduan->update(['status' => ...]) di PengaduanController bisa berjalan.
     * Kalau 'status' tidak ada di daftar ini, perubahan status akan ditolak diam-diam.
     */
    protected $fillable = [
        'user_id',
        'kategori_id',
        'pengaduan',
        'foto',
        'status',
    ];

    /**
     * ------------------------------------------------------------
     * METHOD RELASI: user()
     * ------------------------------------------------------------
     * Method = fungsi di dalam class.
     * "public" artinya boleh dipanggil dari luar class.
     * ": BelongsTo" = return type, artinya method ini mengembalikan relasi "milik satu".
     *
     * Fungsi : menghubungkan Pengaduan dengan User (pembuat laporan).
     *
     * Hubungannya: SATU pengaduan DIMILIKI oleh SATU user (many to one).
     * Sebaliknya, satu user bisa punya banyak pengaduan.
     *
     * CARA KERJA:
     *   $this->belongsTo(User::class)
     *     - $this            = objek pengaduan yang sedang dipakai.
     *     - belongsTo(...)   = "pengaduan ini milik ...".
     *     - User::class      = model tujuan relasi (tabel users).
     *
     *   Laravel menebak kolom penghubung dari NAMA METHOD + "_id".
     *   Nama method-nya "user", maka kolomnya "user_id". Kolom ini ada
     *   di tabel pengaduan dan sudah tercantum di $fillable di atas.
     *   Laravel lalu mencocokkan user_id dengan kolom id di tabel users.
     *
     * CONTOH PEMAKAIAN:
     *   $pengaduan->user->name;   -> nama user yang membuat laporan
     *
     * Di PengaduanController, method ini dipanggil lewat
     * Pengaduan::with(['user', 'kategori']) supaya data user ikut diambil.
     * Kata 'user' di dalam with() itu adalah NAMA METHOD ini.
     */
    public function user(): BelongsTo
    {
        // Mengembalikan relasi "pengaduan ini milik satu user".
        return $this->belongsTo(User::class);
    }

    /**
     * ------------------------------------------------------------
     * METHOD RELASI: kategori()
     * ------------------------------------------------------------
     * Fungsi : menghubungkan Pengaduan dengan Kategori (jenis laporan).
     *
     * Hubungannya: SATU pengaduan DIMILIKI oleh SATU kategori (many to one).
     * Ini adalah pasangan dari method pengaduans() di model Kategori
     * (yang memakai hasMany).
     *
     * CARA KERJA:
     *   Sama seperti user(). Nama method "kategori" maka kolom penghubungnya
     *   "kategori_id", lalu dicocokkan dengan kolom id di tabel kategori.
     *
     * CONTOH PEMAKAIAN:
     *   $pengaduan->kategori->nama;  -> nama kategori laporan tersebut
     *
     * Di controller, kata 'kategori' pada with(['user', 'kategori'])
     * adalah NAMA METHOD ini.
     */
    public function kategori(): BelongsTo
    {
        // Mengembalikan relasi "pengaduan ini milik satu kategori".
        return $this->belongsTo(Kategori::class);
    }
}