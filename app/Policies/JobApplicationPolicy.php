<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('applications.view');
    }

    public function view(User $user, JobApplication $application): bool
    {
        return $user->can('applications.view');
    }

    /**
     * Applications are submitted only through the public careers form.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, JobApplication $application): bool
    {
        return $user->can('applications.update');
    }

    public function downloadResume(User $user, JobApplication $application): bool
    {
        return $user->can('applications.view');
    }

    public function delete(User $user, JobApplication $application): bool
    {
        return $user->can('applications.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('applications.delete');
    }

    public function restore(User $user, JobApplication $application): bool
    {
        return $user->can('applications.delete');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('applications.delete');
    }

    public function forceDelete(User $user, JobApplication $application): bool
    {
        return $user->can('applications.delete');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('applications.delete');
    }
}
