<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'location', 'title', 'is_active'])]
class NavigationMenu extends Model
{
    use Auditable;

    public const LOCATIONS = [
        'header' => 'Header (primary navigation)',
        'footer_1' => 'Footer column 1',
        'footer_2' => 'Footer column 2',
        'footer_3' => 'Footer column 3',
        'footer_legal' => 'Footer legal links',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /**
     * Top-level items.
     *
     * @return HasMany<NavigationItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class)->whereNull('parent_id')->orderBy('sort_order');
    }

    /**
     * @return HasMany<NavigationItem, $this>
     */
    public function allItems(): HasMany
    {
        return $this->hasMany(NavigationItem::class)->orderBy('sort_order');
    }
}
