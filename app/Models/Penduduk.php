<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model {
    use HasFactory;

    protected $table = 'penduduks';

    protected $fillable = [
        'nik',
        'nama',
        'alamat',
        'keluarga_id',
        'rt_id',
        'jenis_kelamin',
        'gol_darah',
        'agama',
        'status_kawin',
        'pendidikan',
        'status_data',
        'tgl_input',
        'petugas_input_id'
    ];

    protected $casts = [
        'tgl_input' => 'datetime'
    ];


    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function pemohon()
    {
        return $this->hasOne(Pemohon::class);
    }
}

