<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemohon extends Model {
    use HasFactory;

    protected $table = 'pemohons';

    protected $fillable = [
        'penduduk_id',
        'nama',
        'no_hp_wa',
        'email'
    ];


    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function surats()
    {
        return $this->hasMany(Surat::class);
    }
}

