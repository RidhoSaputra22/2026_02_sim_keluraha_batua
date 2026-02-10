<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DaftarSuratKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daftar_surat_keluar';

    protected $fillable = [
        'jenis_surat',
        'no_surat',
        'tanggal_surat',
        'nik',
        'nama_pemohon',
        'nama_dalam_surat',
        'no_telepon',
        'alamat',
        'keperluan',
        'status',
        'status_verifikasi',
        'status_ttd',
        'status_esign',
        'penandatangan_id',
        'verifikator_id',
        'petugas_input',
        'tgl_input',
        'catatan',
        'arsip',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tgl_input'     => 'date',
    ];

    /**
     * Get the penduduk associated with the surat.
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
     * Get the penandatangan.
     */
    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(Penandatanganan::class, 'penandatangan_id', 'id');
    }

    /**
     * Get the verifikator.
     */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id', 'id');
    }

    /**
     * Scope query untuk surat yang menunggu verifikasi.
     */
    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status_verifikasi', 'menunggu');
    }

    /**
     * Scope query untuk surat yang menunggu TTD.
     */
    public function scopeMenungguTtd($query)
    {
        return $query->where('status_verifikasi', 'disetujui')
                     ->where('status_ttd', 'menunggu');
    }

    /**
     * Scope query untuk filter by jenis surat.
     */
    public function scopeJenisSurat($query, $jenis)
    {
        return $query->where('jenis_surat', $jenis);
    }
}
