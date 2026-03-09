<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTimeLog extends Model
{
    protected $fillable = ['task_id', 'started_at', 'ended_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function durationSeconds(): int
    {
        $end = $this->ended_at ?? now();
        return (int) $this->started_at->diffInSeconds($end);
    }
}
