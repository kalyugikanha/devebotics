<?php

namespace App\Models;

use App\Enums\NewsletterStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['email', 'name', 'status', 'confirmed_at', 'unsubscribed_at', 'ip_hash', 'source'])]
#[Hidden(['ip_hash'])]
class NewsletterSubscriber extends Model
{
    protected function casts(): array
    {
        return [
            'status' => NewsletterStatus::class,
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }
}
