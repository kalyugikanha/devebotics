<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasActiveFlag;
use App\Models\Concerns\Sortable;
use Database\Factories\FaqFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['faq_category_id', 'question', 'answer', 'show_on_home', 'sort_order', 'is_active'])]
class Faq extends Model
{
    /** @use HasFactory<FaqFactory> */
    use Auditable, HasActiveFlag, HasFactory, Sortable;

    protected function casts(): array
    {
        return [
            'answer' => SanitizedHtml::class,
            'show_on_home' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<FaqCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }

    /**
     * @return MorphToMany<Service, $this>
     */
    public function services(): MorphToMany
    {
        return $this->morphedByMany(Service::class, 'faqable');
    }

    /**
     * @return MorphToMany<Product, $this>
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'faqable');
    }

    /**
     * @return MorphToMany<Industry, $this>
     */
    public function industries(): MorphToMany
    {
        return $this->morphedByMany(Industry::class, 'faqable');
    }
}
