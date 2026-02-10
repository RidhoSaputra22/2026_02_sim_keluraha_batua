<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penandatanganan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penandatanganan';

    protected $fillable = [
        'nik',
        'nip',
        'nama',
        'jabatan',
        'pangkat',
        'golongan',
        'status',
        'alamat',
        'no_telp',
        'email',
        'tgl_mulai',
        'tgl_selesai',
        'ttd_digital',
        'petugas_input',
        'tgl_input',
    ];

    protected $casts = [
        'tgl_mulai'   => 'date',
        'tgl_selesai' => 'date',
        'tgl_input'   => 'date',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_input', 'id');
    }

    public function suratKeluar(): HasMany
    {
        return $this->hasMany(DaftarSuratKeluar::class, 'penandatangan_id', 'id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
