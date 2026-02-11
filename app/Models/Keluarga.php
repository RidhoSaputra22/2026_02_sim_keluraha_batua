<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model {
    use HasFactory;

    protected $table = 'keluargas';

    protected $fillable = [
        'no_kk',
        'kepala_keluarga_id',
        'jumlah_anggota_keluarga',
        'rt_id',
        'tgl_input',
        'petugas_input_id',
        'arsip_path'
    ];

    protected $casts = [
        'tgl_input' => 'datetime'
    ];


    public function kepalaKeluarga()
    {
        return $this->belongsTo(Penduduk::class, 'kepala_keluarga_id');
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function anggota()
    {
        return $this->hasMany(Penduduk::class, 'keluarga_id');
    }
}

