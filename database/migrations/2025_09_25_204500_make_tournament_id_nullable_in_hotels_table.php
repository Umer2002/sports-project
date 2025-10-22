<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'tournament_id')) {
                $table->dropForeign(['tournament_id']);
            }
        });

        DB::statement('ALTER TABLE hotels MODIFY tournament_id BIGINT UNSIGNED NULL');

        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'tournament_id')) {
                $table->foreign('tournament_id')
                    ->references('id')
                    ->on('tournaments')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'tournament_id')) {
                $table->dropForeign(['tournament_id']);
            }
        });

        DB::statement('ALTER TABLE hotels MODIFY tournament_id BIGINT UNSIGNED NOT NULL');

        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'tournament_id')) {
                $table->foreign('tournament_id')
                    ->references('id')
                    ->on('tournaments')
                    ->onDelete('cascade');
            }
        });
    }
};
