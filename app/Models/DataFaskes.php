<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataFaskes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_faskes';

    protected $fillable = [
        'nama_faskes',
        'nama_rs',
        'jenis',
        'jenis_pelayanan',
        'kelas',
        'akreditasi',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'telp',
        'email',
        'kepala_faskes',
        'jumlah_tenaga_medis',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_tenaga_medis' => 'integer',
    ];

    public function scopeWilayah($query, $rt = null, $rw = null)
    {
        return $query->when($rt, fn($q) => $q->where('rt', $rt))
                     ->when($rw, fn($q) => $q->where('rw', $rw));
    }

    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
