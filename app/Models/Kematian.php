<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kematian extends Model
{
    use HasFactory;

    protected $table = 'kematians';

    protected $fillable = [
        'penduduk_id',
        'tanggal_meninggal',
        'tempat_meninggal',
        'penyebab',
        'no_akte_kematian',
        'keterangan',
        'petugas_id',
    ];

    protected $casts = [
        'tanggal_meninggal' => 'date',
    ];

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
