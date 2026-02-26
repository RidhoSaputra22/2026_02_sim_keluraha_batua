<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // ─── Role Constants ───────────────────────────────────────
    public const ADMIN = 'admin';
    public const RT_RW = 'rt_rw';

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

    // ─── Helpers ──────────────────────────────────────────────

    /**
     * Get human-readable labels for each role name.
     */
    public static function roleLabels(): array
    {
        return [
            self::ADMIN => 'Administrator',
            self::RT_RW => 'RT/RW',
        ];
    }
}
