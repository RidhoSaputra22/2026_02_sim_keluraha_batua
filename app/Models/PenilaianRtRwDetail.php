<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianRtRwDetail extends Model {
    use HasFactory;

    protected $table = 'penilaian_rt_rw_details';

    protected $fillable = [
        'periode_id',
        'pengurus_id',
        'nilai',
        'standar_nilai',
        'usulan_nilai_insentif',
        'lpj_path',
        'arsip_path',
        'created_by'
    ];


    public function periode()
    {
        return $this->belongsTo(PenilaianPeriode::class, 'periode_id');
    }

    public function pengurus()
    {
        return $this->belongsTo(RtRwPengurus::class, 'pengurus_id');
    }
}

