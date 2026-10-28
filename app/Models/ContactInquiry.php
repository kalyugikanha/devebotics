<?php

namespace App\Models;

use App\Enums\InquiryPriority;
use App\Enums\InquiryStatus;
use App\Enums\InquiryTopic;
use App\Enums\InquiryType;
use App\Models\Concerns\Auditable;
use Carbon\CarbonInterface;
use Database\Factories\ContactInquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'reference', 'type', 'topic', 'team_id', 'name', 'email', 'phone', 'company', 'service_id', 'product_id', 'budget_range', 'timeline',
    'subject', 'message', 'attachment_path', 'attachment_name', 'status', 'priority', 'assigned_to', 'follow_up_at',
    'source_url', 'utm', 'ip_hash', 'user_agent', 'content_hash', 'is_spam', 'duplicate_of_id', 'first_response_at',
])]
#[Hidden(['ip_hash', 'content_hash', 'attachment_path'])]
class ContactInquiry extends Model
{
    /** @use HasFactory<ContactInquiryFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected array $auditExclude = ['ip_hash', 'content_hash', 'user_agent', 'message'];

    protected function casts(): array
    {
        return [
            'type' => InquiryType::class,
            'topic' => InquiryTopic::class,
            'status' => InquiryStatus::class,
            'priority' => InquiryPriority::class,
            'utm' => 'array',
            'is_spam' => 'boolean',
            'follow_up_at' => 'datetime',
            'closed_at' => 'datetime',
            'first_response_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ContactInquiry $inquiry): void {
            $inquiry->reference ??= static::generateReference('SAI');
        });

        static::saving(function (ContactInquiry $inquiry): void {
            if ($inquiry->isDirty('status')) {
                $inquiry->closed_at = $inquiry->status?->isClosed() ? now() : null;

                // The first move away from "New" counts as the first response.
                if ($inquiry->exists && $inquiry->first_response_at === null && $inquiry->status !== InquiryStatus::New) {
                    $inquiry->first_response_at = now();
                }
            }
        });
    }

    /**
     * Human-friendly unique reference, e.g. SAI-2610-K7Q3MZ (no ambiguous characters).
     */
    public static function generateReference(string $prefix): string
    {
        do {
            $code = collect(range(1, 6))->map(fn () => '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'[random_int(0, 31)])->implode('');
            $reference = $prefix.'-'.now()->format('ym').'-'.$code;
        } while (static::withTrashed()->where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Inquiries a user is allowed to see: all with `inquiries.view_all`;
     * otherwise those assigned to them, plus every inquiry of their teams
     * with `inquiries.view_team`. Unrouted inquiries need `view_all`.
     */
    #[Scope]
    protected function visibleTo(Builder $query, User $user): void
    {
        if ($user->can('inquiries.view_all')) {
            return;
        }

        $canViewTeam = $user->can('inquiries.view_team');

        $query->where(function (Builder $visible) use ($user, $canViewTeam): void {
            $visible->where('assigned_to', $user->getKey());

            if ($canViewTeam) {
                $visible->orWhereIn('team_id', self::teamIdsQuery($user));
            }
        });
    }

    /**
     * Unassigned inquiries waiting in the user's teams.
     */
    #[Scope]
    protected function inTeamQueueOf(Builder $query, User $user): void
    {
        $query->whereNull('assigned_to')->whereIn('team_id', self::teamIdsQuery($user));
    }

    #[Scope]
    protected function open(Builder $query): void
    {
        $query->whereIn('status', InquiryStatus::values(InquiryStatus::open()));
    }

    #[Scope]
    protected function closed(Builder $query): void
    {
        $query->whereIn('status', InquiryStatus::values(InquiryStatus::closed()));
    }

    /**
     * Open, non-spam inquiries that have not had a first response within the target time.
     */
    #[Scope]
    protected function overdue(Builder $query): void
    {
        $query->whereIn('status', InquiryStatus::values(InquiryStatus::open()))
            ->where('is_spam', false)
            ->whereNull('first_response_at')
            ->where('created_at', '<=', now()->subHours(self::responseTargetHours()));
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    private static function teamIdsQuery(User $user)
    {
        return TeamMembership::query()->select('team_id')->where('user_id', $user->getKey())->toBase();
    }

    public static function responseTargetHours(): int
    {
        return max(1, (int) settings('forms.response_target_hours', 24));
    }

    public function responseDueAt(): ?CarbonInterface
    {
        return $this->created_at?->copy()->addHours(self::responseTargetHours());
    }

    public function isOverdue(): bool
    {
        return $this->first_response_at === null
            && ! $this->is_spam
            && in_array($this->status, InquiryStatus::open(), true)
            && $this->responseDueAt()?->isPast() === true;
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return HasMany<InquiryActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(InquiryActivity::class)->latest('created_at')->latest('id');
    }

    /**
     * @return BelongsTo<ContactInquiry, $this>
     */
    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'duplicate_of_id');
    }

    /**
     * @return MorphMany<ConsentRecord, $this>
     */
    public function consents(): MorphMany
    {
        return $this->morphMany(ConsentRecord::class, 'consentable');
    }

    /**
     * @return MorphMany<EmailLog, $this>
     */
    public function emailLogs(): MorphMany
    {
        return $this->morphMany(EmailLog::class, 'related');
    }

    public function excerpt(int $length = 140): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', $this->message) ?? ''), $length);
    }
}
