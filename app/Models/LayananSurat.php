<?php

namespace App\Models;

use App\Traits\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananSurat extends Model
{
    use GeneratesUniqueSlug;
    use HasFactory;

    protected $table = 'layanan_surats';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'icon',
        'estimasi_layanan',
        'biaya',
        'catatan',
        'kontak_petugas',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $layanan) {
            if (blank($layanan->slug)) {
                $layanan->slug = static::uniqueSlugFor($layanan->nama, 'slug', $layanan->id);
            }

            if (blank($layanan->sort_order)) {
                $layanan->sort_order = ((int) static::max('sort_order')) + 10;
            }
        });
    }

    public function persyaratans()
    {
        return $this->hasMany(LayananSuratPersyaratan::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('nama');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
