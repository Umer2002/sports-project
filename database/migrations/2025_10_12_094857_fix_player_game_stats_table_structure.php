<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('player_game_stats', function (Blueprint $table) {
            // Drop the game_id foreign key and column
            $table->dropForeign(['game_id']);
            $table->dropColumn('game_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_game_stats', function (Blueprint $table) {
            // Add back game_id if needed
            $table->foreignId('game_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};