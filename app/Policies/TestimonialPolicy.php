<?php

namespace App\Policies;

use App\Models\Testimonial;
use App\Models\User;

class TestimonialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['testimonials.view', 'testimonials.moderate']);
    }

    public function view(User $user, Testimonial $testimonial): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Staff may record a testimonial received through another verified channel.
     */
    public function create(User $user): bool
    {
        return $user->can('testimonials.moderate');
    }

    public function update(User $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.moderate');
    }

    public function moderate(User $user, ?Testimonial $testimonial = null): bool
    {
        return $user->can('testimonials.moderate');
    }

    public function viewPrivatePhoto(User $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.moderate');
    }

    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('testimonials.delete');
    }

    public function restore(User $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.delete');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('testimonials.delete');
    }

    public function forceDelete(User $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.delete');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('testimonials.delete');
    }

    public function reorder(User $user): bool
    {
        return $user->can('testimonials.moderate');
    }
}
