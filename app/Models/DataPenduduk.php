<?php

namespace App\Models;

use App\Enums\JenisKelaminEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataPenduduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_penduduk';

    protected $fillable = [
        'nik',
        'nama',
        'no_kk',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'gol_darah',
        'agama',
        'status_kawin',
        'pekerjaan',
        'pendidikan',
        'kewarganegaraan',
        'rt',
        'rw',
        'alamat',
        'kecamatan',
        'kelurahan',
        'status_data',
        'keterangan',
        'petugas_input',
        'tgl_input',
    ];

    protected $casts = [
        'jenis_kelamin' => JenisKelaminEnum::class,
        'tanggal_lahir' => 'date',
        'tgl_input'     => 'date',
    ];

    /**
     * Get the keluarga that owns the penduduk.
     */
    public function keluarga(): BelongsTo
    {
        return $this->belongsTo(DataKeluarga::class, 'no_kk', 'no_kk');
    }

    /**
     * Get the surat records for the penduduk.
     */
    public function suratKeluar(): HasMany
    {
        return $this->hasMany(DaftarSuratKeluar::class, 'nik', 'nik');
    }

    /**
     * Get the petugas that input this record.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }

    /**
     * Scope query untuk penduduk aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('status_data', 'aktif');
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
