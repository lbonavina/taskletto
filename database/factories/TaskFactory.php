<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(TaskStatus::cases());

        return [
            'title' => $this->faker->sentence(rand(3, 8), false),
            'description' => $this->faker->optional(0.7)->paragraph(),
            'status' => $status,
            'priority' => $this->faker->randomElement(TaskPriority::cases()),
            'category_id' => $this->faker->optional(0.6) ? Category::inRandomOrder()->first()?->id ?? Category::factory() : null,
            'due_date' => $this->faker->optional(0.5)->dateTimeBetween('-1 week', '+1 month'),
            'completed_at' => $status === TaskStatus::Completed ? now() : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => TaskStatus::Pending, 'completed_at' => null]);
    }

    public function completed(): static
    {
        return $this->state(['status' => TaskStatus::Completed, 'completed_at' => now()]);
    }

    public function urgent(): static
    {
        return $this->state(['priority' => TaskPriority::Urgent]);
    }

    public function overdue(): static
    {
        return $this->state([
            'due_date' => now()->subDays(rand(1, 30)),
            'status' => TaskStatus::Pending,
        ]);
    }

    public function withCategory(?Category $category = null): static
    {
        return $this->state(['category_id' => $category?->id ?? Category::factory()]);
    }
}
