<?php

namespace App\Models;

use App\Enums\TechnologyCategory;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasActiveFlag;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Database\Factories\TechnologyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['name', 'slug', 'category', 'logo_media_id', 'website_url', 'show_on_home', 'sort_order', 'is_active'])]
class Technology extends Model
{
    /** @use HasFactory<TechnologyFactory> */
    use Auditable, HasActiveFlag, HasFactory, HasSlug, Sortable;

    protected string $slugSource = 'name';

    protected function casts(): array
    {
        return [
            'category' => TechnologyCategory::class,
            'show_on_home' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    /**
     * @return MorphToMany<Service, $this>
     */
    public function services(): MorphToMany
    {
        return $this->morphedByMany(Service::class, 'technologyable');
    }

    /**
     * @return MorphToMany<Product, $this>
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'technologyable');
    }

    /**
     * @return MorphToMany<Portfolio, $this>
     */
    public function portfolios(): MorphToMany
    {
        return $this->morphedByMany(Portfolio::class, 'technologyable');
    }
}
