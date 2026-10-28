<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use App\Contracts\HasPublicUrl;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasFaqs;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsSlugRedirects;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\TracksEditors;
use Database\Factories\IndustryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'slug', 'icon', 'illustration', 'excerpt', 'description', 'challenges', 'solutions', 'hero_media_id',
    'status', 'published_at', 'is_featured', 'sort_order', 'is_demo',
])]
class Industry extends Model implements HasPublicUrl
{
    /** @use HasFactory<IndustryFactory> */
    use Auditable, FlagsDemoContent, HasFactory, HasFaqs, HasSeo, HasSlug, Publishable;

    use RecordsSlugRedirects, SoftDeletes, Sortable, TracksEditors;

    protected string $slugSource = 'name';

    protected function casts(): array
    {
        return [
            'description' => SanitizedHtml::class,
            'challenges' => 'array',
            'solutions' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function heroMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_media_id');
    }

    /**
     * @return BelongsToMany<Service, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * @return HasMany<Portfolio, $this>
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function publicUrl(): string
    {
        return route('industries.show', $this->slug);
    }

    public function publicPathForSlug(string $slug): string
    {
        return '/industries/'.$slug;
    }
}
