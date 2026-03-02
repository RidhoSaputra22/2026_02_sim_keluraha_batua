<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Kontrakan extends Model
{
    use Auditable;

    protected $table = 'kontrakans';

    protected $fillable = [
        'kelurahan_id',
        'rw_id',
        'rt_id',
        'nama',
        'alamat',
        'pemilik',
        'jumlah_kontrakan',
        'jumlah_kost_putera',
        'jumlah_kost_putri',
        'jumlah_kost_campur',
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
     * Total semua unit kost.
     */
    public function getTotalKostAttribute(): int
    {
        return ($this->jumlah_kost_putera ?? 0)
             + ($this->jumlah_kost_putri ?? 0)
             + ($this->jumlah_kost_campur ?? 0);
    }
}
