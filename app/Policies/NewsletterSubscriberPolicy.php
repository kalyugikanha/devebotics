<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriberPolicy extends ModulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['newsletter.manage']);
    }

    public function create(User $user): bool
    {
        return $user->can('newsletter.manage');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('newsletter.manage');
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can('newsletter.manage');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('newsletter.manage');
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->can('newsletter.manage');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('newsletter.manage');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->can('newsletter.manage');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('newsletter.manage');
    }

    public function replicate(User $user, Model $model): bool
    {
        return $user->can('newsletter.manage');
    }

    public function reorder(User $user): bool
    {
        return $user->can('newsletter.manage');
    }
}
