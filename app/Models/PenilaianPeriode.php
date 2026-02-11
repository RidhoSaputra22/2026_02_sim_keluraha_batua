<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPeriode extends Model {
    use HasFactory;

    protected $table = 'penilaian_periodes';

    protected $fillable = [
        'kelurahan_id',
        'nama_periode',
        'tgl_mulai',
        'tgl_selesai'
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function details()
    {
        return $this->hasMany(PenilaianRtRwDetail::class, 'periode_id');
    }
}

