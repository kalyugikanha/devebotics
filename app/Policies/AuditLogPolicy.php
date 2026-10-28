<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

/**
 * The audit log is append-only: nobody can create, edit or delete entries
 * through the application (retention pruning runs as a scheduled command).
 */
class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['audit_logs.view', 'security_logs.view']);
    }

    public function view(User $user, AuditLog $log): bool
    {
        return $log->is_security_event ? $user->can('security_logs.view') : $user->can('audit_logs.view');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AuditLog $log): bool
    {
        return false;
    }

    public function delete(User $user, AuditLog $log): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
