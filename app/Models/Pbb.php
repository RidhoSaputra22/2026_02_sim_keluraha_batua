<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Pbb extends Model
{
    use Auditable;

    protected $table = 'pbbs';

    protected $fillable = [
        'kelurahan_id',
        'rw_id',
        'rt_id',
        'nama_wajib_pajak',
        'objek_pajak',
        'nob',
        'beban',
        'status',
        'tahun_pajak',
        'keterangan',
    ];

    protected $casts = [
        'beban' => 'decimal:2',
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

    public static function statusOptions(): array
    {
        return [
            'Lunas' => 'Lunas',
            'Belum' => 'Belum',
        ];
    }
}
