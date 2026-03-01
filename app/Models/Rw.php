<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rw extends Model {
    use HasFactory;

    protected $table = 'rws';

    protected $fillable = [
        'kelurahan_id',
        'nomor',
        'polygon',
    ];

    /**
     * Get polygon as GeoJSON string.
     */
    public function getPolygonGeojsonAttribute(): ?string
    {
        if (! $this->polygon) {
            return null;
        }

        return DB::selectOne(
            'SELECT ST_AsGeoJSON(polygon) as geojson FROM rws WHERE id = ?',
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
            'UPDATE rws SET polygon = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?',
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

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function rts()
    {
        return $this->hasMany(Rt::class);
    }
}

