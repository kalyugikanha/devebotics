<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Permissions\PermissionRegistry;

/**
 * Guard rails that apply to everyone, including Super Administrators:
 * nobody can delete or deactivate themselves, only Super Administrators
 * can modify other Super Administrators, and the last active Super
 * Administrator can never be removed.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view') || $user->is($model);
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        if (! $user->can('users.update')) {
            return false;
        }

        return ! $model->isSuperAdmin() || $user->isSuperAdmin();
    }

    /**
     * Activate / deactivate another account.
     */
    public function toggleActive(User $user, User $model): bool
    {
        if ($user->is($model) || ! $this->update($user, $model)) {
            return false;
        }

        return ! ($model->is_active && self::isLastActiveSuperAdmin($model));
    }

    /**
     * Changing roles of an account (never your own — prevents self-escalation).
     */
    public function assignRoles(User $user, User $model): bool
    {
        return $user->can('roles.manage') && ! $user->is($model) && $this->update($user, $model)
            && ! self::isLastActiveSuperAdmin($model);
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->is($model) || ! $user->can('users.delete')) {
            return false;
        }

        if ($model->isSuperAdmin() && (! $user->isSuperAdmin() || self::isLastActiveSuperAdmin($model))) {
            return false;
        }

        return true;
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('users.delete');
    }

    public static function isLastActiveSuperAdmin(User $model): bool
    {
        if (! $model->isSuperAdmin()) {
            return false;
        }

        return User::role(PermissionRegistry::SUPER_ADMIN)
            ->where('is_active', true)
            ->whereKeyNot($model->getKey())
            ->doesntExist();
    }
}
