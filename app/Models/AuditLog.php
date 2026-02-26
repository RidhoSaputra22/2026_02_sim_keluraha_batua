<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'event',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ────────────────────────────────────────────
    // Relationships
    // ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    // ────────────────────────────────────────────
    // Helper: record an audit entry
    // ────────────────────────────────────────────

    public static function record(
        string $event,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
    ): self {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $request = request();

        // Build friendly description if not provided
        if (!$description) {
            $modelLabel = static::getModelLabel($model);
            $description = match ($event) {
                'created' => "Menambah data {$modelLabel}",
                'updated' => "Memperbarui data {$modelLabel}",
                'deleted' => "Menghapus data {$modelLabel}",
                default   => ucfirst($event) . " {$modelLabel}",
            };
        }

        return static::create([
            'user_id'         => $user?->id,
            'user_name'       => $user?->name ?? 'Sistem',
            'event'           => $event,
            'auditable_type'  => $model->getMorphClass(),
            'auditable_id'    => $model->getKey(),
            'description'     => $description,
            'old_values'      => $oldValues,
            'new_values'      => $newValues,
            'ip_address'      => $request?->ip(),
            'user_agent'      => $request?->userAgent(),
        ]);
    }

    // ────────────────────────────────────────────
    // Helper: get human-readable model name
    // ────────────────────────────────────────────

    public static function getModelLabel(Model $model): string
    {
        $map = [
            User::class              => 'Pengguna',
            Penduduk::class          => 'Penduduk',
            Keluarga::class          => 'Keluarga',
            PegawaiStaff::class      => 'Pegawai',
            Penandatanganan::class   => 'Penandatangan',
            RtRwPengurus::class      => 'Wilayah RT/RW',
            Faskes::class            => 'Fasilitas Kesehatan',
            Sekolah::class           => 'Sekolah',
            TempatIbadah::class      => 'Tempat Ibadah',
            AgendaKegiatan::class    => 'Agenda Kegiatan',
            HasilKegiatan::class     => 'Hasil Kegiatan',
        ];

        return $map[get_class($model)] ?? class_basename($model);
    }

    // ────────────────────────────────────────────
    // Scopes
    // ────────────────────────────────────────────

    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('auditable_type', $modelClass);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByEvent($query, string $event)
    {
        return $query->where('event', $event);
    }
}
