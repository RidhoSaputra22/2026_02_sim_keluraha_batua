<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rw extends Model {
    use HasFactory;

    protected $table = 'rws';

    protected $fillable = [
        'kelurahan_id',
        'nomor'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function rts()
    {
        return $this->hasMany(Rt::class);
    }
}

