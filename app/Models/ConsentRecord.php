<?php

namespace App\Models;

use App\Enums\ConsentType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['consentable_type', 'consentable_id', 'consent_type', 'granted', 'policy_version', 'details', 'ip_hash', 'user_agent', 'created_at'])]
class ConsentRecord extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'consent_type' => ConsentType::class,
            'granted' => 'boolean',
            'details' => 'array',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function consentable(): MorphTo
    {
        return $this->morphTo();
    }
}
