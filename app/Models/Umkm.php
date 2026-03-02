<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umkm extends Model {

    protected $table = 'umkms';

    protected $fillable = [
        'kelurahan_id',
        'peta_layer_polygon_id',
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
        'latitude',
        'longitude',
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

    public function petaLayerPolygon()
    {
        return $this->belongsTo(PetaLayerPolygon::class);
    }
}

