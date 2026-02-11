<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisSurat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_surat';

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'format_nomor',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function templateSurat()
    {
        return $this->hasMany(TemplateSurat::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
