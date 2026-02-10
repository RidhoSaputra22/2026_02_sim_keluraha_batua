<?php

namespace App\Models;

use App\Enums\JenisKelaminEnum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PetugasKebersihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'petugas_kebersihan';

    protected $fillable = ["nama", "nik", "unit_kerja", "jenis_kelamin", "pekerjaan", "lokasi", "status"];


    protected $casts = [
        'jenis_kelamin' => JenisKelaminEnum::class,
    ];
}
