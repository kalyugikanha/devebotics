<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use App\Enums\SectionBackground;
use App\Enums\SectionType;
use App\Models\Concerns\Auditable;
use Database\Factories\PageSectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['page_id', 'type', 'anchor', 'eyebrow', 'title', 'subtitle', 'content', 'data', 'media_id', 'background', 'animation', 'sort_order', 'is_enabled'])]
class PageSection extends Model
{
    /** @use HasFactory<PageSectionFactory> */
    use Auditable, HasFactory;

    protected function casts(): array
    {
        return [
            'content' => SanitizedHtml::class,
            'type' => SectionType::class,
            'background' => SectionBackground::class,
            'data' => 'array',
            'is_enabled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (PageSection $section) => $section->page?->touch());
        static::deleted(fn (PageSection $section) => $section->page?->touch());
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }
}
