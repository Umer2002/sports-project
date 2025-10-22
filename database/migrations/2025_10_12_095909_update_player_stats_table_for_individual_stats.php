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
        Schema::table('player_stats', function (Blueprint $table) {
            // Drop existing columns that we don't need
            $table->dropForeign(['stat_id']);
            $table->dropColumn(['stat_id', 'value', 'game_date', 'game_location', 'opponent_team']);
            
            // Add new columns for individual player stats
            $table->string('stat1')->nullable()->after('player_id');
            $table->string('stat2')->nullable()->after('stat1');
            $table->string('stat3')->nullable()->after('stat2');
            $table->string('stat4')->nullable()->after('stat3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_stats', function (Blueprint $table) {
            // Add back the old columns
            $table->foreignId('stat_id')->nullable()->constrained('stats')->onDelete('cascade');
            $table->string('value')->nullable();
            $table->date('game_date')->nullable();
            $table->string('game_location')->nullable();
            $table->string('opponent_team')->nullable();
            
            // Drop the new columns
            $table->dropColumn(['stat1', 'stat2', 'stat3', 'stat4']);
        });
    }
};