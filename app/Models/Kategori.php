<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * Menggunakan nama singular karena tidak mengikuti
     * konvensi jamak Laravel.
     */
    protected $table = 'kategori';

    /**
     * Kolom yang boleh diisi lewat mass assignment.
     */
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    /**
     * Relasi ke model Pengaduan.
     *
     * Setiap kategori dapat memiliki banyak pengaduan.
     */
    public function pengaduans(): HasMany
    {
        return $this->hasMany(Pengaduan::class);
    }
}
