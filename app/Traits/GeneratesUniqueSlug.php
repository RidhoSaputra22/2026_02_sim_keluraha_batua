<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesUniqueSlug
{
    protected static function uniqueSlugFor(string $source, string $field = 'slug', ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($source);
        $slug = $baseSlug !== '' ? $baseSlug : 'item';
        $counter = 2;

        while (static::query()
            ->where($field, $slug)
            ->when($ignoreId, fn ($query) => $query->where((new static())->getKeyName(), '!=', $ignoreId))
            ->exists()) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'item') . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
