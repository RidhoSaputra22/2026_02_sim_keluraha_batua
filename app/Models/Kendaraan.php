<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model {
    use Auditable;

    protected $table = 'kendaraans';

    protected $fillable = [
        'jenis_barang',
        'nama_pengemudi',
        'no_polisi',
        'no_rangka',
        'no_mesin',
        'tahun_perolehan',
        'merek_type',
        'kelurahan_id'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }
}

