<?php

namespace App\Listeners;

use App\Models\User;
use App\Support\Audit\AuditLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;

/**
 * Writes authentication events to the security log. Attempted e-mail
 * addresses are masked and passwords are never recorded.
 */
class RecordAuthenticationActivity
{
    public function __construct(private AuditLogger $audit) {}

    public function handle(Login|Logout|Failed|Lockout|PasswordReset $event): void
    {
        match (true) {
            $event instanceof Login => $this->login($event),
            $event instanceof Logout => $event->user instanceof User
                ? $this->audit->security('auth.logout', 'Signed out', $event->user)
                : null,
            $event instanceof Failed => $this->audit->security(
                'auth.failed',
                'Failed sign-in attempt for '.AuditLogger::maskEmail($event->credentials['email'] ?? null),
                $event->user instanceof User ? $event->user : null,
            ),
            $event instanceof Lockout => $this->audit->security(
                'auth.lockout',
                'Too many sign-in attempts for '.AuditLogger::maskEmail($event->request->input('email', $event->request->input('data.email'))),
            ),
            $event instanceof PasswordReset => $event->user instanceof User
                ? $this->audit->security('auth.password_reset', 'Password reset completed', $event->user)
                : null,
        };
    }

    private function login(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $event->user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ])->saveQuietly();

        $this->audit->security('auth.login', 'Signed in', $event->user);
    }
}
