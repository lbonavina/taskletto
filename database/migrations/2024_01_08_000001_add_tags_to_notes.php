<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('notes', function (Blueprint $table) {
            $table->string('tags', 500)->nullable()->after('category')
                ->comment('Comma-separated tags, e.g. "trabalho,ideia,urgente"');
        });
    }

    public function down(): void {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
};
