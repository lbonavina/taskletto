<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Migrate existing category strings to category_id
        $tasks = DB::table('tasks')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->get(['id', 'category']);

        foreach ($tasks as $task) {
            // Find or create the category
            $categoryId = DB::table('categories')
                ->where('name', $task->category)
                ->value('id');

            if (!$categoryId) {
                // Create the category if it doesn't exist
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => $task->category,
                    'color' => '#6366f1',
                    'icon' => 'tag',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update the task with category_id
            DB::table('tasks')
                ->where('id', $task->id)
                ->update(['category_id' => $categoryId]);
        }

        // Drop the index first, then the column
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['category']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        // Re-add the category column
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('category', 100)->nullable()->after('priority');
            $table->index('category');
        });

        // Migrate back from category_id to category string
        $tasks = DB::table('tasks')
            ->whereNotNull('category_id')
            ->get(['id', 'category_id']);

        foreach ($tasks as $task) {
            $categoryName = DB::table('categories')
                ->where('id', $task->category_id)
                ->value('name');

            if ($categoryName) {
                DB::table('tasks')
                    ->where('id', $task->id)
                    ->update(['category' => $categoryName]);
            }
        }
    }
};
