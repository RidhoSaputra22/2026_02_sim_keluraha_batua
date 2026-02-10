<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratHimbauan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_himbauan';

    protected $fillable = [
        'no_surat',
        'sifat_surat',
        'asal_surat',
        'tanggal_surat',
        'tujuan_surat',
        'perihal',
        'uraian',
        'petugas_input',
        'arsip',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }
}
