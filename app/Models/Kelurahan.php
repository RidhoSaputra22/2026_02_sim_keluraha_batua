<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model {
    use HasFactory;

    protected $table = 'kelurahans';

    protected $fillable = [
        'kecamatan_id',
        'nama',
        'foto',
        'kode_pos',
        'luas_area',
        'alamat_kantor',
        'no_telp',
        'email',
        'website',
        'nama_lurah',
        'nip_lurah',
        'visi',
        'misi',
        'deskripsi',
        'batas_utara',
        'batas_selatan',
        'batas_timur',
        'batas_barat',
    ];

    protected function casts(): array
    {
        return [
            'luas_area' => 'decimal:2',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function rws()
    {
        return $this->hasMany(Rw::class);
    }

    public function petaPolygon()
    {
        return $this->hasOne(PetaLayerPolygon::class);
    }
}
