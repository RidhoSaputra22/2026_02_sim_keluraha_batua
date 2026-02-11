<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendudukAsing extends Model {
    use HasFactory;

    protected $table = 'penduduk_asings';

    protected $fillable = [
        'no_paspor',
        'nama',
        'alamat',
        'rt_id',
        'jenis_kelamin',
        'tgl_input',
        'petugas_input_id',
        'arsip_path'
    ];

    protected $casts = [
        'tgl_input' => 'datetime'
    ];


    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }
}

