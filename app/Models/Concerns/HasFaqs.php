<?php

namespace App\Models\Concerns;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Model
 */
trait HasFaqs
{
    /**
     * @return MorphToMany<Faq, $this>
     */
    public function faqs(): MorphToMany
    {
        return $this->morphToMany(Faq::class, 'faqable')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }
}
