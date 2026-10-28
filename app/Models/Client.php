<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasActiveFlag;
use App\Models\Concerns\Sortable;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'logo_media_id', 'website_url', 'description', 'industry_id', 'logo_usage_approved',
    'logo_approved_at', 'approval_reference', 'show_on_home', 'sort_order', 'is_active', 'is_demo',
])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use Auditable, FlagsDemoContent, HasActiveFlag, HasFactory, Sortable;

    protected function casts(): array
    {
        return [
            'logo_usage_approved' => 'boolean',
            'logo_approved_at' => 'datetime',
            'show_on_home' => 'boolean',
        ];
    }

    /**
     * Clients whose logo may be shown publicly (active + recorded approval + logo uploaded).
     */
    #[Scope]
    protected function publiclyListed(Builder $query): void
    {
        $query->where('is_active', true)->where('logo_usage_approved', true)->whereNotNull('logo_media_id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    /**
     * @return BelongsTo<Industry, $this>
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * @return HasMany<Portfolio, $this>
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }
}
