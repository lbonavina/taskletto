<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskRecurrence;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'status' => ['sometimes', new Enum(TaskStatus::class)],
            'priority' => ['sometimes', new Enum(TaskPriority::class)],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'due_date'           => ['nullable', 'date', 'after_or_equal:today'],
            'recurrence'         => ['sometimes', new Enum(TaskRecurrence::class)],
            'recurrence_ends_at' => ['nullable', 'date', 'after:due_date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'título',
            'description' => 'descrição',
            'status' => 'status',
            'priority' => 'prioridade',
            'category_id' => 'categoria',
            'due_date' => 'data de vencimento',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->mergeIfMissing([
            'status'     => TaskStatus::Pending->value,
            'priority'   => TaskPriority::Medium->value,
            'recurrence' => 'none',
        ]);
    }
}