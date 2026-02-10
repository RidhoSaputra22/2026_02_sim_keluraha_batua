<?php

namespace App\Models;

use App\Enums\JenisKelaminEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendudukAsing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penduduk_asing';

    protected $fillable = [
        'no_passport',
        'nama',
        'negara_asal',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'kewarganegaraan',
        'kecamatan',
        'kelurahan',
        'rt',
        'rw',
        'alamat',
        'keperluan',
        'masa_tinggal',
        'sponsor',
        'tgl_input',
        'petugas_input',
        'arsip',
    ];

    protected $casts = [
        'jenis_kelamin' => JenisKelaminEnum::class,
        'tanggal_lahir' => 'date',
        'tgl_input' => 'date',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }

    public function scopeWilayah($query, $rt = null, $rw = null)
    {
        return $query->when($rt, fn ($q) => $q->where('rt', $rt))
            ->when($rw, fn ($q) => $q->where('rw', $rw));
    }
}
