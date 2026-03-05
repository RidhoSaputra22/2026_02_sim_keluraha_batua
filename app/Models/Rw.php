<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rw extends Model {
    use HasFactory;

    protected $table = 'rws';

    protected $fillable = [
        'kelurahan_id',
        'nomor',
        'foto',
        'luas_area',
        'alamat_sekretariat',
        'no_telp',
        'deskripsi',
        'fasilitas',
        'batas_utara',
        'batas_selatan',
        'batas_timur',
        'batas_barat',
    ];

    protected function casts(): array
    {
        return [
            'luas_area' => 'decimal:2',
        ];
    }

    /**
     * Get the polygon record from peta_layer_polygons linked to this RW.
     */
    public function petaPolygon()
    {
        return $this->hasOne(PetaLayerPolygon::class, 'rw_id');
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function rts()
    {
        return $this->hasMany(Rt::class);
    }

    public function pengurus()
    {
        return $this->hasMany(RtRwPengurus::class);
    }
}

