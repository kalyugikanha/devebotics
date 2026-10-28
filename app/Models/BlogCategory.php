<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasActiveFlag;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Database\Factories\BlogCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'sort_order', 'is_active'])]
class BlogCategory extends Model
{
    /** @use HasFactory<BlogCategoryFactory> */
    use Auditable, HasActiveFlag, HasFactory, HasSlug, Sortable;

    protected string $slugSource = 'name';

    /**
     * @return HasMany<BlogPost, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function publicUrl(): string
    {
        return route('blog.category', $this->slug);
    }
}
