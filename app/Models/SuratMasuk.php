<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratMasuk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_masuk';

    protected $fillable = [
        'no_surat',
        'jenis_surat',
        'sifat_surat',
        'asal_surat',
        'perihal',
        'tanggal_surat',
        'tanggal_diterima',
        'disposisi',
        'status',
        'petugas_input',
        'tgl_input',
        'arsip',
    ];

    protected $casts = [
        'tanggal_surat'    => 'date',
        'tanggal_diterima' => 'date',
        'tgl_input'        => 'date',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }
}
