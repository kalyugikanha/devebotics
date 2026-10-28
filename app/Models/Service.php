<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use App\Contracts\HasPublicUrl;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasFaqs;
use App\Models\Concerns\HasRevisions;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTechnologies;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsSlugRedirects;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\TracksEditors;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'service_category_id', 'title', 'slug', 'tagline', 'excerpt', 'overview', 'description', 'icon', 'illustration',
    'hero_media_id', 'video_url', 'challenges', 'benefits', 'features', 'process_steps', 'cta_title',
    'cta_text', 'status', 'published_at', 'is_featured', 'sort_order', 'is_demo',
])]
class Service extends Model implements HasPublicUrl
{
    /** @use HasFactory<ServiceFactory> */
    use Auditable, FlagsDemoContent, HasFactory, HasFaqs, HasRevisions, HasSeo, HasSlug, HasTechnologies;

    use Publishable, RecordsSlugRedirects, SoftDeletes, Sortable, TracksEditors;

    /**
     * @var array<int, string>
     */
    protected array $revisionable = ['title', 'slug', 'tagline', 'excerpt', 'overview', 'description', 'status'];

    protected function casts(): array
    {
        return [
            'description' => SanitizedHtml::class,
            'challenges' => 'array',
            'benefits' => 'array',
            'features' => 'array',
            'process_steps' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<ServiceCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
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
    public function relatedServices(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'service_related', 'service_id', 'related_service_id');
    }

    /**
     * @return BelongsToMany<Industry, $this>
     */
    public function industries(): BelongsToMany
    {
        return $this->belongsToMany(Industry::class);
    }

    /**
     * @return BelongsToMany<Portfolio, $this>
     */
    public function portfolios(): BelongsToMany
    {
        return $this->belongsToMany(Portfolio::class);
    }

    /**
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function publicUrl(): string
    {
        return route('services.show', $this->slug);
    }

    public function publicPathForSlug(string $slug): string
    {
        return '/services/'.$slug;
    }
}
