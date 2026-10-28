<?php

namespace App\Policies;

use App\Models\User;

/**
 * Query desk teams, members and routing rules (`teams.manage`).
 */
class TeamPolicy extends ModulePolicy
{
    protected string $module = 'teams';

    public function viewAny(User $user): bool
    {
        return $user->can('teams.manage');
    }
}
