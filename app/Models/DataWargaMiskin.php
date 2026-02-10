<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataWargaMiskin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_warga_miskin';

    protected $fillable = [
        'nik',
        'nama',
        'no_kk',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'jenis_bantuan',
        'no_peserta',
        'status',
        'keterangan',
        'petugas_input',
        'tgl_input',
    ];

    protected $casts = [
        'tgl_input' => 'date',
    ];

    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(DataPenduduk::class, 'nik', 'nik');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }
}
