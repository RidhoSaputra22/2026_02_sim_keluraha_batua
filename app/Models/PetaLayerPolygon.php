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
        'jenis', // point, polygon, multipolygon, dsb
        'nama',
        'deskripsi',
        'warna',
        'rw_id',
        'kelurahan_id',
        'properties',
        'sort_order',
    ];

    // ── Boot ────────────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $layer) {
            if (empty($layer->sort_order)) {
                $maxSort = self::max('sort_order');
                $layer->sort_order = $maxSort + 10; // Tambahkan gap 10 untuk memudahkan penyisipan
            }
        });
    }


    /**
     * Jenis geometry: 'point', 'polygon', 'multipolygon', dst.
     */
    public function isPoint(): bool
    {
        return $this->jenis === 'point';
    }

    public function isPolygon(): bool
    {
        return $this->jenis === 'polygon' || $this->jenis === 'multipolygon';
    }

    protected $casts = [
        'properties' => 'array',
    ];

    // ── Relationships ───────────────────────────────────────
    public function layer()
    {
        return $this->belongsTo(PetaLayer::class, 'peta_layer_id');
    }

    public function sekolah()
    {
        return $this->hasOne(Sekolah::class);
    }

    public function faskes()
    {
        return $this->hasOne(Faskes::class);
    }

    public function tempatIbadah()
    {
        return $this->hasOne(TempatIbadah::class);
    }

    public function kontrakan()
    {
        return $this->hasOne(Kontrakan::class);
    }

    public function asrama()
    {
        return $this->hasOne(Asrama::class);
    }

    public function umkm()
    {
        return $this->hasOne(Umkm::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
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
