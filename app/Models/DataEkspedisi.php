<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataEkspedisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_ekspedisi';

    protected $fillable = [
        'nama_ekspedisi',
        'ekspedisi',
        'nik_pemilik',
        'pemilik_usaha',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'penanggung_jawab',
        'telp_hp',
        'email',
        'kegiatan_ekspedisi',
        'jenis_layanan',
        'status',
        'keterangan',
        'petugas_input',
        'tgl_input',
    ];

    protected $casts = [
        'tgl_input' => 'date',
    ];

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(DataPenduduk::class, 'nik_pemilik', 'nik');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }
}
