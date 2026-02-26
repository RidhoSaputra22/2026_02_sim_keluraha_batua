<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class AgendaKegiatan extends Model
{
    use Auditable;

    protected $table = 'agenda_kegiatans';

    protected $fillable = [
        'kelurahan_id',
        'hari_kegiatan',
        'jam',
        'lokasi',
        'instansi_id',
        'perihal',
        'penanggung_jawab',
        'keterangan',
        'arsip_path',
    ];

    protected $casts = [
        'hari_kegiatan' => 'date',
    ];

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }

    public function hasil()
    {
        return $this->hasOne(HasilKegiatan::class, 'agenda_id');
    }
}
