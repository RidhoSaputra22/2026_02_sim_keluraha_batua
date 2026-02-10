<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempatIbadah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tempat_ibadah';

    protected $fillable = [
        'jenis_tempat_ibadah',
        'nama',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'pengurus',
        'no_telp',
        'tahun_berdiri',
        'luas_tanah',
        'luas_bangunan',
        'status_tanah',
        'keterangan',
        'petugas_input',
        'tgl_input',
        'arsip',
    ];

    protected $casts = [
        'tahun_berdiri'  => 'integer',
        'luas_tanah'     => 'decimal:2',
        'luas_bangunan'  => 'decimal:2',
        'tgl_input'      => 'date',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }
}
