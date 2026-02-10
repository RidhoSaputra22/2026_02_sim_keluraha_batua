<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailSekolah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detail_sekolah';

    protected $fillable = ["npsn", "nama_sekolah", "tahun_ajar", "jumlah_siswa", "rombel", "jumlah_guru", "jumlah_pegawai", "ruang_kelas", "jumlah_r_lab", "jumlah_r_perpus"];

}
