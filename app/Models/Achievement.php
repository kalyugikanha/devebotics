<?php

namespace App\Models;

use App\Enums\AchievementType;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasActiveFlag;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['type', 'title', 'issuer', 'issued_on', 'expires_on', 'description', 'media_id', 'verification_url', 'is_verified', 'sort_order', 'is_active', 'is_demo'])]
class Achievement extends Model
{
    use Auditable, FlagsDemoContent, HasActiveFlag, Sortable;

    protected function casts(): array
    {
        return [
            'type' => AchievementType::class,
            'issued_on' => 'date',
            'expires_on' => 'date',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Only active, verified and unexpired achievements are published.
     */
    #[Scope]
    protected function publishable(Builder $query): void
    {
        $query->where('is_active', true)
            ->where('is_verified', true)
            ->where(fn (Builder $q) => $q->whereNull('expires_on')->orWhere('expires_on', '>=', now()->toDateString()));
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
