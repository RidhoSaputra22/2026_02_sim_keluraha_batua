<?php

namespace App\Models;

use App\Enums\JenisKelaminEnum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenilaianMasyarakat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penilaian_masyarakat';

    protected $fillable = ["jenis_kelamin", "umur", "pendidikan", "pekerjaan", "jenis_layanan", "jumlah_nilai", "nilai_rata_rata", "wilayah_penugasan"];


    protected $casts = [
        'jenis_kelamin' => JenisKelaminEnum::class,
    ];
}
