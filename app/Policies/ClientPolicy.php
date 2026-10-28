<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ClientPolicy extends ModulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['clients.manage', 'company.view']);
    }

    public function create(User $user): bool
    {
        return $user->can('clients.manage');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('clients.manage');
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can('clients.manage');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('clients.manage');
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->can('clients.manage');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('clients.manage');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->can('clients.manage');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('clients.manage');
    }

    public function replicate(User $user, Model $model): bool
    {
        return $user->can('clients.manage');
    }

    public function reorder(User $user): bool
    {
        return $user->can('clients.manage');
    }
}
