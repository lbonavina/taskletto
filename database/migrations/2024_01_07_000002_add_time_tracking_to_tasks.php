<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedInteger('estimated_minutes')->nullable()->after('recurrence_ends_at');
            $table->unsignedBigInteger('tracked_seconds')->default(0)->after('estimated_minutes');
        });

        Schema::create('task_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_time_logs');
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['estimated_minutes', 'tracked_seconds']);
        });
    }
};
