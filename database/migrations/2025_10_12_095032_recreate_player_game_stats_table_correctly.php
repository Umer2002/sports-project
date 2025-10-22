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
        // Drop the existing table
        Schema::dropIfExists('player_game_stats');
        
        // Create the correct table structure
        Schema::create('player_game_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained('sports')->onDelete('cascade');
            $table->string('stat1')->nullable();
            $table->string('stat2')->nullable();
            $table->string('stat3')->nullable();
            $table->string('stat4')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_game_stats');
    }
};