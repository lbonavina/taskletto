<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'category_id',
        'due_date',
        'completed_at',
        'sort_order',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', TaskStatus::Pending);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TaskStatus::Completed);
    }

    public function scopeByPriority($query, TaskPriority $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', TaskStatus::Completed);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::Completed;
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !$this->isCompleted();
    }

    public function complete(): void
    {
        $this->update([
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => TaskStatus::Pending,
            'completed_at' => null,
        ]);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function histories(): HasMany
    {
        return $this->hasMany(TaskHistory::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->latest();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}