<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ekspedisi extends Model {
    use HasFactory, Auditable;

    protected $table = 'ekspedisis';

    protected $fillable = [
        'kelurahan_id',
        'pemilik_usaha',
        'ekspedisi',
        'alamat',
        'penanggung_jawab',
        'telp_hp',
        'kegiatan_ekspedisi'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }
}

