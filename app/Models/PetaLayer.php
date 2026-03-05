<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PetaLayer extends Model
{
    use HasFactory;

    protected $table = 'peta_layers';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'warna',
        'fill_opacity',
        'stroke_width',
        'pattern_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'fill_opacity' => 'float',
        'stroke_width' => 'float',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Boot ────────────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $layer) {
            if (empty($layer->slug)) {
                $layer->slug = Str::slug($layer->nama);
            }
        });
    }

    // ── Relationships ───────────────────────────────────────
    public function polygons()
    {
        return $this->hasMany(PetaLayerPolygon::class);
    }

    // ── Layer Slugs (konvensi per jenis data) ───────────────
    public const LAYER_SEKOLAH        = 'sekolah';
    public const LAYER_FASKES         = 'fasilitas-kesehatan';
    public const LAYER_TEMPAT_IBADAH  = 'tempat-ibadah';
    public const LAYER_KONTRAKAN_KOST = 'kontrakan-kost';
    public const LAYER_ASRAMA         = 'asrama';
    public const LAYER_DATA_USAHA     = 'data-usaha';
    public const LAYER_WILAYAH_RW      = 'wilayah-rw';

    /**
     * Daftar layer default beserta konfigurasi warna.
     */
    public static function facilityLayers(): array
    {
        return [
            self::LAYER_SEKOLAH        => ['nama' => 'Sekolah',              'warna' => '#2563EB', 'sort' => 10],
            self::LAYER_FASKES         => ['nama' => 'Fasilitas Kesehatan',  'warna' => '#DC2626', 'sort' => 20],
            self::LAYER_TEMPAT_IBADAH  => ['nama' => 'Tempat Ibadah',        'warna' => '#059669', 'sort' => 30],
            self::LAYER_KONTRAKAN_KOST => ['nama' => 'Kontrakan & Kost',     'warna' => '#D97706', 'sort' => 40],
            self::LAYER_ASRAMA         => ['nama' => 'Asrama',               'warna' => '#7C3AED', 'sort' => 50],
            self::LAYER_DATA_USAHA     => ['nama' => 'Data Usaha',           'warna' => '#EA580C', 'sort' => 60],
        ];
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('nama');
    }

    // ── Pattern Types ───────────────────────────────────────
    public static function patternTypes(): array
    {
        return [
            'solid' => 'Solid',
            'hatch' => 'Arsir Diagonal',
            'dots' => 'Titik-titik',
            'crosshatch' => 'Arsir Silang',
        ];
    }
}
