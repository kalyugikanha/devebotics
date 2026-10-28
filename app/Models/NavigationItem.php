<?php

namespace App\Models;

use App\Contracts\HasPublicUrl;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Route;

#[Fillable([
    'navigation_menu_id', 'parent_id', 'label', 'link_type', 'url', 'route_name', 'linkable_type', 'linkable_id',
    'description', 'icon', 'open_in_new_tab', 'is_highlighted', 'sort_order', 'is_active',
])]
class NavigationItem extends Model
{
    use Auditable;

    public const LINK_TYPES = [
        'route' => 'Website section',
        'url' => 'Custom URL',
        'linkable' => 'Specific page / service / product / industry',
        'services_mega' => 'Services mega menu (auto-generated)',
    ];

    /**
     * Named public routes administrators may link to.
     */
    public const ROUTES = [
        'home' => 'Home',
        'about' => 'About us',
        'services.index' => 'Services',
        'products.index' => 'Products',
        'industries.index' => 'Industries',
        'portfolio.index' => 'Case studies',
        'blog.index' => 'Blog & insights',
        'testimonials.index' => 'Client reviews',
        'feedback.create' => 'Leave feedback',
        'careers.index' => 'Careers',
        'team.index' => 'Our team',
        'faq.index' => 'FAQ',
        'contact.create' => 'Contact',
        'project.create' => 'Start a project',
        'search' => 'Search',
    ];

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
            'is_highlighted' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<NavigationMenu, $this>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'navigation_menu_id');
    }

    /**
     * @return BelongsTo<NavigationItem, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<NavigationItem, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Resolved href, or null when the target is unavailable (unpublished, deleted, unknown route).
     */
    public function resolveUrl(): ?string
    {
        return match ($this->link_type) {
            'url' => $this->url ?: null,
            'route', 'services_mega' => $this->resolveRoute(),
            'linkable' => $this->linkable instanceof HasPublicUrl && $this->linkable->isPubliclyVisible()
                ? $this->linkable->publicUrl()
                : null,
            default => null,
        };
    }

    public function isMegaMenu(): bool
    {
        return $this->link_type === 'services_mega';
    }

    public function isExternal(): bool
    {
        $url = $this->resolveUrl();

        return $url !== null && str_starts_with($url, 'http') && ! str_starts_with($url, url('/'));
    }

    private function resolveRoute(): ?string
    {
        $name = $this->link_type === 'services_mega' ? 'services.index' : $this->route_name;

        if ($name === null || ! array_key_exists($name, self::ROUTES) || ! Route::has($name)) {
            return null;
        }

        return route($name);
    }
}
