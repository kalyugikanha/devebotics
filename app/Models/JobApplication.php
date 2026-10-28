<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Models\Concerns\Auditable;
use Database\Factories\JobApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'reference', 'job_opening_id', 'name', 'email', 'phone', 'location', 'linkedin_url', 'portfolio_url',
    'cover_letter', 'resume_path', 'resume_name', 'status', 'internal_notes', 'rating', 'reviewed_by', 'ip_hash',
])]
#[Hidden(['resume_path', 'ip_hash', 'internal_notes'])]
class JobApplication extends Model
{
    /** @use HasFactory<JobApplicationFactory> */
    use Auditable, HasFactory, SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected array $auditExclude = ['ip_hash', 'cover_letter'];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'rating' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (JobApplication $application): void {
            $application->reference ??= static::generateReference();
        });
    }

    public static function generateReference(): string
    {
        do {
            $code = collect(range(1, 6))->map(fn () => '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'[random_int(0, 31)])->implode('');
            $reference = 'JOB-'.now()->format('ym').'-'.$code;
        } while (static::withTrashed()->where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * @return BelongsTo<JobOpening, $this>
     */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * @return MorphMany<ConsentRecord, $this>
     */
    public function consents(): MorphMany
    {
        return $this->morphMany(ConsentRecord::class, 'consentable');
    }
}
