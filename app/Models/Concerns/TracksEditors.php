<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Fills created_by / updated_by from the authenticated administrator.
 *
 * @mixin Model
 */
trait TracksEditors
{
    public static function bootTracksEditors(): void
    {
        static::creating(function (Model $model): void {
            if (Auth::id() !== null && blank($model->getAttribute('created_by'))) {
                $model->setAttribute('created_by', Auth::id());
            }
        });

        static::saving(function (Model $model): void {
            if (Auth::id() !== null) {
                $model->setAttribute('updated_by', Auth::id());
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
