<?php

namespace App\Models\Concerns;

use App\Models\MediaAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Model
 */
trait HasGallery
{
    /**
     * @return MorphMany<MediaAttachment, $this>
     */
    public function galleryItems(): MorphMany
    {
        return $this->morphMany(MediaAttachment::class, 'attachable')
            ->where('collection', 'gallery')
            ->orderBy('sort_order');
    }
}
