<?php

namespace App\Models;

use App\Traits\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use GeneratesUniqueSlug;
    use HasFactory;

    protected $table = 'beritas';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'ringkasan',
        'isi',
        'gambar',
        'is_featured',
        'is_published',
        'published_at',
        'author_id',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $berita) {
            if (blank($berita->slug)) {
                $berita->slug = static::uniqueSlugFor($berita->judul, 'slug', $berita->id);
            }

            if ($berita->is_published && blank($berita->published_at)) {
                $berita->published_at = now();
            }

            if (! $berita->is_published) {
                $berita->published_at = null;
            }
        });
    }

    public static function kategoriOptions(): array
    {
        return [
            'berita' => 'Berita',
            'pengumuman' => 'Pengumuman',
            'kegiatan' => 'Kegiatan',
            'agenda' => 'Agenda',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeLatestPublished($query)
    {
        return $query->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
