<?php

namespace App\Models;

use App\Traits\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenPublik extends Model
{
    use GeneratesUniqueSlug;
    use HasFactory;

    protected $table = 'dokumen_publiks';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'deskripsi',
        'file_path',
        'cover_image',
        'mime_type',
        'file_size',
        'is_published',
        'published_at',
        'author_id',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $dokumen) {
            if (blank($dokumen->slug)) {
                $dokumen->slug = static::uniqueSlugFor($dokumen->judul, 'slug', $dokumen->id);
            }

            if ($dokumen->is_published && blank($dokumen->published_at)) {
                $dokumen->published_at = now();
            }

            if (! $dokumen->is_published) {
                $dokumen->published_at = null;
            }
        });
    }

    public static function kategoriOptions(): array
    {
        return [
            'transparansi' => 'Transparansi',
            'pengumuman' => 'Pengumuman',
            'formulir' => 'Formulir',
            'laporan' => 'Laporan',
            'regulasi' => 'Regulasi',
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
