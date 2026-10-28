<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use App\Contracts\HasPublicUrl;
use App\Enums\PricingModel;
use App\Enums\ProductAvailability;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasFaqs;
use App\Models\Concerns\HasGallery;
use App\Models\Concerns\HasRevisions;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTechnologies;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsSlugRedirects;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\TracksEditors;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'product_category_id', 'name', 'slug', 'tagline', 'illustration', 'summary', 'description', 'logo_media_id', 'hero_media_id',
    'demo_video_url', 'features', 'benefits', 'target_audience', 'pricing_model', 'pricing_note', 'has_free_trial',
    'trial_days', 'has_demo', 'availability', 'website_url', 'documentation_url', 'support_url', 'status',
    'published_at', 'is_featured', 'sort_order', 'is_demo',
])]
class Product extends Model implements HasPublicUrl
{
    /** @use HasFactory<ProductFactory> */
    use Auditable, FlagsDemoContent, HasFactory, HasFaqs, HasGallery, HasRevisions, HasSeo, HasSlug, HasTechnologies;

    use Publishable, RecordsSlugRedirects, SoftDeletes, Sortable, TracksEditors;

    protected string $slugSource = 'name';

    /**
     * @var array<int, string>
     */
    protected array $revisionable = ['name', 'slug', 'tagline', 'summary', 'description', 'status', 'availability', 'pricing_model'];

    protected function casts(): array
    {
        return [
            'description' => SanitizedHtml::class,
            'features' => 'array',
            'benefits' => 'array',
            'pricing_model' => PricingModel::class,
            'availability' => ProductAvailability::class,
            'has_free_trial' => 'boolean',
            'has_demo' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<ProductCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function heroMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_media_id');
    }

    /**
     * @return HasMany<ProductPlan, $this>
     */
    public function plans(): HasMany
    {
        return $this->hasMany(ProductPlan::class)->orderBy('sort_order');
    }

    /**
     * @return BelongsToMany<Product, $this>
     */
    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_related', 'product_id', 'related_product_id');
    }

    /**
     * @return BelongsToMany<Service, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
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

    public function showsPlans(): bool
    {
        return $this->pricing_model === PricingModel::Plans;
    }

    public function publicUrl(): string
    {
        return route('products.show', $this->slug);
    }

    public function publicPathForSlug(string $slug): string
    {
        return '/products/'.$slug;
    }
}
