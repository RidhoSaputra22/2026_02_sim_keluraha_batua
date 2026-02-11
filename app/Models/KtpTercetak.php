<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KtpTercetak extends Model {
    use HasFactory;

    protected $table = 'ktp_tercetaks';

    protected $fillable = [
        'penduduk_id',
        'tgl_buat',
        'petugas_input_id'
    ];

    protected $casts = [
        'tgl_buat' => 'date'
    ];


    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }
}

