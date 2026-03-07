<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('sort_order')->default(0)->after('id');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->after('category');
        });
    }
    public function down(): void {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'category_id']);
        });
    }
};
