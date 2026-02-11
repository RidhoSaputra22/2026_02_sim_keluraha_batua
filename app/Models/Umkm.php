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
        'sektor_umkm',
        'jenis_usaha_id',
        'status',
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

    public function jenisUsaha()
    {
        return $this->belongsTo(JenisUsaha::class);
    }
}

