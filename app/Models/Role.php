<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // ─── Role Constants ───────────────────────────────────────
    public const ADMIN         = 'admin';
    public const OPERATOR      = 'operator';
    public const VERIFIKATOR   = 'verifikator';
    public const PENANDATANGAN = 'penandatangan';
    public const RT_RW         = 'rt_rw';
    public const WARGA         = 'warga';

    protected $fillable = [
        'name',
        'label',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
