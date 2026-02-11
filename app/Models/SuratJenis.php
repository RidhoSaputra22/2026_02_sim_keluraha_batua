<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJenis extends Model {
    use HasFactory;

    protected $table = 'surat_jenis';

    protected $fillable = [
        'nama'
    ];


    public function surats()
    {
        return $this->hasMany(Surat::class, 'jenis_id');
    }
}

