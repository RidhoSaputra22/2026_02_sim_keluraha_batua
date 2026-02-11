<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelahiran extends Model
{
    use HasFactory;

    protected $table = 'kelahirans';

    protected $fillable = [
        'nama_bayi',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'jam_lahir',
        'ibu_id',
        'ayah_id',
        'rt_id',
        'no_akte',
        'keterangan',
        'petugas_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function ibu()
    {
        return $this->belongsTo(Penduduk::class, 'ibu_id');
    }

    public function ayah()
    {
        return $this->belongsTo(Penduduk::class, 'ayah_id');
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
