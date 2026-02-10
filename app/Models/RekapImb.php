<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapImb extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rekap_imb';

    protected $fillable = ["nama_pemohon", "alamat_pemohon", "alamat_bangunan", "status_luas_tanah", "nama_pada_surat", "penggunaan_fungsi_gedung"];

}
