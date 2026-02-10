<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PegawaiStaff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai_staff';

    protected $fillable = [
        'nik',
        'nip',
        'nama',
        'jabatan',
        'golongan',
        'pangkat',
        'status_pegawai',
        'alamat',
        'no_telp',
        'email',
        'tgl_lahir',
        'tgl_mulai',
        'no_urut',
        'petugas_input',
        'tgl_input',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'tgl_mulai' => 'date',
        'tgl_input' => 'date',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status_pegawai', 'aktif');
    }
}
