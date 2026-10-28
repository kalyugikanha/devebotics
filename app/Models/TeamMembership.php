<?php

namespace App\Models;

use App\Enums\TeamRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A user's membership of a query desk team (the team_user pivot row).
 */
#[Fillable(['team_id', 'user_id', 'role', 'last_assigned_at'])]
class TeamMembership extends Model
{
    protected $table = 'team_user';

    protected function casts(): array
    {
        return [
            'role' => TeamRole::class,
            'last_assigned_at' => 'datetime',
        ];
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
