<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananSuratPersyaratan extends Model
{
    use HasFactory;

    protected $table = 'layanan_surat_persyaratans';

    protected $fillable = [
        'layanan_surat_id',
        'nama',
        'keterangan',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function layananSurat()
    {
        return $this->belongsTo(LayananSurat::class);
    }
}
