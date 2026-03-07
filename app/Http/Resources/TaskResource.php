<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'status'       => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'priority'     => [
                'value' => $this->priority->value,
                'label' => $this->priority->label(),
            ],
            'category'     => $this->category,
            'due_date'     => $this->due_date?->toDateString(),
            'is_overdue'   => $this->isOverdue(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at'   => $this->created_at->toIso8601String(),
            'updated_at'   => $this->updated_at->toIso8601String(),
        ];
    }
}
