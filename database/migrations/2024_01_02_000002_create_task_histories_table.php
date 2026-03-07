<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('task_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->string('field');        // campo alterado
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('label');        // descrição humana da mudança
            $table->timestamp('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('task_histories'); }
};
