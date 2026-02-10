<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaKegiatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agenda_kegiatan';

    protected $fillable = [
        'judul_kegiatan',
        'tanggal_kegiatan',
        'hari_kegiatan',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'instansi_pengirim',
        'perihal',
        'penanggung_jawab',
        'peserta',
        'status',
        'keterangan',
        'petugas_input',
        'tgl_input',
        'arsip',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'tgl_input'        => 'date',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }
}
