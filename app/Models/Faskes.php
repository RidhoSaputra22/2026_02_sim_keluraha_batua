<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faskes extends Model {
    use HasFactory;

    protected $table = 'faskes';

    protected $fillable = [
        'kelurahan_id',
        'nama_rs',
        'alamat',
        'rw_id',
        'jenis',
        'kelas',
        'jenis_pelayanan',
        'akreditasi',
        'telp'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }
}

