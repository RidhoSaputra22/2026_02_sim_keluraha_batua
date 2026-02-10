<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataKeluarga extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_keluarga';

    protected $fillable = [
        'no_kk',
        'nama_kepala_keluarga',
        'nik_kepala_keluarga',
        'jumlah_anggota_keluarga',
        'alamat',
        'rt',
        'rw',
        'kecamatan',
        'kelurahan',
        'status',
        'petugas_input',
        'tgl_input',
        'arsip',
    ];

    protected $casts = [
        'tgl_input' => 'date',
    ];

    /**
     * Get all anggota keluarga (penduduk).
     */
    public function penduduk(): HasMany
    {
        return $this->hasMany(DataPenduduk::class, 'no_kk', 'no_kk');
    }

    /**
     * Get the kepala keluarga.
     */
    public function kepalaKeluarga(): BelongsTo
    {
        return $this->belongsTo(DataPenduduk::class, 'nik_kepala_keluarga', 'nik');
    }

    /**
     * Get the petugas that input this record.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }

    /**
     * Scope query untuk filter by RT/RW.
     */
    public function scopeWilayah($query, $rt = null, $rw = null)
    {
        return $query->when($rt, fn($q) => $q->where('rt', $rt))
                     ->when($rw, fn($q) => $q->where('rw', $rw));
    }
}
