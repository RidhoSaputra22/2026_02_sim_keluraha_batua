<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanRekapSuratMasuk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan_rekap_surat_masuk';

    protected $fillable = [
        'kelurahan_desa',
        'no_surat',
        'jenis_surat',
        'sifat_surat',
        'asal_surat',
        'tanggal_diterima',
    ];

    protected $casts = [
        'tanggal_diterima' => 'date',
    ];
}
