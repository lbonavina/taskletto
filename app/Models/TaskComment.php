<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComment extends Model
{
    protected $fillable = ['task_id', 'body'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}