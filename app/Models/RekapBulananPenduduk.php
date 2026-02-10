<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapBulananPenduduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rekap_bulanan_penduduk';

    protected $fillable = [
        'kelurahan',
        'periode',
        'data_laki_laki',
        'data_perempuan',
        'data_laki_laki_wna',
        'data_perempuan_wna',
        'lahir_laki_laki',
        'lahir_perempuan',
        'lahir_laki_laki_wna',
        'lahir_perempuan_wna',
        'kematian_laki_laki',
        'kematian_perempuan',
        'kematian_laki_laki_wna',
        'kematian_perempuan_wna',
        'datang_laki_laki',
        'datang_perempuan',
        'datang_laki_laki_wna',
        'datang_perempuan_wna',
        'pindah_laki_laki',
        'pindah_perempuan',
        'pindah_laki_laki_wna',
        'pindah_perempuan_wna',
        'pend_laki_laki',
        'pend_perempuan',
        'pend_laki_laki_wna',
        'pend_perempuan_wna',
    ];

    protected $casts = [
        'periode'               => 'date',
        'data_laki_laki'        => 'integer',
        'data_perempuan'        => 'integer',
        'data_laki_laki_wna'    => 'integer',
        'data_perempuan_wna'    => 'integer',
        'lahir_laki_laki'       => 'integer',
        'lahir_perempuan'       => 'integer',
        'lahir_laki_laki_wna'   => 'integer',
        'lahir_perempuan_wna'   => 'integer',
        'kematian_laki_laki'    => 'integer',
        'kematian_perempuan'    => 'integer',
        'kematian_laki_laki_wna' => 'integer',
        'kematian_perempuan_wna' => 'integer',
        'datang_laki_laki'      => 'integer',
        'datang_perempuan'      => 'integer',
        'datang_laki_laki_wna'  => 'integer',
        'datang_perempuan_wna'  => 'integer',
        'pindah_laki_laki'      => 'integer',
        'pindah_perempuan'      => 'integer',
        'pindah_laki_laki_wna'  => 'integer',
        'pindah_perempuan_wna'  => 'integer',
        'pend_laki_laki'        => 'integer',
        'pend_perempuan'        => 'integer',
        'pend_laki_laki_wna'    => 'integer',
        'pend_perempuan_wna'    => 'integer',
    ];
}
