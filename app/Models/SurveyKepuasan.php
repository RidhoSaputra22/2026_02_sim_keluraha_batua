<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyKepuasan extends Model {
    use HasFactory, Auditable;

    protected $table = 'survey_kepuasans';

    protected $fillable = [
        'kelurahan_id',
        'jenis_kelamin',
        'umur',
        'pendidikan',
        'pekerjaan',
        'jenis_layanan_id',
        'jumlah_nilai',
        'nilai_rata_rata'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function jenisLayanan()
    {
        return $this->belongsTo(SurveyLayanan::class, 'jenis_layanan_id');
    }
}

