<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NoteShare extends Model
{
    protected $fillable = [
        'note_id',
        'token',
        'visibility',
        'allowed_emails',
        'expires_at',
        'views',
        'active',
    ];

    protected $casts = [
        'allowed_emails' => 'array',
        'expires_at'     => 'datetime',
        'active'         => 'boolean',
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAccessible(): bool
    {
        return $this->active && ! $this->isExpired();
    }

    public function allowsEmail(string $email): bool
    {
        if ($this->visibility === 'public') return true;
        $allowed = $this->allowed_emails ?? [];
        return in_array(strtolower(trim($email)), array_map('strtolower', $allowed));
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public static function generateToken(): string
    {
        do {
            $token = Str::random(32);
        } while (static::where('token', $token)->exists());

        return $token;
    }

    public function url(): string
    {
        return route('share.show', $this->token);
    }
}
