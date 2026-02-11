<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model {
    use HasFactory;

    protected $table = 'rts';

    protected $fillable = [
        'rw_id',
        'nomor'
    ];


    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }
}

