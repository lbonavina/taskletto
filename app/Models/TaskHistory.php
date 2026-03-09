<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskHistory extends Model
{
    public $timestamps = false;

    protected $fillable = ['task_id', 'field', 'old_value', 'new_value', 'label', 'changed_by'];

    protected $casts = ['created_at' => 'datetime'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
            $model->label = $model->generateLabel();
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Generate a human-readable label for the change.
     */
    protected function generateLabel(): string
    {
        $fieldLabels = [
            'title' => __('app.field_title'),
            'description' => __('app.field_description'),
            'status' => __('app.field_status'),
            'priority' => __('app.field_priority'),
            'category_id' => __('app.field_category'),
            'due_date' => __('app.field_due_date'),
            'deleted' => __('app.field_deleted'),
            'restored' => __('app.field_restored'),
        ];

        $fieldName = $fieldLabels[$this->field] ?? $this->field;

        if ($this->field === 'deleted') {
            return __('app.history_task_deleted');
        }

        if ($this->field === 'restored') {
            return __('app.history_task_restored');
        }

        return __('app.history_field_changed', ['field' => $fieldName]);
    }
}
