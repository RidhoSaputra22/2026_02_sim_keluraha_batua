<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtRwPengurus extends Model
{
    use HasFactory, Auditable;

    protected $table = 'rt_rw_pengurus';

    protected $fillable = [
        'kelurahan_id',
        'penduduk_id',
        'jabatan_id',
        'rw_id',
        'rt_id',
        'tgl_mulai',
        'status',
        'alamat',
        'no_telp',
        'no_rekening',
        'no_npwp'
    ];

    protected $casts = [
        'tgl_mulai' => 'date'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(JabatanRtRw::class, 'jabatan_id');
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }
}
