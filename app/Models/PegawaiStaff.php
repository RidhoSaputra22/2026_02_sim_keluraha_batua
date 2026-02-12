<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiStaff extends Model {
    use HasFactory, Auditable;

    protected $table = 'pegawai_staff';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'gol',
        'pangkat',
        'status_pegawai',
        'tgl_input',
        'petugas_input_id',
        'no_urut'
    ];

    protected $casts = [
        'tgl_input' => 'datetime'
    ];


    public function penandatanganans()
    {
        return $this->hasMany(Penandatanganan::class, 'pegawai_id');
    }
}

