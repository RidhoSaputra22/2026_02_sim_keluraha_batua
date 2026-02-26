<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SuratJenis extends Model {
    use Auditable;

    protected $table = 'surat_jenis';

    protected $fillable = [
        'nama'
    ];


    public function surats()
    {
        return $this->hasMany(Surat::class, 'jenis_id');
    }
}

