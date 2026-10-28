<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use App\Support\Mail\Outbox;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Password;

/**
 * Emails a "choose your password" link to a newly created administrator.
 * The link is a standard password-reset token (expires per auth config).
 */
class UserInvitationService
{
    public function __construct(private Outbox $outbox, private AuditLogger $audit) {}

    public function send(User $user): bool
    {
        $token = Password::broker(Filament::getPanel('admin')->getAuthPasswordBroker())->createToken($user);
        $url = Filament::getPanel('admin')->getResetPasswordUrl($token, $user);

        $sent = $this->outbox->send($user->email, new TemplatedMail('user_invitation', [
            'name' => $user->name,
            'roles' => $user->roles()->pluck('name')->implode(', ') ?: 'no role yet',
        ], $url), $user);

        $user->forceFill(['invited_at' => now()])->saveQuietly();

        $this->audit->log('user.invited', $user, description: 'Invitation sent to '.AuditLogger::maskEmail($user->email), isSecurityEvent: true);

        return $sent;
    }
}
