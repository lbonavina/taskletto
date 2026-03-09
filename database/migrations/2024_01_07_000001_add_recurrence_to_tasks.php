<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('recurrence', ['none', 'daily', 'weekly', 'monthly'])
                  ->default('none')
                  ->after('completed_at');
            $table->date('recurrence_ends_at')->nullable()->after('recurrence');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['recurrence', 'recurrence_ends_at']);
        });
    }
};
