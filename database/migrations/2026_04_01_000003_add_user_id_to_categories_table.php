<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Remove global unique constraint — names are now unique per user
            $table->dropUnique(['name']);

            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->index('user_id');
            $table->unique(['user_id', 'name']);
        });

        if (DB::table('users')->where('id', 1)->exists()) {
            DB::table('categories')->whereNull('user_id')->update(['user_id' => 1]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'name']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
            $table->unique('name');
        });
    }
};
