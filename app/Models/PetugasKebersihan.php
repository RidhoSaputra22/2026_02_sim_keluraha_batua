<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetugasKebersihan extends Model {
    use HasFactory, Auditable;

    protected $table = 'petugas_kebersihans';

    protected $fillable = [
        'kelurahan_id',
        'penduduk_id',
        'nama',
        'nik',
        'unit_kerja',
        'jenis_kelamin',
        'pekerjaan',
        'lokasi',
        'status'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }
}

