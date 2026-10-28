<?php

namespace App\Models;

use App\Enums\InquiryActivityType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['contact_inquiry_id', 'user_id', 'type', 'body', 'meta', 'created_at'])]
class InquiryActivity extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'type' => InquiryActivityType::class,
            'meta' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ContactInquiry, $this>
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(ContactInquiry::class, 'contact_inquiry_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
