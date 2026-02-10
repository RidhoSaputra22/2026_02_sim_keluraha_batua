<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratLainLain extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_lain_lain';

    protected $fillable = [
        'no_surat',
        'tanggal_surat',
        'perihal',
        'tujuan_surat',
        'isi_surat',
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
