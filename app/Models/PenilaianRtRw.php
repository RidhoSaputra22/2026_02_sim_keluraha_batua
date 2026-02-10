<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenilaianRtRw extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penilaian_rt_rw';

    protected $fillable = [
        'nik',
        'nama',
        'jabatan',
        'rt',
        'rw',
        'nilai',
        'standar_nilai',
        'usulan_nilai_insentif',
        'periode_penilaian',
        'keterangan',
        'petugas_input',
        'lpj',
        'arsip',
    ];

    protected $casts = [
        'periode_penilaian'      => 'date',
        'nilai'                  => 'decimal:2',
        'standar_nilai'          => 'decimal:2',
        'usulan_nilai_insentif'  => 'decimal:2',
    ];

    public function rtRw(): BelongsTo
    {
        return $this->belongsTo(DataRtRw::class, 'nik', 'nik');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }
}
