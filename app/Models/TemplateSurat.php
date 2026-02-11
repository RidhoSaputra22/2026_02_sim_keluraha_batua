<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateSurat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'template_surat';

    protected $fillable = [
        'jenis_surat_id',
        'nama',
        'isi_template',
        'field_mapping',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'field_mapping' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
