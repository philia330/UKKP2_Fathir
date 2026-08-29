<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengaduan extends Model
{
    /**
     * Nama tabel di database.
     *
     * Perlu disebutkan karena nama tabel "pengaduan"
     * tidak mengikuti aturan penamaan jamak bawaan Laravel.
     */
    protected $table = 'pengaduan';

    /**
     * Kolom yang boleh diisi lewat mass assignment.
     */
    protected $fillable = [
        'user_id',
        'kategori_id',
        'pengaduan',
        'foto',
        'status',
    ];

    /**
     * Relasi ke model User.
     *
     * Setiap pengaduan dimiliki oleh satu user (customer).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Kategori.
     *
     * Setiap pengaduan memiliki satu kategori.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }
}