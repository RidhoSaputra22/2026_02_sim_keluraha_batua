<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instansi extends Model {

    protected $table = 'instansis';

    protected $fillable = [
        'nama',
        'alamat',
        'telp'
    ];


    public function surats()
    {
        return $this->hasMany(Surat::class);
    }
}

