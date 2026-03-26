<?php

namespace App\Models;

use App\Traits\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinasiWisata extends Model
{
    use GeneratesUniqueSlug;
    use HasFactory;

    protected $table = 'destinasi_wisatas';

    protected $fillable = [
        'nama',
        'slug',
        'kategori',
        'ringkasan',
        'deskripsi',
        'gambar',
        'alamat',
        'jam_operasional',
        'harga_tiket',
        'kontak',
        'maps_url',
        'is_featured',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $destinasi) {
            if (blank($destinasi->slug)) {
                $destinasi->slug = static::uniqueSlugFor($destinasi->nama, 'slug', $destinasi->id);
            }

            if (blank($destinasi->sort_order)) {
                $destinasi->sort_order = ((int) static::max('sort_order')) + 10;
            }
        });
    }

    public static function kategoriOptions(): array
    {
        return [
            'wisata' => 'Wisata',
            'kuliner' => 'Kuliner',
            'budaya' => 'Budaya',
            'olahraga' => 'Olahraga',
            'ruang-terbuka' => 'Ruang Terbuka',
            'layanan' => 'Layanan Publik',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
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
