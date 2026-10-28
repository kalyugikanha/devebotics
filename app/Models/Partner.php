<?php

namespace App\Models;

use App\Enums\PartnerType;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasActiveFlag;
use App\Models\Concerns\Sortable;
use Database\Factories\PartnerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name', 'type', 'logo_media_id', 'website_url', 'description', 'logo_usage_approved',
    'logo_approved_at', 'approval_reference', 'sort_order', 'is_active', 'is_demo',
])]
class Partner extends Model
{
    /** @use HasFactory<PartnerFactory> */
    use Auditable, FlagsDemoContent, HasActiveFlag, HasFactory, Sortable;

    protected function casts(): array
    {
        return [
            'type' => PartnerType::class,
            'logo_usage_approved' => 'boolean',
            'logo_approved_at' => 'datetime',
        ];
    }

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
}
