<?php

namespace App\Models;

use App\Enums\Plan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'status',
        'abacatepay_billing_id',
        'abacatepay_customer_id',
        'current_period_ends_at',
        'cancelled_at',
    ];

    protected $casts = [
        'plan'                   => Plan::class,
        'current_period_ends_at' => 'datetime',
        'cancelled_at'           => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired') return true;
        if ($this->current_period_ends_at && $this->current_period_ends_at->isPast()) return true;
        return false;
    }
}
