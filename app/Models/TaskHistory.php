<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskHistory extends Model
{
    public $timestamps = false;

    protected $fillable = ['task_id', 'field', 'old_value', 'new_value', 'label'];

    protected $casts = ['created_at' => 'datetime'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
