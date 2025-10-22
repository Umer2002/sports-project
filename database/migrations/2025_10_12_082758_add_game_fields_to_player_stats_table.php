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
            $table->date('game_date')->nullable()->after('value');
            $table->string('game_location')->nullable()->after('game_date');
            $table->string('opponent_team')->nullable()->after('game_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_stats', function (Blueprint $table) {
            $table->dropColumn(['game_date', 'game_location', 'opponent_team']);
        });
    }
};