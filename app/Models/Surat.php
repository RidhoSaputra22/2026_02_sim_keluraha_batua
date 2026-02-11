<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model {
    use HasFactory;

    protected $table = 'surats';

    protected $fillable = [
        'kelurahan_id',
        'arah',
        'nomor_surat',
        'tanggal_surat',
        'tanggal_diterima',
        'jenis_id',
        'sifat_id',
        'instansi_id',
        'layanan_publik_id',
        'tujuan_surat',
        'perihal',
        'uraian',
        'nama_dalam_surat',
        'pemohon_id',
        'status_esign',
        'tgl_input',
        'petugas_input_id',
        'arsip_path',
        'verifikator_id',
        'penandatangan_pejabat_id',
        'tgl_verifikasi',
        'tgl_ttd',
        'catatan_verifikasi',
        'catatan_penandatangan',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_diterima' => 'date',
        'tgl_input' => 'datetime',
        'tgl_verifikasi' => 'datetime',
        'tgl_ttd' => 'datetime',
    ];


    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function jenis()
    {
        return $this->belongsTo(SuratJenis::class, 'jenis_id');
    }

    public function sifat()
    {
        return $this->belongsTo(SuratSifat::class, 'sifat_id');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }

    public function layananPublik()
    {
        return $this->belongsTo(LayananPublik::class);
    }

    public function pemohon()
    {
        return $this->belongsTo(Pemohon::class);
    }

    public function petugasInput()
    {
        return $this->belongsTo(User::class, 'petugas_input_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    public function penandatanganPejabat()
    {
        return $this->belongsTo(Penandatanganan::class, 'penandatangan_pejabat_id');
    }
}

