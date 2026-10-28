<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['media.view', 'media.manage']);
    }

    public function view(User $user, Media $media): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('media.manage');
    }

    public function update(User $user, Media $media): bool
    {
        return $user->can('media.manage');
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->can('media.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('media.delete');
    }
}
