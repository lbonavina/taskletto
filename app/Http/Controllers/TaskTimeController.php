<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskTimeLog;
use Illuminate\Http\JsonResponse;

class TaskTimeController extends Controller
{
    /** Start a new timer. Stops any open log first. */
    public function start(Task $task): JsonResponse
    {
        // Close any open session for this task
        $task->timeLogs()->whereNull('ended_at')->update(['ended_at' => now()]);

        $log = $task->timeLogs()->create(['started_at' => now()]);

        return response()->json([
            'log_id'    => $log->id,
            'started_at' => $log->started_at->toIso8601String(),
            'tracked_seconds' => $task->fresh()->tracked_seconds,
        ]);
    }

    /** Stop the running timer and persist elapsed seconds. */
    public function stop(Task $task): JsonResponse
    {
        $log = $task->timeLogs()->whereNull('ended_at')->latest('started_at')->first();

        if (! $log) {
            return response()->json(['message' => 'No active timer.'], 422);
        }

        $log->update(['ended_at' => now()]);
        $elapsed = $log->durationSeconds();

        $task->increment('tracked_seconds', $elapsed);

        return response()->json([
            'elapsed_seconds'  => $elapsed,
            'tracked_seconds'  => $task->fresh()->tracked_seconds,
        ]);
    }

    /** Active timer state for this task. */
    public function status(Task $task): JsonResponse
    {
        $active = $task->timeLogs()->whereNull('ended_at')->latest('started_at')->first();

        return response()->json([
            'running'          => (bool) $active,
            'started_at'       => $active?->started_at->toIso8601String(),
            'tracked_seconds'  => $task->tracked_seconds,
        ]);
    }
}
