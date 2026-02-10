<?php

namespace App\Models;

use App\Enums\JenisKelaminEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesaWismaPkk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'desa_wisma_pkk';

    protected $fillable = [
        'nik',
        'nama',
        'no_kk',
        'kecamatan',
        'kelurahan',
        'rt',
        'rw',
        'alamat',
        'jenis_kelamin',
        'agama',
        'status_kawin',
        'pendidikan',
        'status_data',
        'petugas_input',
        'tgl_input',
        'arsip',
    ];

    protected $casts = [
        'jenis_kelamin' => JenisKelaminEnum::class,
        'tgl_input'     => 'date',
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
