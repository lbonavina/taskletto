<?php

namespace Tests\Unit;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Category;
use App\Models\Task;
use App\Models\TaskHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_is_recorded_when_task_is_updated(): void
    {
        $task = Task::factory()->create(['title' => 'Original title']);

        $task->update(['title' => 'Updated title']);

        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'field' => 'title',
            'old_value' => 'Original title',
            'new_value' => 'Updated title',
        ]);
    }

    public function test_history_records_status_change(): void
    {
        $task = Task::factory()->pending()->create();

        $task->update(['status' => TaskStatus::InProgress]);

        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'field' => 'status',
            'old_value' => 'pending',
            'new_value' => 'in_progress',
        ]);
    }

    public function test_history_records_priority_change(): void
    {
        $task = Task::factory()->create(['priority' => TaskPriority::Low]);

        $task->update(['priority' => TaskPriority::Urgent]);

        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'field' => 'priority',
            'old_value' => 'low',
            'new_value' => 'urgent',
        ]);
    }

    public function test_history_records_category_change(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $task = Task::factory()->withCategory($category1)->create();

        $task->update(['category_id' => $category2->id]);

        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'field' => 'category_id',
            'old_value' => (string) $category1->id,
            'new_value' => (string) $category2->id,
        ]);
    }

    public function test_history_records_due_date_change(): void
    {
        $task = Task::factory()->create(['due_date' => '2024-01-15']);

        $task->update(['due_date' => '2024-02-20']);

        $history = TaskHistory::where('task_id', $task->id)
            ->where('field', 'due_date')
            ->first();

        $this->assertNotNull($history);
        $this->assertEquals('2024-01-15', $history->old_value);
        $this->assertEquals('2024-02-20', $history->new_value);
    }

    public function test_history_is_recorded_when_task_is_deleted(): void
    {
        $task = Task::factory()->create();

        $task->delete();

        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'field' => 'deleted',
            'new_value' => '1',
        ]);
    }

    public function test_history_is_recorded_when_task_is_restored(): void
    {
        $task = Task::factory()->create();
        $task->delete();
        $task->restore();

        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'field' => 'restored',
        ]);
    }

    public function test_no_history_recorded_for_same_values(): void
    {
        $task = Task::factory()->create(['title' => 'Same title']);
        $initialCount = TaskHistory::where('task_id', $task->id)->count();

        $task->update(['title' => 'Same title']);

        $this->assertEquals($initialCount, TaskHistory::where('task_id', $task->id)->count());
    }

    public function test_history_label_is_generated(): void
    {
        $task = Task::factory()->create(['title' => 'Old']);
        $task->update(['title' => 'New']);

        $history = TaskHistory::where('task_id', $task->id)->first();

        $this->assertNotNull($history->label);
        $this->assertStringContainsString('Title', $history->label);
    }
}
