<?php

namespace App\Models;

use App\Enums\BillingPeriod;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasActiveFlag;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

#[Fillable(['product_id', 'name', 'price', 'currency', 'billing_period', 'description', 'features', 'cta_label', 'cta_url', 'is_highlighted', 'sort_order', 'is_active'])]
class ProductPlan extends Model
{
    use Auditable, HasActiveFlag;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'billing_period' => BillingPeriod::class,
            'features' => 'array',
            'is_highlighted' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function formattedPrice(): ?string
    {
        if ($this->price === null) {
            return null;
        }

        return Number::currency((float) $this->price, $this->currency ?: 'USD', precision: fmod((float) $this->price, 1.0) === 0.0 ? 0 : 2);
    }
}
