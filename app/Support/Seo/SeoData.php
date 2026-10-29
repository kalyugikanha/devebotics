<?php

namespace App\Support\Seo;

use App\Models\Media;
use App\Models\SeoMeta;
use App\Support\Branding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Page-level SEO metadata rendered by <x-seo.head>.
 */
final class SeoData
{
    public ?string $canonical = null;

    public ?string $image = null;

    public ?string $ogTitle = null;

    public ?string $ogDescription = null;

    public string $type = 'website';

    public bool $index = true;

    public bool $follow = true;

    public ?string $publishedTime = null;

    public ?string $modifiedTime = null;

    /**
     * @var array<int, array{label: string, url: ?string}>
     */
    public array $breadcrumbs = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $schema = [];

    private bool $appendSuffix = true;

    public function __construct(public string $title, public ?string $description = null) {}

    public static function make(string $title, ?string $description = null): self
    {
        return new self($title, $description ? Str::limit(strip_tags($description), 300) : null);
    }

    /**
     * Build metadata for a content record, applying any administrator overrides from seo_meta.
     */
    public static function forModel(Model $model, string $title, ?string $description = null, ?Media $image = null): self
    {
        $seo = self::make($title, $description);

        if ($image !== null) {
            $seo->image = $image->variantUrl(1600) ?? $image->url();
        }

        /** @var SeoMeta|null $meta */
        $meta = $model->relationLoaded('seo') ? $model->getRelation('seo') : (method_exists($model, 'seo') ? $model->seo()->with('ogImage')->first() : null);

        if ($meta !== null) {
            $seo->title = $meta->meta_title ?: $seo->title;
            $seo->description = $meta->meta_description ?: $seo->description;
            $seo->canonical = $meta->canonical_url ?: null;
            $seo->index = $meta->robots_index;
            $seo->follow = $meta->robots_follow;
            $seo->ogTitle = $meta->og_title;
            $seo->ogDescription = $meta->og_description;

            if ($meta->ogImage !== null) {
                $seo->image = $meta->ogImage->url();
            }
        }

        if ($model->getAttribute('updated_at') !== null) {
            $seo->modifiedTime = $model->getAttribute('updated_at')->toIso8601String();
        }

        return $seo;
    }

    /**
     * @param  array<int, array{0: string, 1?: ?string}>  $crumbs  [label, url] pairs, Home is prepended automatically.
     */
    public function breadcrumbs(array $crumbs): self
    {
        $this->breadcrumbs = [['label' => 'Home', 'url' => route('home')]];

        foreach ($crumbs as $crumb) {
            $this->breadcrumbs[] = ['label' => $crumb[0], 'url' => $crumb[1] ?? null];
        }

        $this->schema[] = SchemaFactory::breadcrumbList($this->breadcrumbs);

        return $this;
    }

    /**
     * @param  array<string, mixed>|null  $schema
     */
    public function withSchema(?array $schema): self
    {
        if ($schema !== null) {
            $this->schema[] = $schema;
        }

        return $this;
    }

    public function noindex(): self
    {
        $this->index = false;

        return $this;
    }

    public function canonical(string $url): self
    {
        $this->canonical ??= $url;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function withoutSuffix(): self
    {
        $this->appendSuffix = false;

        return $this;
    }

    public function fullTitle(): string
    {
        $suffix = (string) settings('seo.title_suffix', '');
        $siteName = (string) settings('company.name');

        if (! $this->appendSuffix || $suffix === '' || Str::contains($this->title, $siteName, ignoreCase: true) || Str::contains($this->title, 'Softec AI', ignoreCase: true)) {
            return $this->title;
        }

        return $this->title.$suffix;
    }

    public function metaDescription(): string
    {
        return Str::limit((string) ($this->description ?: settings('seo.default_description')), 160);
    }

    public function imageUrl(): string
    {
        return $this->image ?? Branding::defaultOgImageUrl();
    }

    public function canonicalUrl(): string
    {
        return $this->canonical ?? url()->current();
    }

    public function robots(): string
    {
        $index = $this->index && self::siteAllowsIndexing();

        return ($index ? 'index' : 'noindex').','.($this->follow ? 'follow' : 'nofollow').($index ? ',max-image-preview:large' : '');
    }

    /**
     * Indexing requires production, the APP_ALLOW_INDEXING flag and the admin toggle.
     */
    public static function siteAllowsIndexing(): bool
    {
        return app()->isProduction()
            && (bool) config('softec.allow_indexing')
            && (bool) settings('seo.allow_indexing', true);
    }
}
