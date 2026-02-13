<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model {
    use HasFactory;

    protected $table = 'rts';

    protected $fillable = [
        'rw_id',
        'nomor'
    ];


    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function penduduks()
    {
        return $this->hasMany(Penduduk::class);
    }

    public function keluargas()
    {
        return $this->hasMany(Keluarga::class);
    }

    public function umkms()
    {
        return $this->hasMany(Umkm::class);
    }
}

