<?php

namespace App\Models;

use App\Enums\Plan;
use App\Services\PlanService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'oauth_provider',
        'oauth_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /** True if the user has a local password set (i.e. not OAuth-only). */
    public function hasPassword(): bool
    {
        return !is_null($this->getAttributes()['password'] ?? null);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    // ── Plan helpers ──────────────────────────────────────────────────────────

    public function plan(): Plan
    {
        return app(PlanService::class)->plan($this);
    }

    public function onPro(): bool
    {
        return $this->plan() === Plan::Pro;
    }

    public function canCreate(string $resource): bool
    {
        return app(PlanService::class)->canCreate($this, $resource);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Display initials (up to 2 chars) for avatar fallback. */
    public function initials(): string
    {
        $parts = explode(' ', trim($this->name));
        if (count($parts) >= 2) {
            return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
        }
        return strtoupper(mb_substr($this->name, 0, 2));
    }
}
