<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Standard "{module}.view" / "{module}.manage" authorization used by most
 * content resources. Every check runs server-side on each request.
 */
abstract class ModulePolicy
{
    /**
     * Permission prefix, e.g. "services" → services.view / services.manage.
     */
    protected string $module;

    /**
     * When true, users holding `seo.manage` may open the edit form to change
     * SEO metadata (all other fields are locked for them in the form).
     */
    protected bool $seoEditable = false;

    public function viewAny(User $user): bool
    {
        return $user->can("{$this->module}.view") || $user->can("{$this->module}.manage");
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can("{$this->module}.manage");
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can("{$this->module}.manage") || ($this->seoEditable && $user->can('seo.manage'));
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can("{$this->module}.manage");
    }

    public function deleteAny(User $user): bool
    {
        return $user->can("{$this->module}.manage");
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->can("{$this->module}.manage");
    }

    public function restoreAny(User $user): bool
    {
        return $user->can("{$this->module}.manage");
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->can("{$this->module}.manage");
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can("{$this->module}.manage");
    }

    public function replicate(User $user, Model $model): bool
    {
        return $user->can("{$this->module}.manage");
    }

    public function reorder(User $user): bool
    {
        return $user->can("{$this->module}.manage");
    }
}
