<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmailTemplatePolicy extends ModulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['email_templates.manage']);
    }

    public function create(User $user): bool
    {
        return $user->can('email_templates.manage');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('email_templates.manage');
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can('email_templates.manage');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('email_templates.manage');
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->can('email_templates.manage');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('email_templates.manage');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->can('email_templates.manage');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('email_templates.manage');
    }

    public function replicate(User $user, Model $model): bool
    {
        return $user->can('email_templates.manage');
    }

    public function reorder(User $user): bool
    {
        return $user->can('email_templates.manage');
    }
}
