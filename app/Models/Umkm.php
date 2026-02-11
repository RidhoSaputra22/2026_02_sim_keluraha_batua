<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model {
    use HasFactory;

    protected $table = 'umkms';

    protected $fillable = [
        'kelurahan_id',
        'rt_id',
        'penduduk_id',
        'nama_pemilik',
        'nik_pemilik',
        'no_hp',
        'nama_ukm',
        'alamat',
        'sektor_umkm'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }
}

