<?php

namespace App\Models;

use App\Enums\JenisKelaminEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KtpTercetak extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ktp_tercetak';

    protected $fillable = [
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_kawin',
        'pendidikan',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'tgl_buat',
        'petugas_input',
    ];

    protected $casts = [
        'jenis_kelamin' => JenisKelaminEnum::class,
        'tanggal_lahir' => 'date',
        'tgl_buat'      => 'date',
    ];

    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(DataPenduduk::class, 'nik', 'nik');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }

    public function scopeWilayah($query, $rt = null, $rw = null)
    {
        return $query->when($rt, fn($q) => $q->where('rt', $rt))
                     ->when($rw, fn($q) => $q->where('rw', $rw));
    }
}
