<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use App\Contracts\HasPublicUrl;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasRevisions;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsSlugRedirects;
use App\Models\Concerns\TracksEditors;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'slug', 'template', 'excerpt', 'body', 'hero_media_id', 'status', 'published_at', 'is_system', 'show_in_sitemap'])]
class Page extends Model implements HasPublicUrl
{
    /** @use HasFactory<PageFactory> */
    use Auditable, HasFactory, HasRevisions, HasSeo, HasSlug, Publishable, RecordsSlugRedirects, SoftDeletes, TracksEditors;

    public const TEMPLATES = [
        'default' => 'Standard content page',
        'home' => 'Homepage (sections)',
        'about' => 'About page (sections)',
        'landing' => 'Landing page (sections only)',
        'legal' => 'Legal / policy document',
    ];

    /**
     * @var array<int, string>
     */
    protected array $revisionable = ['title', 'slug', 'excerpt', 'body', 'template', 'status'];

    protected function casts(): array
    {
        return [
            'body' => SanitizedHtml::class,
            'is_system' => 'boolean',
            'show_in_sitemap' => 'boolean',
        ];
    }

    /**
     * @return HasMany<PageSection, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function heroMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_media_id');
    }

    public function isHome(): bool
    {
        return $this->slug === 'home';
    }

    public function publicUrl(): string
    {
        return url($this->publicPathForSlug($this->slug));
    }

    public function publicPathForSlug(string $slug): string
    {
        return match ($slug) {
            'home' => '/',
            default => '/'.$slug,
        };
    }
}
