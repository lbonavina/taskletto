<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskRecurrence;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:65535'],
            'status' => ['sometimes', new Enum(TaskStatus::class)],
            'priority' => ['sometimes', new Enum(TaskPriority::class)],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'due_date'           => ['sometimes', 'nullable', 'date'],
            'recurrence'         => ['sometimes', new Enum(TaskRecurrence::class)],
            'recurrence_ends_at' => ['sometimes', 'nullable', 'date'],
            'estimated_minutes'  => ['sometimes', 'nullable', 'integer', 'min:0', 'max:99999'],
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
}