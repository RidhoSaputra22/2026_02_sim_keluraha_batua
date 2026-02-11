<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WargaMiskin extends Model {
    use HasFactory;

    protected $table = 'warga_miskins';

    protected $fillable = [
        'kelurahan_id',
        'penduduk_id',
        'rt_id',
        'rw_id',
        'no_peserta',
        'keterangan'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }
}

