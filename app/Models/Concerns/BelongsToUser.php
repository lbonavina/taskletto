<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Automatically scopes all queries to the authenticated user
 * and sets user_id on create.
 */
trait BelongsToUser
{
    public static function bootBelongsToUser(): void
    {
        // Auto-assign user_id when creating
        static::creating(function ($model) {
            if (empty($model->user_id) && Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    public function initializeBelongsToUser(): void
    {
        $this->fillable[] = 'user_id';
    }

    // ── Global scope ──────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where($builder->getModel()->getTable() . '.user_id', Auth::id());
            }
        });
    }

    // ── Relationship ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
