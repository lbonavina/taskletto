<?php

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── INDEX ────────────────────────────────────────────────────────────────

    public function test_can_list_tasks(): void
    {
        Task::factory(3)->create();

        $response = $this->getJson('/api/v1/tasks');

        $response->assertOk()
                 ->assertJsonStructure([
                     'data' => [['id', 'title', 'status', 'priority', 'created_at']],
                     'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                 ]);
    }

    public function test_can_filter_tasks_by_status(): void
    {
        Task::factory(2)->pending()->create();
        Task::factory(3)->completed()->create();

        $response = $this->getJson('/api/v1/tasks?status=pending');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_filter_tasks_by_priority(): void
    {
        Task::factory(2)->urgent()->create();
        Task::factory(3)->create(['priority' => TaskPriority::Low]);

        $response = $this->getJson('/api/v1/tasks?priority=urgent');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_search_tasks_by_title(): void
    {
        Task::factory()->create(['title' => 'Tarefa especial de teste']);
        Task::factory(3)->create();

        $response = $this->getJson('/api/v1/tasks?search=especial');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    // ─── STORE ────────────────────────────────────────────────────────────────

    public function test_can_create_task(): void
    {
        $payload = [
            'title'    => 'Nova tarefa de teste',
            'priority' => 'high',
            'category' => 'Testes',
            'due_date' => now()->addDays(7)->toDateString(),
        ];

        $response = $this->postJson('/api/v1/tasks', $payload);

        $response->assertCreated()
                 ->assertJsonPath('data.title', $payload['title'])
                 ->assertJsonPath('data.status.value', 'pending'); // default

        $this->assertDatabaseHas('tasks', ['title' => $payload['title']]);
    }

    public function test_cannot_create_task_without_title(): void
    {
        $response = $this->postJson('/api/v1/tasks', ['priority' => 'high']);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['title']);
    }

    public function test_cannot_create_task_with_invalid_status(): void
    {
        $response = $this->postJson('/api/v1/tasks', [
            'title'  => 'Tarefa inválida',
            'status' => 'invalid_status',
        ]);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['status']);
    }

    // ─── SHOW ─────────────────────────────────────────────────────────────────

    public function test_can_show_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        $response->assertOk()
                 ->assertJsonPath('data.id', $task->id);
    }

    public function test_returns_404_for_nonexistent_task(): void
    {
        $this->getJson('/api/v1/tasks/9999')
             ->assertNotFound()
             ->assertJsonPath('error', 'not_found');
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create(['title' => 'Título antigo']);

        $response = $this->putJson("/api/v1/tasks/{$task->id}", [
            'title'    => 'Título atualizado',
            'priority' => 'urgent',
        ]);

        $response->assertOk()
                 ->assertJsonPath('data.title', 'Título atualizado')
                 ->assertJsonPath('data.priority.value', 'urgent');
    }

    // ─── DESTROY ──────────────────────────────────────────────────────────────

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $this->deleteJson("/api/v1/tasks/{$task->id}")
             ->assertNoContent();

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    // ─── COMPLETE / REOPEN ───────────────────────────────────────────────────

    public function test_can_complete_task(): void
    {
        $task = Task::factory()->pending()->create();

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/complete");

        $response->assertOk()
                 ->assertJsonPath('data.status.value', 'completed');

        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_cannot_complete_already_completed_task(): void
    {
        $task = Task::factory()->completed()->create();

        $this->patchJson("/api/v1/tasks/{$task->id}/complete")
             ->assertStatus(409)
             ->assertJsonPath('error', 'already_completed');
    }

    public function test_can_reopen_completed_task(): void
    {
        $task = Task::factory()->completed()->create();

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/reopen");

        $response->assertOk()
                 ->assertJsonPath('data.status.value', 'pending');

        $this->assertNull($task->fresh()->completed_at);
    }

    // ─── STATS ────────────────────────────────────────────────────────────────

    public function test_can_get_stats(): void
    {
        Task::factory(5)->pending()->create();
        Task::factory(3)->completed()->create();

        $response = $this->getJson('/api/v1/tasks/stats');

        $response->assertOk()
                 ->assertJsonStructure([
                     'data' => ['total', 'by_status', 'by_priority', 'overdue', 'completion_rate'],
                 ]);

        $this->assertEquals(8, $response->json('data.total'));
    }

    // ─── HEALTH ───────────────────────────────────────────────────────────────

    public function test_health_endpoint(): void
    {
        $this->getJson('/api/v1/health')
             ->assertOk()
             ->assertJsonPath('status', 'ok');
    }
}
