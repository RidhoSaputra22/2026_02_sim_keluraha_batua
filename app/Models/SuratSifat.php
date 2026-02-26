<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratSifat extends Model {

    protected $table = 'surat_sifat';

    protected $fillable = [
        'nama'
    ];


    public function surats()
    {
        return $this->hasMany(Surat::class, 'sifat_id');
    }
}

