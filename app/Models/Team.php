<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use App\Enums\InquiryTopic;
use App\Enums\TeamRole;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A query desk team (department). Inquiries are routed to a team by topic or
 * service; members work the team's queue, leads assign within the team.
 */
#[Fillable(['name', 'slug', 'description', 'color', 'email', 'topics', 'auto_assign', 'is_active', 'sort_order'])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use Auditable, HasFactory, HasSlug, Sortable;

    protected string $slugSource = 'name';

    protected function casts(): array
    {
        return [
            'topics' => 'array',
            'auto_assign' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['role', 'last_assigned_at'])->withTimestamps();
    }

    /**
     * @return HasMany<TeamMembership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(TeamMembership::class);
    }

    /**
     * @return BelongsToMany<Service, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * @return HasMany<ContactInquiry, $this>
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(ContactInquiry::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function handlesTopic(InquiryTopic $topic): bool
    {
        return in_array($topic->value, (array) $this->topics, true);
    }

    public function isLead(User $user): bool
    {
        return $this->memberships()->where('user_id', $user->getKey())->where('role', TeamRole::Lead->value)->exists();
    }

    public function openInquiriesCount(): int
    {
        return $this->inquiries()->whereIn('status', InquiryStatus::values(InquiryStatus::open()))->where('is_spam', false)->count();
    }
}
