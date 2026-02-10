<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanRekapSuratKeteranganDomisili extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan_rekap_surat_keterangan_domisili';

    protected $fillable = [
        'kelurahan_desa',
        'nama_layanan_publik',
        'nama_pengguna_layanan',
        'tgl_mengurus_layanan',
        'no_hp_wa_aktif',
        'email_aktif',
    ];

    protected $casts = [
        'tgl_mengurus_layanan' => 'date',
    ];
}
