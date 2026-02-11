<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesaWismaPkkEntry extends Model {
    use HasFactory;

    protected $table = 'desa_wisma_pkk_entries';

    protected $fillable = [
        'penduduk_id',
        'keluarga_id',
        'rt_id',
        'kelurahan_id',
        'keterangan'
    ];


    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }
}

