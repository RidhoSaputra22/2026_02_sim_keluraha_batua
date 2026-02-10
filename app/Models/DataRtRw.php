<?php

namespace App\Models;

use App\Enums\JabatanRtRwEnum;
use App\Enums\StatusAktifEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataRtRw extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_rt_rw';

    protected $fillable = [
        'nik',
        'nama',
        'jabatan',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'alamat',
        'no_telp',
        'email',
        'tgl_mulai',
        'tgl_selesai',
        'periode',
        'status',
        'no_rekening',
        'no_npwp',
        'foto',
        'keterangan',
        'petugas_input',
        'tgl_input',
    ];

    protected $casts = [
        'jabatan'     => JabatanRtRwEnum::class,
        'status'      => StatusAktifEnum::class,
        'tgl_mulai'   => 'date',
        'tgl_selesai' => 'date',
        'tgl_input'   => 'date',
    ];

    /**
     * Get the penduduk associated with RT/RW.
     */
    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(DataPenduduk::class, 'nik', 'nik');
    }

    /**
     * Get the petugas that input this record.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }

    /**
     * Get all penduduk in this RT/RW.
     */
    public function warga(): HasMany
    {
        return $this->hasMany(DataPenduduk::class, 'rt', 'rt')
                    ->where('rw', $this->rw);
    }

    /**
     * Scope query untuk RT/RW aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', StatusAktifEnum::AKTIF);
    }

    /**
     * Scope query untuk filter by jabatan.
     */
    public function scopeJabatan($query, $jabatan)
    {
        return $query->where('jabatan', $jabatan);
    }

    /**
     * Scope query untuk filter by wilayah.
     */
    public function scopeWilayah($query, $rt = null, $rw = null)
    {
        return $query->when($rt, fn($q) => $q->where('rt', $rt))
                     ->when($rw, fn($q) => $q->where('rw', $rw));
    }
}
