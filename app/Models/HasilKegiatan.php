<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasilKegiatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hasil_kegiatan';

    protected $fillable = [
        'agenda_kegiatan_id',
        'hari_tanggal',
        'agenda',
        'notulen',
        'hasil',
        'dokumentasi',
        'peserta_hadir',
        'keterangan',
        'petugas_input',
        'tgl_input',
        'arsip',
    ];

    protected $casts = [
        'hari_tanggal' => 'date',
        'tgl_input'    => 'date',
    ];

    public function agendaKegiatan(): BelongsTo
    {
        return $this->belongsTo(AgendaKegiatan::class, 'agenda_kegiatan_id', 'id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }
}
