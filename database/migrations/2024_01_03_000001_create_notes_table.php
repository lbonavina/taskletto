<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->default('Sem título');
            $table->longText('content')->nullable();
            $table->string('color', 7)->default('#ff914d');
            $table->boolean('pinned')->default(false)->index();
            $table->string('category', 100)->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('notes'); }
};
