<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use App\Contracts\HasPublicUrl;
use App\Enums\EmploymentType;
use App\Enums\WorkplaceType;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsSlugRedirects;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\TracksEditors;
use Database\Factories\JobOpeningFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Number;

#[Fillable([
    'title', 'slug', 'department', 'location', 'country_code', 'employment_type', 'workplace_type', 'experience_level',
    'salary_min', 'salary_max', 'salary_currency', 'salary_period', 'show_salary', 'summary', 'description',
    'responsibilities', 'requirements', 'benefits', 'status', 'published_at', 'valid_through', 'is_featured',
    'sort_order', 'is_demo',
])]
class JobOpening extends Model implements HasPublicUrl
{
    /** @use HasFactory<JobOpeningFactory> */
    use Auditable, FlagsDemoContent, HasFactory, HasSeo, HasSlug, Publishable, RecordsSlugRedirects, SoftDeletes, Sortable, TracksEditors;

    protected function casts(): array
    {
        return [
            'description' => SanitizedHtml::class,
            'employment_type' => EmploymentType::class,
            'workplace_type' => WorkplaceType::class,
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'show_salary' => 'boolean',
            'responsibilities' => 'array',
            'requirements' => 'array',
            'benefits' => 'array',
            'valid_through' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Published openings that have not passed their closing date.
     */
    #[Scope]
    protected function open(Builder $query): void
    {
        $query->published()->where(fn (Builder $q) => $q->whereNull('valid_through')->orWhere('valid_through', '>=', now()->toDateString()));
    }

    public function isOpen(): bool
    {
        return $this->isPubliclyVisible() && ($this->valid_through === null || $this->valid_through->endOfDay()->isFuture());
    }

    public function salaryRange(): ?string
    {
        if (! $this->show_salary || $this->salary_min === null) {
            return null;
        }

        $currency = $this->salary_currency ?: 'USD';
        $range = Number::currency((float) $this->salary_min, $currency, precision: 0);

        if ($this->salary_max !== null && (float) $this->salary_max > (float) $this->salary_min) {
            $range .= ' – '.Number::currency((float) $this->salary_max, $currency, precision: 0);
        }

        return $range.($this->salary_period ? ' / '.$this->salary_period : '');
    }

    /**
     * @return HasMany<JobApplication, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function publicUrl(): string
    {
        return route('careers.show', $this->slug);
    }

    public function publicPathForSlug(string $slug): string
    {
        return '/careers/'.$slug;
    }
}
