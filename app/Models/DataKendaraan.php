<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataKendaraan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_kendaraan';

    protected $fillable = [
        'jenis_kendaraan',
        'jenis_barang',
        'nama_pemilik',
        'nik_pemilik',
        'nama_pengemudi',
        'no_polisi',
        'no_rangka',
        'no_mesin',
        'merek_type',
        'warna',
        'tahun_pembuatan',
        'tahun_perolehan',
        'wilayah_penugasan',
        'status',
        'keterangan',
        'petugas_input',
        'tgl_input',
        'arsip',
    ];

    protected $casts = [
        'tahun_pembuatan' => 'integer',
        'tahun_perolehan' => 'integer',
        'tgl_input'       => 'date',
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
