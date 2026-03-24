<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class RetribusiSampah extends Model
{
    use Auditable;

    protected $table = 'retribusi_sampahs';

    protected $fillable = [
        'kelurahan_id',
        'rw_id',
        'rt_id',
        'nama_nasabah',
        'npwr',
        'alamat',
        'no_skrd',
        'beban',
        'status',
        'tahun',
        'keterangan',
    ];

    protected $casts = [
        'beban' => 'decimal:2',
    ];

    // ── Boot ────────────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $data) {
            if(empty($data->status)) {

                $data->status = 'Belum';
            }
        });
    }

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
