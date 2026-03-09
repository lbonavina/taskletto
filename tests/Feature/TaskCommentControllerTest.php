<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCommentControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── INDEX ────────────────────────────────────────────────────────────────

    public function test_can_list_comments_for_task(): void
    {
        $task = Task::factory()->create();
        TaskComment::factory(3)->create(['task_id' => $task->id]);

        $response = $this->getJson("/api/v1/tasks/{$task->id}/comments");

        $response->assertOk()
            ->assertJsonStructure([
                'data'         => [['id', 'body', 'created_at', 'updated_at', 'edited']],
                'current_page',
                'last_page',
                'total',
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_comments_are_paginated(): void
    {
        $task = Task::factory()->create();
        TaskComment::factory(12)->create(['task_id' => $task->id]);

        $response = $this->getJson("/api/v1/tasks/{$task->id}/comments?page=1");

        $response->assertOk();
        $this->assertCount(10, $response->json('data'));
        $this->assertEquals(2, $response->json('last_page'));
        $this->assertEquals(12, $response->json('total'));
    }

    public function test_comments_belong_to_correct_task(): void
    {
        $task  = Task::factory()->create();
        $other = Task::factory()->create();
        TaskComment::factory(2)->create(['task_id' => $task->id]);
        TaskComment::factory(5)->create(['task_id' => $other->id]);

        $response = $this->getJson("/api/v1/tasks/{$task->id}/comments");

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_returns_empty_data_for_task_with_no_comments(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/{$task->id}/comments");

        $response->assertOk()->assertJson(['data' => [], 'total' => 0]);
    }

    // ─── STORE ────────────────────────────────────────────────────────────────

    public function test_can_create_comment(): void
    {
        $task = Task::factory()->create();

        $response = $this->postJson("/api/v1/tasks/{$task->id}/comments", [
            'body' => 'Primeiro comentário da task.',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['id', 'body', 'created_at', 'updated_at', 'edited'])
            ->assertJsonFragment(['body' => 'Primeiro comentário da task.']);

        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'body'    => 'Primeiro comentário da task.',
        ]);
    }

    public function test_store_fails_without_body(): void
    {
        $task = Task::factory()->create();

        $this->postJson("/api/v1/tasks/{$task->id}/comments", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }

    public function test_store_fails_with_empty_body(): void
    {
        $task = Task::factory()->create();

        $this->postJson("/api/v1/tasks/{$task->id}/comments", ['body' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }

    public function test_store_fails_with_body_exceeding_max_length(): void
    {
        $task = Task::factory()->create();

        $this->postJson("/api/v1/tasks/{$task->id}/comments", [
            'body' => str_repeat('a', 2001),
        ])->assertUnprocessable()->assertJsonValidationErrors(['body']);
    }

    public function test_store_accepts_body_at_max_length(): void
    {
        $task = Task::factory()->create();

        $this->postJson("/api/v1/tasks/{$task->id}/comments", [
            'body' => str_repeat('a', 2000),
        ])->assertCreated();
    }

    public function test_store_returns_404_for_nonexistent_task(): void
    {
        $this->postJson('/api/v1/tasks/99999/comments', ['body' => 'Olá'])
            ->assertNotFound();
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    public function test_can_update_comment(): void
    {
        $task    = Task::factory()->create();
        $comment = TaskComment::factory()->create(['task_id' => $task->id, 'body' => 'Original']);

        // Travel forward to ensure updated_at > created_at
        $this->travel(2)->seconds();

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/comments/{$comment->id}", [
            'body' => 'Editado',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['body' => 'Editado'])
            ->assertJsonFragment(['edited' => true]);

        $this->assertDatabaseHas('task_comments', ['id' => $comment->id, 'body' => 'Editado']);
    }

    public function test_update_fails_for_comment_of_different_task(): void
    {
        $task    = Task::factory()->create();
        $other   = Task::factory()->create();
        $comment = TaskComment::factory()->create(['task_id' => $other->id]);

        $this->patchJson("/api/v1/tasks/{$task->id}/comments/{$comment->id}", [
            'body' => 'Tentativa inválida',
        ])->assertNotFound();
    }

    public function test_update_fails_without_body(): void
    {
        $task    = Task::factory()->create();
        $comment = TaskComment::factory()->create(['task_id' => $task->id]);

        $this->patchJson("/api/v1/tasks/{$task->id}/comments/{$comment->id}", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function test_can_delete_comment(): void
    {
        $task    = Task::factory()->create();
        $comment = TaskComment::factory()->create(['task_id' => $task->id]);

        $this->deleteJson("/api/v1/tasks/{$task->id}/comments/{$comment->id}")
            ->assertOk()
            ->assertJson(['deleted' => true]);

        $this->assertDatabaseMissing('task_comments', ['id' => $comment->id]);
    }

    public function test_destroy_fails_for_comment_of_different_task(): void
    {
        $task    = Task::factory()->create();
        $other   = Task::factory()->create();
        $comment = TaskComment::factory()->create(['task_id' => $other->id]);

        $this->deleteJson("/api/v1/tasks/{$task->id}/comments/{$comment->id}")
            ->assertNotFound();
    }

    public function test_destroy_returns_404_for_nonexistent_comment(): void
    {
        $task = Task::factory()->create();

        $this->deleteJson("/api/v1/tasks/{$task->id}/comments/99999")
            ->assertNotFound();
    }
}
