<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pk5 extends Model {
    use HasFactory;

    protected $table = 'pk5s';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'pangkat',
        'status',
        'no_telp',
        'tgl_input',
        'petugas_input_id'
    ];

    protected $casts = [
        'tgl_input' => 'datetime'
    ];

}

