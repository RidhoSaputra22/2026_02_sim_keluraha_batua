<?php

namespace App\Traits;

use App\Models\AuditLog;

/**
 * Trait Auditable
 *
 * Automatically logs created, updated, and deleted events on Eloquent models.
 * Add `use Auditable;` to any model that needs audit tracking.
 *
 * Optionally override $auditExclude to hide sensitive fields from logs.
 */
trait Auditable
{
    /**
     * Boot the trait — register model event listeners.
     */
    public static function bootAuditable(): void
    {
        // Log CREATED
        static::created(function ($model) {
            $newValues = $model->getAuditableAttributes($model->getAttributes());

            AuditLog::record('created', $model, null, $newValues);
        });

        // Log UPDATED
        static::updated(function ($model) {
            $dirty = $model->getDirty();

            if (empty($dirty)) {
                return;
            }

            $oldValues = $model->getAuditableAttributes(
                collect($dirty)->mapWithKeys(fn ($v, $k) => [$k => $model->getOriginal($k)])->toArray()
            );
            $newValues = $model->getAuditableAttributes($dirty);

            AuditLog::record('updated', $model, $oldValues, $newValues);
        });

        // Log DELETED
        static::deleted(function ($model) {
            $oldValues = $model->getAuditableAttributes($model->getAttributes());

            AuditLog::record('deleted', $model, $oldValues, null);
        });
    }

    /**
     * Filter out excluded fields (passwords, tokens, etc).
     */
    public function getAuditableAttributes(array $attributes): array
    {
        $exclude = property_exists($this, 'auditExclude')
            ? $this->auditExclude
            : ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

        return collect($attributes)
            ->except($exclude)
            ->toArray();
    }
}
