<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BlogCommentPolicy extends ModulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['comments.moderate']);
    }

    public function create(User $user): bool
    {
        return $user->can('comments.moderate');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('comments.moderate');
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can('comments.moderate');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('comments.moderate');
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->can('comments.moderate');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('comments.moderate');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->can('comments.moderate');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('comments.moderate');
    }

    public function replicate(User $user, Model $model): bool
    {
        return $user->can('comments.moderate');
    }

    public function reorder(User $user): bool
    {
        return $user->can('comments.moderate');
    }
}
