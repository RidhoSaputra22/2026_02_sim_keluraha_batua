<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imb extends Model {
    use HasFactory;

    protected $table = 'imbs';

    protected $fillable = [
        'kelurahan_id',
        'nama_pemohon',
        'alamat_pemohon',
        'alamat_bangunan',
        'status_luas_tanah',
        'nama_pada_surat',
        'penggunaan_fungsi_gedung'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }
}

