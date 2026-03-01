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
