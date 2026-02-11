<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model {
    use HasFactory;

    protected $table = 'sekolahs';

    protected $fillable = [
        'kelurahan_id',
        'npsn',
        'nama_sekolah',
        'jenjang',
        'status',
        'alamat',
        'latitude',
        'longitude',
        'tahun_ajar',
        'jumlah_siswa',
        'rombel',
        'jumlah_guru',
        'jumlah_pegawai',
        'ruang_kelas',
        'jumlah_r_lab',
        'jumlah_r_perpus'
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }
}

