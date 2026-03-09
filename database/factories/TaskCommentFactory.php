<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskCommentFactory extends Factory
{
    protected $model = TaskComment::class;

    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'body'    => $this->faker->paragraph(),
        ];
    }
}
