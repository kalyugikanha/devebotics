<?php

namespace App\Models\Concerns;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Model
 */
trait HasTechnologies
{
    /**
     * @return MorphToMany<Technology, $this>
     */
    public function technologies(): MorphToMany
    {
        return $this->morphToMany(Technology::class, 'technologyable')
            ->orderBy('technologies.sort_order');
    }
}
