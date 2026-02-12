<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    use HasFactory, Auditable;

    protected $table = 'template_surats';

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
        return $this->belongsTo(SuratJenis::class, 'jenis_surat_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
