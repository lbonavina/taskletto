<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskRecurrence;
use App\Enums\TaskStatus;
use Carbon\Carbon;
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
        'recurrence',
        'recurrence_ends_at',
        'estimated_minutes',
        'tracked_seconds',
    ];

    protected $casts = [
        'status'             => TaskStatus::class,
        'priority'           => TaskPriority::class,
        'recurrence'         => TaskRecurrence::class,
        'due_date'           => 'date',
        'recurrence_ends_at' => 'date',
        'completed_at'       => 'datetime',
        'estimated_minutes'  => 'integer',
        'tracked_seconds'    => 'integer',
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
        return $query->whereDate('due_date', '<', today())
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
            && $this->due_date->startOfDay()->lt(today())
            && !$this->isCompleted();
    }

    public function isRecurring(): bool
    {
        return $this->recurrence !== TaskRecurrence::None;
    }

    public function nextDueDate(): ?Carbon
    {
        if (! $this->isRecurring() || ! $this->due_date) {
            return null;
        }

        $next = match ($this->recurrence) {
            TaskRecurrence::Daily   => $this->due_date->copy()->addDay(),
            TaskRecurrence::Weekly  => $this->due_date->copy()->addWeek(),
            TaskRecurrence::Monthly => $this->due_date->copy()->addMonth(),
            default                 => null,
        };

        if ($next && $this->recurrence_ends_at && $next->gt($this->recurrence_ends_at)) {
            return null;
        }

        return $next;
    }

    /**
     * Spawns the next occurrence when a recurring task is completed.
     * Returns the new Task or null if recurrence has ended.
     */
    public function spawnNextRecurrence(): ?self
    {
        $nextDue = $this->nextDueDate();

        if (! $nextDue) {
            return null;
        }

        return self::create([
            'title'              => $this->title,
            'description'        => $this->description,
            'status'             => TaskStatus::Pending,
            'priority'           => $this->priority,
            'category_id'        => $this->category_id,
            'due_date'           => $nextDue,
            'recurrence'         => $this->recurrence,
            'recurrence_ends_at' => $this->recurrence_ends_at,
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status'       => TaskStatus::Completed,
            'completed_at' => now(),
        ]);

        $this->spawnNextRecurrence();
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

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TaskTimeLog::class);
    }

    /** Returns tracked time as "Xh Ym" or "Zm" string */
    public function formattedTrackedTime(): string
    {
        $s = $this->tracked_seconds;
        if ($s < 60) return "{$s}s";
        $m = intdiv($s, 60);
        $h = intdiv($m, 60);
        $m = $m % 60;
        return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
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