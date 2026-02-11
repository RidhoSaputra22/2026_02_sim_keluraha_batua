<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananPublik extends Model {
    use HasFactory;

    protected $table = 'layanan_publiks';

    protected $fillable = [
        'nama'
    ];


    public function surats()
    {
        return $this->hasMany(Surat::class);
    }
}

