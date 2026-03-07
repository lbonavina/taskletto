<?php

namespace Tests\Unit;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_has_correct_casts(): void
    {
        $task = Task::factory()->create([
            'status'   => TaskStatus::Pending,
            'priority' => TaskPriority::High,
        ]);

        $this->assertInstanceOf(TaskStatus::class, $task->status);
        $this->assertInstanceOf(TaskPriority::class, $task->priority);
    }

    public function test_is_completed_returns_correct_value(): void
    {
        $pending   = Task::factory()->pending()->create();
        $completed = Task::factory()->completed()->create();

        $this->assertFalse($pending->isCompleted());
        $this->assertTrue($completed->isCompleted());
    }

    public function test_is_overdue_returns_true_for_past_due_date(): void
    {
        $task = Task::factory()->overdue()->create();

        $this->assertTrue($task->isOverdue());
    }

    public function test_complete_sets_status_and_timestamp(): void
    {
        $task = Task::factory()->pending()->create();
        $task->complete();

        $this->assertTrue($task->isCompleted());
        $this->assertNotNull($task->completed_at);
    }

    public function test_reopen_clears_completed_at(): void
    {
        $task = Task::factory()->completed()->create();
        $task->reopen();

        $this->assertFalse($task->isCompleted());
        $this->assertNull($task->completed_at);
    }

    public function test_scope_pending_returns_only_pending(): void
    {
        Task::factory(2)->pending()->create();
        Task::factory(3)->completed()->create();

        $this->assertEquals(2, Task::pending()->count());
    }

    public function test_scope_overdue_excludes_completed(): void
    {
        Task::factory()->overdue()->create(); // pending + overdue
        Task::factory()->completed()->create(['due_date' => now()->subDay()]); // completed, should NOT appear

        $this->assertEquals(1, Task::overdue()->count());
    }
}
