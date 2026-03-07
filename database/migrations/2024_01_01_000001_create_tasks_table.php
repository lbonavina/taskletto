<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title', 255);
            $table->text('description')->nullable();

            $table->enum('status', TaskStatus::values())
                  ->default(TaskStatus::Pending->value)
                  ->index();

            $table->enum('priority', TaskPriority::values())
                  ->default(TaskPriority::Medium->value)
                  ->index();

            $table->string('category', 100)->nullable()->index();

            $table->date('due_date')->nullable()->index();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
