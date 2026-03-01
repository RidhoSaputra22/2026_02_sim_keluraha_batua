<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PetaLayerPolygon extends Model
{
    use HasFactory;

    protected $table = 'peta_layer_polygons';

    protected $fillable = [
        'peta_layer_id',
        'nama',
        'deskripsi',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    // ── Relationships ───────────────────────────────────────
    public function layer()
    {
        return $this->belongsTo(PetaLayer::class, 'peta_layer_id');
    }

    // ── PostGIS Methods ─────────────────────────────────────

    /**
     * Get polygon as GeoJSON string.
     */
    public function getPolygonGeojsonAttribute(): ?string
    {
        return DB::selectOne(
            'SELECT ST_AsGeoJSON(polygon) as geojson FROM peta_layer_polygons WHERE id = ?',
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
            'UPDATE peta_layer_polygons SET polygon = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?',
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
}
