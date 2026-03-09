<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskHistory;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    /**
     * Handle the Task "updated" event.
     * Records changes to task attributes in the history table.
     */
    public function updated(Task $task): void
    {
        $changes = $task->getDirty();
        $original = $task->getOriginal();

        // Skip if no meaningful changes
        if (empty($changes)) {
            return;
        }

        // Fields to track
        $trackableFields = [
            'title',
            'description',
            'status',
            'priority',
            'category_id',
            'due_date',
        ];

        foreach ($changes as $field => $newValue) {
            // Skip untracked fields and timestamps
            if (!in_array($field, $trackableFields)) {
                continue;
            }

            // Get the old value
            $oldValue = $original[$field] ?? null;

            // Handle enum values
            if ($newValue instanceof \BackedEnum) {
                $newValue = $newValue->value;
            }
            if ($oldValue instanceof \BackedEnum) {
                $oldValue = $oldValue->value;
            }

            // Handle date objects
            if ($newValue instanceof \DateTimeInterface) {
                $newValue = $newValue->format('Y-m-d');
            }
            if ($oldValue instanceof \DateTimeInterface) {
                $oldValue = $oldValue->format('Y-m-d');
            }

            // Handle string dates that might have time component
            if ($field === 'due_date') {
                $newValue = $newValue ? \Carbon\Carbon::parse($newValue)->format('Y-m-d') : null;
                $oldValue = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;
            }

            // Skip if values are the same
            if ((string) $oldValue === (string) $newValue) {
                continue;
            }

            TaskHistory::create([
                'task_id' => $task->id,
                'field' => $field,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'changed_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        TaskHistory::create([
            'task_id' => $task->id,
            'field' => 'deleted',
            'old_value' => null,
            'new_value' => true,
            'changed_by' => Auth::id(),
        ]);
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        TaskHistory::create([
            'task_id' => $task->id,
            'field' => 'restored',
            'old_value' => true,
            'new_value' => null,
            'changed_by' => Auth::id(),
        ]);
    }
}
