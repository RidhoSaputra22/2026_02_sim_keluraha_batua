<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempatIbadah extends Model {
    use HasFactory;

    protected $table = 'tempat_ibadahs';

    protected $fillable = [
        'kelurahan_id',
        'tempat_ibadah',
        'nama',
        'alamat',
        'rt_id',
        'rw_id',
        'pengurus',
        'arsip_path'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }
}

