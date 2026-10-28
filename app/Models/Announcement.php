<?php

namespace App\Models;

use App\Enums\AnnouncementStyle;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['message', 'link_label', 'link_url', 'style', 'starts_at', 'ends_at', 'is_active', 'is_dismissible', 'sort_order'])]
class Announcement extends Model
{
    use Auditable, Sortable;

    protected function casts(): array
    {
        return [
            'style' => AnnouncementStyle::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'is_dismissible' => 'boolean',
        ];
    }

    /**
     * Active announcements inside their scheduling window.
     */
    #[Scope]
    protected function current(Builder $query): void
    {
        $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()));
    }

    /**
     * Stable identifier for the visitor's "dismissed" preference; changes when the message changes.
     */
    public function dismissKey(): string
    {
        return 'announcement-'.$this->id.'-'.substr(sha1($this->message.$this->updated_at), 0, 8);
    }
}
