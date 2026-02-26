<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class HasilKegiatan extends Model {
    use Auditable;

    protected $table = 'hasil_kegiatans';

    protected $fillable = [
        'agenda_id',
        'hari_tanggal',
        'notulen',
        'keterangan',
        'arsip_path'
    ];

    protected $casts = [
        'hari_tanggal' => 'datetime'
    ];


    public function agenda()
    {
        return $this->belongsTo(AgendaKegiatan::class, 'agenda_id');
    }
}

