<?php

namespace App\Models;

use App\Enums\TestimonialStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\Sortable;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'source', 'client_name', 'company_name', 'job_title', 'email', 'email_hash', 'service_id', 'product_id',
    'project_name', 'rating', 'rating_communication', 'rating_quality', 'rating_delivery', 'feedback',
    'publish_permission', 'display_identity_consent', 'submitted_photo_path', 'photo_media_id', 'status',
    'is_featured', 'sort_order', 'admin_reply', 'moderation_note', 'ip_hash', 'content_hash', 'is_demo',
])]
#[Hidden(['email', 'email_hash', 'ip_hash', 'content_hash', 'admin_reply', 'moderation_note', 'submitted_photo_path'])]
class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use Auditable, FlagsDemoContent, HasFactory, SoftDeletes, Sortable;

    /**
     * @var array<int, string>
     */
    protected array $auditExclude = ['email', 'email_hash', 'ip_hash', 'content_hash'];

    protected function casts(): array
    {
        return [
            'email' => 'encrypted',
            'status' => TestimonialStatus::class,
            'rating' => 'integer',
            'rating_communication' => 'integer',
            'rating_quality' => 'integer',
            'rating_delivery' => 'integer',
            'publish_permission' => 'boolean',
            'display_identity_consent' => 'boolean',
            'is_featured' => 'boolean',
            'moderated_at' => 'datetime',
            'replied_at' => 'datetime',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Reviews that may appear on the public website: approved by a moderator
     * AND the client granted permission to publish.
     */
    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('status', TestimonialStatus::Approved->value)->where('publish_permission', true);
    }

    /**
     * Name shown publicly — only with the client's consent to display their identity.
     */
    public function publicName(): string
    {
        if ($this->display_identity_consent) {
            return $this->client_name;
        }

        return 'Verified client';
    }

    public function publicCompany(): ?string
    {
        return $this->display_identity_consent ? $this->company_name : null;
    }

    public function publicInitials(): string
    {
        if (! $this->display_identity_consent) {
            return 'VC';
        }

        return collect(explode(' ', $this->client_name))->filter()->take(2)->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
    }

    public static function hashEmail(string $email): string
    {
        return hash_hmac('sha256', mb_strtolower(trim($email)), (string) config('app.key'));
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
     * @return BelongsTo<Media, $this>
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * @return MorphMany<ConsentRecord, $this>
     */
    public function consents(): MorphMany
    {
        return $this->morphMany(ConsentRecord::class, 'consentable');
    }
}
