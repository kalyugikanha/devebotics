<?php

namespace App\Policies;

use App\Models\EmailLog;
use App\Models\User;

/**
 * Outgoing email history is read-only.
 */
class EmailLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['email_templates.manage', 'audit_logs.view']);
    }

    public function view(User $user, EmailLog $log): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, EmailLog $log): bool
    {
        return false;
    }

    public function delete(User $user, EmailLog $log): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
