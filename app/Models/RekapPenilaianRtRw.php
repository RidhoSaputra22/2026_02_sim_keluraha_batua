<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapPenilaianRtRw extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rekap_penilaian_rt_rw';

    protected $fillable = [
        'nik',
        'nama',
        'jabatan',
        'rt',
        'rw',
        'tanggal',
        'periode_penilaian',
        'nilai',
        'kelurahan',
        'keterangan',
        'arsip',
    ];

    protected $casts = [
        'tanggal'           => 'date',
        'periode_penilaian' => 'date',
        'nilai'             => 'decimal:2',
    ];

    public function rtRw(): BelongsTo
    {
        return $this->belongsTo(DataRtRw::class, 'nik', 'nik');
    }
}
