<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model {
    use HasFactory;

    protected $table = 'kelurahans';

    protected $fillable = [
        'kecamatan_id',
        'nama'
    ];


    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function rws()
    {
        return $this->hasMany(Rw::class);
    }
}

