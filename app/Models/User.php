<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'nip',
        'nik',
        'jabatan',
        'wilayah_rt',
        'wilayah_rw',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────

    /**
     * User belongs to a role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // ─── Role Helpers ───────────────────────────────────────────

    /**
     * Get the role name.
     */
    public function getRoleName(): string
    {
        return $this->role?->name ?? 'warga';
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->role?->name === $roles;
        }

        return in_array($this->role?->name, $roles);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    /**
     * Check if user is operator.
     */
    public function isOperator(): bool
    {
        return $this->hasRole(Role::OPERATOR);
    }

    /**
     * Check if user is verifikator.
     */
    public function isVerifikator(): bool
    {
        return $this->hasRole(Role::VERIFIKATOR);
    }

    /**
     * Check if user is penandatangan (lurah).
     */
    public function isPenandatangan(): bool
    {
        return $this->hasRole(Role::PENANDATANGAN);
    }

    /**
     * Check if user is RT/RW.
     */
    public function isRtRw(): bool
    {
        return $this->hasRole(Role::RT_RW);
    }

    /**
     * Check if user is warga.
     */
    public function isWarga(): bool
    {
        return $this->hasRole(Role::WARGA);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        // Admin has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        return $this->role?->hasPermission($permission) ?? false;
    }
}
