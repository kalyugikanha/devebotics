<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('roles.manage');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('roles.manage');
    }

    /**
     * The Super Administrator role is implicit (all permissions) and locked.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('roles.manage') && $role->name !== PermissionRegistry::SUPER_ADMIN;
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can('roles.manage')
            && ! in_array($role->name, [PermissionRegistry::SUPER_ADMIN, PermissionRegistry::ADMIN], true)
            && $role->users()->doesntExist();
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
