<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pk5 extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pk5';

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
}
