<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kelurahan extends Model {
    use HasFactory;

    protected $table = 'kelurahans';

    protected $fillable = [
        'kecamatan_id',
        'nama',
        'polygon',
        'foto',
        'kode_pos',
        'luas_area',
        'alamat_kantor',
        'no_telp',
        'email',
        'website',
        'nama_lurah',
        'nip_lurah',
        'visi',
        'misi',
        'deskripsi',
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
     * Get polygon as GeoJSON string.
     */
    public function getPolygonGeojsonAttribute(): ?string
    {
        if (! $this->polygon) {
            return null;
        }

        return DB::selectOne(
            'SELECT ST_AsGeoJSON(polygon) as geojson FROM kelurahans WHERE id = ?',
            [$this->id]
        )->geojson ?? null;
    }

    /**
     * Set polygon from GeoJSON array/string.
     */
    public function setPolygonFromGeojson(array|string $geojson): void
    {
        $json = is_array($geojson) ? json_encode($geojson) : $geojson;

        DB::statement(
            'UPDATE kelurahans SET polygon = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?',
            [$json, $this->id]
        );
    }

    /**
     * Scope: select with polygon as GeoJSON.
     */
    public function scopeWithPolygonGeojson($query)
    {
        return $query->addSelect(DB::raw('ST_AsGeoJSON(polygon) as polygon_geojson'));
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function rws()
    {
        return $this->hasMany(Rw::class);
    }
}

