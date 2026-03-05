<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Kontrakan extends Model
{
    use Auditable;

    protected $table = 'kontrakans';

    protected $fillable = [
        'kelurahan_id',
        'peta_layer_polygon_id',
        'rw_id',
        'rt_id',
        'nama',
        'alamat',
        'pemilik',
        'no_hp_pemilik',
        'jenis_unit',
        'jumlah_kamar',
        'keterangan',
        'latitude',
        'longitude',
    ];

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function petaLayerPolygon()
    {
        return $this->belongsTo(PetaLayerPolygon::class);
    }

    /**
     * Label jenis unit.
     */
    public const JENIS_UNIT_OPTIONS = [
        'Kontrakan'    => 'Kontrakan',
        'Kost Putera'  => 'Kost Putera',
        'Kost Putri'   => 'Kost Putri',
        'Kost Campur'  => 'Kost Campur',
    ];
}
