<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penandatanganan extends Model {
    use HasFactory, Auditable;

    protected $table = 'penandatanganans';

    protected $fillable = [
        'pegawai_id',
        'status',
        'no_telp',
        'tgl_input',
        'petugas_input_id'
    ];

    protected $casts = [
        'tgl_input' => 'datetime'
    ];


    public function pegawai()
    {
        return $this->belongsTo(PegawaiStaff::class, 'pegawai_id');
    }
}

