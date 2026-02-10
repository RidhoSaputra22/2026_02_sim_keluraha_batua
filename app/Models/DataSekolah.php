<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataSekolah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_sekolah';

    protected $fillable = [
        'kelurahan',
        'kecamatan',
        'nama_sekolah',
        'npsn',
        'jenjang',
        'status',
        'alamat',
        'rt',
        'rw',
        'kepala_sekolah',
        'no_telp',
        'email',
        'jumlah_siswa',
        'jumlah_guru',
        'jumlah_rombel',
        'latitude',
        'longitude',
        'keterangan',
        'petugas_input',
        'tgl_input',
        'arsip',
    ];

    protected $casts = [
        'latitude'      => 'decimal:8',
        'longitude'     => 'decimal:8',
        'jumlah_siswa'  => 'integer',
        'jumlah_guru'   => 'integer',
        'jumlah_rombel' => 'integer',
        'tgl_input'     => 'date',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }

    public function scopeWilayah($query, $rt = null, $rw = null)
    {
        return $query->when($rt, fn($q) => $q->where('rt', $rt))
                     ->when($rw, fn($q) => $q->where('rw', $rw));
    }

    public function scopeJenjang($query, $jenjang)
    {
        return $query->where('jenjang', $jenjang);
    }
}
