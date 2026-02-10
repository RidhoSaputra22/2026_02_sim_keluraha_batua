<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'permissions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_active'   => 'boolean',
        ];
    }

    // Role name constants
    const ADMIN          = 'admin';
    const OPERATOR       = 'operator';
    const VERIFIKATOR    = 'verifikator';
    const PENANDATANGAN  = 'penandatangan';
    const RT_RW          = 'rt_rw';
    const WARGA          = 'warga';

    /**
     * Get all available role names.
     */
    public static function availableRoles(): array
    {
        return [
            self::ADMIN,
            self::OPERATOR,
            self::VERIFIKATOR,
            self::PENANDATANGAN,
            self::RT_RW,
            self::WARGA,
        ];
    }

    /**
     * Role labels for display.
     */
    public static function roleLabels(): array
    {
        return [
            self::ADMIN         => 'Admin Sistem',
            self::OPERATOR      => 'Operator Kelurahan',
            self::VERIFIKATOR   => 'Verifikator (Kasi/Seklur)',
            self::PENANDATANGAN => 'Penandatangan (Lurah)',
            self::RT_RW         => 'RT/RW',
            self::WARGA         => 'Warga',
        ];
    }

    /**
     * Relationship: role has many users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }
}
