<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('note_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained()->cascadeOnDelete();
            $table->string('token', 32)->unique();
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->json('allowed_emails')->nullable(); // only for private
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('note_shares');
    }
};
