<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanRtRw extends Model {
    use HasFactory;

    protected $table = 'jabatan_rt_rw';

    protected $fillable = [
        'nama'
    ];


    public function pengurus()
    {
        return $this->hasMany(RtRwPengurus::class, 'jabatan_id');
    }
}

