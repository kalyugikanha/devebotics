<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasActiveFlag;
use App\Models\Concerns\Sortable;
use Database\Factories\TeamMemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'job_title', 'department', 'bio', 'photo_media_id', 'linkedin_url', 'github_url', 'x_url', 'is_leadership', 'sort_order', 'is_active', 'is_demo'])]
class TeamMember extends Model
{
    /** @use HasFactory<TeamMemberFactory> */
    use Auditable, FlagsDemoContent, HasActiveFlag, HasFactory, Sortable;

    protected function casts(): array
    {
        return ['is_leadership' => 'boolean'];
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))->filter()->take(2)->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
    }
}
