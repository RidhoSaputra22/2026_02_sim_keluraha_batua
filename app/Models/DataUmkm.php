<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataUmkm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_umkm';

    protected $fillable = [
        'nik',
        'nama_pemilik',
        'no_hp',
        'nama_umkm',
        'jenis_usaha',
        'sektor_umkm',
        'alamat',
        'rt',
        'rw',
        'kecamatan',
        'kelurahan',
        'tahun_berdiri',
        'modal_usaha',
        'omzet_bulanan',
        'jumlah_karyawan',
        'status_usaha',
        'keterangan',
        'petugas_input',
        'tgl_input',
    ];

    protected $casts = [
        'tgl_input'      => 'date',
        'tahun_berdiri'  => 'integer',
        'modal_usaha'    => 'decimal:2',
        'omzet_bulanan'  => 'decimal:2',
        'jumlah_karyawan'=> 'integer',
    ];

    /**
     * Get the pemilik (penduduk) of the UMKM.
     */
    public function pemilik(): BelongsTo
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
     * Scope query untuk UMKM aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('status_usaha', 'aktif');
    }

    /**
     * Scope query untuk filter by RT/RW.
     */
    public function scopeWilayah($query, $rt = null, $rw = null)
    {
        return $query->when($rt, fn($q) => $q->where('rt', $rt))
                     ->when($rw, fn($q) => $q->where('rw', $rw));
    }

    /**
     * Scope query untuk filter by jenis usaha.
     */
    public function scopeJenisUsaha($query, $jenis)
    {
        return $query->where('jenis_usaha', $jenis);
    }
}
