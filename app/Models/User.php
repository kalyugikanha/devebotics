<?php

namespace App\Models;

use App\Mail\TemplatedMail;
use App\Models\Concerns\Auditable;
use App\Support\Mail\Outbox;
use App\Support\Permissions\PermissionRegistry;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'job_title', 'phone', 'avatar_path', 'is_active', 'invited_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasAvatar, HasEmailAuthentication
{
    /** @use HasFactory<UserFactory> */
    use Auditable, HasFactory, HasRoles, Notifiable;

    use InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery, InteractsWithEmailAuthentication;

    /**
     * @var array<int, string>
     */
    protected array $auditExclude = ['last_login_at', 'last_login_ip', 'has_email_authentication'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
            'invited_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if ($user->isDirty('is_active')) {
                $user->deactivated_at = $user->is_active ? null : now();
            }

            if ($user->exists && $user->isDirty('password')) {
                $user->password_changed_at = now();
            }
        });

        // A deactivated account loses every active session immediately.
        static::saved(function (User $user): void {
            if ($user->wasChanged('is_active') && ! $user->is_active) {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->getKey())->delete();
            }
        });

        // Security notifications to the account owner (updates only, never on account creation).
        static::updated(function (User $user): void {
            $event = match (true) {
                $user->wasChanged('password') => 'The password for your account was changed.',
                $user->wasChanged('app_authentication_secret') => filled($user->app_authentication_secret)
                    ? 'Two-factor authentication (authenticator app) was enabled on your account.'
                    : 'Two-factor authentication (authenticator app) was removed from your account.',
                $user->wasChanged('has_email_authentication') => $user->has_email_authentication
                    ? 'Email two-factor authentication was enabled on your account.'
                    : 'Email two-factor authentication was disabled on your account.',
                default => null,
            };

            if ($event !== null) {
                app(Outbox::class)->send($user->email, new TemplatedMail('security_alert', [
                    'name' => $user->name,
                    'event' => $event,
                    'time' => now()->toDayDateTimeString().' UTC',
                ], url('/admin')), $user);
            }
        });
    }

    public function hasMultiFactorAuthentication(): bool
    {
        return filled($this->app_authentication_secret) || $this->has_email_authentication;
    }

    /**
     * Only active users holding the dashboard permission may enter the admin panel.
     * Enforced on every request by Filament's Authenticate middleware.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->can('dashboard.view');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(PermissionRegistry::SUPER_ADMIN);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null;
    }

    /**
     * @return HasOne<Author, $this>
     */
    public function author(): HasOne
    {
        return $this->hasOne(Author::class);
    }

    /**
     * @return HasMany<ContactInquiry, $this>
     */
    public function assignedInquiries(): HasMany
    {
        return $this->hasMany(ContactInquiry::class, 'assigned_to');
    }

    /**
     * Query desk teams this user belongs to (pivot: role, last_assigned_at).
     *
     * @return BelongsToMany<Team, $this>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot(['role', 'last_assigned_at'])->withTimestamps();
    }

    public function belongsToTeam(?int $teamId): bool
    {
        return $teamId !== null && $this->teams()->whereKey($teamId)->exists();
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
