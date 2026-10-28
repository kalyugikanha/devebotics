<?php

namespace App\Support\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

/**
 * Records administrative activity and security events.
 *
 * Sensitive attributes are never written to the log, long values are
 * truncated, and a logging failure never breaks the action being audited.
 */
class AuditLogger
{
    /**
     * Attributes that are never written to the audit log for any model.
     *
     * @var array<int, string>
     */
    public const SENSITIVE_ATTRIBUTES = [
        'password',
        'remember_token',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
        'token',
        'secret',
    ];

    private const MAX_VALUE_LENGTH = 300;

    private static bool $enabled = true;

    /**
     * Run a callback (e.g. seeding) without writing audit entries.
     *
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    public static function withoutLogging(Closure $callback): mixed
    {
        $previous = self::$enabled;
        self::$enabled = false;

        try {
            return $callback();
        } finally {
            self::$enabled = $previous;
        }
    }

    public function modelCreated(Model $model): void
    {
        $this->log('created', $model, null, $this->filter($model, $model->getAttributes()));
    }

    public function modelUpdated(Model $model): void
    {
        $changes = $this->filter($model, $model->getChanges());
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        $old = array_intersect_key($this->filter($model, $model->getRawOriginal()), $changes);

        $this->log('updated', $model, $old, $changes);
    }

    public function modelDeleted(Model $model): void
    {
        $event = method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting() ? 'archived' : 'deleted';

        $this->log($event, $model);
    }

    /**
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     */
    public function log(
        string $event,
        ?Model $subject = null,
        ?array $old = null,
        ?array $new = null,
        ?string $description = null,
        bool $isSecurityEvent = false,
        ?User $user = null,
    ): void {
        if (! self::$enabled) {
            return;
        }

        try {
            $request = app()->runningInConsole() ? null : request();

            AuditLog::query()->create([
                'user_id' => $user?->getKey() ?? Auth::id(),
                'event' => Str::limit($event, 64, ''),
                'auditable_type' => $subject?->getMorphClass(),
                'auditable_id' => $subject?->getKey(),
                'description' => Str::limit($description ?? $this->describe($event, $subject), 250),
                'old_values' => $old ?: null,
                'new_values' => $new ?: null,
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? Str::limit((string) $request->userAgent(), 250, '') : null,
                'url' => $request ? Str::limit($request->fullUrl(), 2000, '') : null,
                'is_security_event' => $isSecurityEvent,
                'created_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function security(string $event, string $description, ?User $user = null, array $context = []): void
    {
        $this->log($event, $user, null, $context ?: null, $description, true, $user);
    }

    /**
     * Mask an e-mail address for logs: j***@example.com.
     */
    public static function maskEmail(?string $email): string
    {
        if (blank($email) || ! str_contains($email, '@')) {
            return '(none)';
        }

        [$local, $domain] = explode('@', $email, 2);

        return Str::substr($local, 0, 1).'***@'.$domain;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function filter(Model $model, array $attributes): array
    {
        $excluded = array_merge(
            self::SENSITIVE_ATTRIBUTES,
            $model->getHidden(),
            method_exists($model, 'getAuditExclude') ? $model->getAuditExclude() : [],
        );

        $filtered = [];

        foreach ($attributes as $key => $value) {
            if (in_array($key, $excluded, true)) {
                continue;
            }

            if (is_string($value) && mb_strlen($value) > self::MAX_VALUE_LENGTH) {
                $value = mb_substr(strip_tags($value), 0, self::MAX_VALUE_LENGTH).'…';
            }

            $filtered[$key] = $value;
        }

        return $filtered;
    }

    private function describe(string $event, ?Model $subject): string
    {
        if ($subject === null) {
            return Str::headline($event);
        }

        $label = method_exists($subject, 'getAuditLabel') ? $subject->getAuditLabel() : '#'.$subject->getKey();

        return Str::headline(class_basename($subject)).' "'.$label.'" '.$event;
    }
}
