<?php

namespace App\Models\Concerns;

use App\Support\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * Writes create / update / delete events to the audit log.
 *
 * Models may define `protected array $auditExclude` to keep additional
 * attributes (beyond the global sensitive list) out of the log.
 *
 * @mixin Model
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn (Model $model) => app(AuditLogger::class)->modelCreated($model));
        static::updated(fn (Model $model) => app(AuditLogger::class)->modelUpdated($model));
        static::deleted(fn (Model $model) => app(AuditLogger::class)->modelDeleted($model));

        if (method_exists(static::class, 'restored')) {
            static::restored(fn (Model $model) => app(AuditLogger::class)->log('restored', $model));
        }
    }

    /**
     * @return array<int, string>
     */
    public function getAuditExclude(): array
    {
        return property_exists($this, 'auditExclude') ? $this->auditExclude : [];
    }

    public function getAuditLabel(): string
    {
        foreach (['title', 'name', 'reference', 'label', 'key', 'question', 'client_name', 'from_path'] as $attribute) {
            if (filled($this->getAttribute($attribute))) {
                return (string) $this->getAttribute($attribute);
            }
        }

        return class_basename($this).' #'.$this->getKey();
    }
}
