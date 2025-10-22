<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('clubs', 'user_id')) {
            return;
        }

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE clubs MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('clubs', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('clubs', 'user_id')) {
            return;
        }

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::table('clubs')->whereNull('user_id')->delete();

        DB::statement('ALTER TABLE clubs MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('clubs', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
