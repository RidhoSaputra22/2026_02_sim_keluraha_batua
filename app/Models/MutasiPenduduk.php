<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiPenduduk extends Model
{
    use HasFactory;

    protected $table = 'mutasi_penduduks';

    protected $fillable = [
        'penduduk_id',
        'jenis_mutasi',
        'tanggal_mutasi',
        'alamat_asal',
        'alamat_tujuan',
        'rt_asal_id',
        'rt_tujuan_id',
        'alasan',
        'keterangan',
        'no_surat_pindah',
        'status',
        'petugas_id',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
    ];

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function rtAsal()
    {
        return $this->belongsTo(Rt::class, 'rt_asal_id');
    }

    public function rtTujuan()
    {
        return $this->belongsTo(Rt::class, 'rt_tujuan_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
