<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model {

    protected $table = 'kecamatans';

    protected $fillable = [
        'nama'
    ];


    public function kelurahans()
    {
        return $this->hasMany(Kelurahan::class);
    }
}

