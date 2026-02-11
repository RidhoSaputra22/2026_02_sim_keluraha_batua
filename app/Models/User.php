<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // ─── Helpers ──────────────────────────────────────────────

    public function getRoleName(): ?string
    {
        return $this->role?->name;
    }

    public function isAdmin(): bool
    {
        return $this->getRoleName() === Role::ADMIN;
    }

    public function hasRole(array $roles): bool
    {
        return in_array($this->getRoleName(), $roles);
    }
}
