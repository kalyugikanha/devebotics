<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Seeded demonstration records are flagged so they can be labelled publicly
 * and removed in one step (`php artisan softec:purge-demo`).
 *
 * @mixin Model
 */
trait FlagsDemoContent
{
    public function initializeFlagsDemoContent(): void
    {
        $this->mergeCasts(['is_demo' => 'boolean']);
    }

    #[Scope]
    protected function demo(Builder $query): void
    {
        $query->where($this->qualifyColumn('is_demo'), true);
    }
}
