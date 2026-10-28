<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use App\Contracts\HasPublicUrl;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasGallery;
use App\Models\Concerns\HasRevisions;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTechnologies;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsSlugRedirects;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\TracksEditors;
use Database\Factories\PortfolioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'slug', 'illustration', 'client_id', 'client_label', 'is_client_anonymized', 'industry_id', 'portfolio_category_id',
    'summary', 'challenge', 'solution', 'outcome', 'metrics', 'duration', 'started_on', 'completed_on',
    'show_timeline', 'project_url', 'hero_media_id', 'status', 'published_at', 'is_featured', 'sort_order', 'is_demo',
])]
class Portfolio extends Model implements HasPublicUrl
{
    /** @use HasFactory<PortfolioFactory> */
    use Auditable, FlagsDemoContent, HasFactory, HasGallery, HasRevisions, HasSeo, HasSlug, HasTechnologies;

    use Publishable, RecordsSlugRedirects, SoftDeletes, Sortable, TracksEditors;

    /**
     * @var array<int, string>
     */
    protected array $revisionable = ['title', 'slug', 'summary', 'challenge', 'solution', 'outcome', 'status'];

    protected function casts(): array
    {
        return [
            'challenge' => SanitizedHtml::class,
            'solution' => SanitizedHtml::class,
            'outcome' => SanitizedHtml::class,
            'metrics' => 'array',
            'is_client_anonymized' => 'boolean',
            'show_timeline' => 'boolean',
            'is_featured' => 'boolean',
            'started_on' => 'date',
            'completed_on' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<Industry, $this>
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * @return BelongsTo<PortfolioCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id');
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
     * Public client attribution — the real client name is used only when not
     * anonymised and the client has approved public listing.
     */
    public function clientDisplayName(): ?string
    {
        if (! $this->is_client_anonymized && $this->client?->logo_usage_approved) {
            return $this->client->name;
        }

        return $this->client_label;
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function verifiedMetrics(): array
    {
        return collect($this->metrics ?? [])
            ->filter(fn (array $metric): bool => (bool) ($metric['is_verified'] ?? false) && filled($metric['label'] ?? null))
            ->map(fn (array $metric): array => ['label' => (string) $metric['label'], 'value' => (string) ($metric['value'] ?? '')])
            ->values()
            ->all();
    }

    public function publicUrl(): string
    {
        return route('portfolio.show', $this->slug);
    }

    public function publicPathForSlug(string $slug): string
    {
        return '/case-studies/'.$slug;
    }
}
