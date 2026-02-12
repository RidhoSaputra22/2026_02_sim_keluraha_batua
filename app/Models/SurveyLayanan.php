<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyLayanan extends Model {
    use HasFactory;

    protected $table = 'survey_layanans';

    protected $fillable = [
        'nama'
    ];


    public function surveyKepuasan()
    {
        return $this->hasMany(SurveyKepuasan::class, 'jenis_layanan_id');
    }
}

