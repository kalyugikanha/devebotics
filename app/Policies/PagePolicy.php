<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PagePolicy extends ModulePolicy
{
    protected string $module = 'pages';

    protected bool $seoEditable = true;

    /**
     * System pages (home, about, legal, ...) can be edited but never deleted.
     */
    public function delete(User $user, Model $model): bool
    {
        /** @var Page $model */
        return ! $model->is_system && parent::delete($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        /** @var Page $model */
        return ! $model->is_system && parent::forceDelete($user, $model);
    }
}
