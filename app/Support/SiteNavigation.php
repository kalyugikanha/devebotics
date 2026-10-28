<?php

namespace App\Support;

use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\Page;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Builds the header/footer navigation as plain arrays (cached). Items whose
 * targets are unpublished or missing are dropped automatically, so the site
 * never renders dead links.
 */
class SiteNavigation
{
    public const CACHE_KEY = 'site.navigation';

    /**
     * Icons for menu items that have none set, matched on the label.
     *
     * @var array<string, string>
     */
    private const DEFAULT_ICONS = [
        'about' => 'building-office-2',
        'team' => 'user-group',
        'engagement' => 'puzzle-piece',
        'pricing' => 'puzzle-piece',
        'review' => 'chat-bubble-left-right',
        'testimonial' => 'chat-bubble-left-right',
        'career' => 'rocket-launch',
        'faq' => 'light-bulb',
        'case stud' => 'presentation-chart-line',
        'insight' => 'newspaper',
        'blog' => 'newspaper',
        'contact' => 'paper-airplane',
        'product' => 'cube',
        'industr' => 'globe-alt',
    ];

    /**
     * @return array{menus: array<string, array{title: ?string, items: array<int, array<string, mixed>>}>, mega: array<int, array<string, mixed>>, cookie_policy_url: ?string}
     */
    public function get(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(10), fn (): array => [
            'menus' => $this->menus(),
            'mega' => $this->megaMenu(),
            'cookie_policy_url' => Page::query()->published()->where('slug', 'cookie-policy')->exists() ? url('/cookie-policy') : null,
        ]);
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, array{title: ?string, items: array<int, array<string, mixed>>}>
     */
    private function menus(): array
    {
        $menus = NavigationMenu::query()
            ->where('is_active', true)
            ->with(['allItems' => fn ($q) => $q->where('is_active', true)->with('linkable')])
            ->get();

        $result = [];

        foreach ($menus as $menu) {
            $items = $menu->allItems;
            $build = function (?int $parentId) use (&$build, $items): array {
                return $items->where('parent_id', $parentId)->sortBy('sort_order')
                    ->map(function (NavigationItem $item) use ($build): ?array {
                        $children = $build($item->getKey());
                        $url = $item->resolveUrl();

                        if ($url === null && $children === [] && ! $item->isMegaMenu()) {
                            return null;
                        }

                        return [
                            'label' => $item->label,
                            'url' => $url,
                            'description' => $item->description,
                            'icon' => IconRegistry::isValid($item->icon) ? $item->icon : $this->defaultIcon($item->label),
                            'new_tab' => $item->open_in_new_tab,
                            'highlighted' => $item->is_highlighted,
                            'mega' => $item->isMegaMenu(),
                            'external' => $url !== null && $item->isExternal(),
                            'children' => $children,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all();
            };

            $result[$menu->location] = ['title' => $menu->title, 'items' => $build(null)];
        }

        return $result;
    }

    /**
     * Services grouped by category for the mega menu.
     *
     * @return array<int, array<string, mixed>>
     */
    private function megaMenu(): array
    {
        return ServiceCategory::query()->active()->ordered()
            ->with(['services' => fn ($q) => $q->published()->ordered()])
            ->get()
            ->filter(fn (ServiceCategory $category) => $category->services->isNotEmpty())
            ->map(function (ServiceCategory $category): array {
                return [
                    'name' => $category->name,
                    'icon' => IconRegistry::isValid($category->icon) ? $category->icon : 'squares-2x2',
                    'services' => $category->services->map(fn (Service $service) => [
                        'title' => $service->title,
                        'url' => $service->publicUrl(),
                        'icon' => IconRegistry::isValid($service->icon) ? $service->icon : null,
                        'excerpt' => Str::limit((string) $service->excerpt, 120),
                    ])->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function defaultIcon(?string $label): ?string
    {
        $label = Str::lower((string) $label);

        foreach (self::DEFAULT_ICONS as $keyword => $icon) {
            if (str_contains($label, $keyword)) {
                return $icon;
            }
        }

        return null;
    }
}
