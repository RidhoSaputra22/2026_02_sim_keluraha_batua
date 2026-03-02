<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Asrama extends Model
{
    use Auditable;

    protected $table = 'asramas';

    protected $fillable = [
        'kelurahan_id',
        'rw_id',
        'rt_id',
        'nama',
        'alamat',
        'jenis',
        'jumlah',
        'keterangan',
    ];

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * Daftar jenis asrama yang tersedia.
     */
    public static function jenisOptions(): array
    {
        return [
            'TNI'        => 'TNI',
            'POLRI'      => 'POLRI',
            'Mahasiswa'  => 'Mahasiswa',
            'Kerukunan'  => 'Kerukunan',
        ];
    }
}
