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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('club_id')->nullable();
            $table->unsignedBigInteger('player_id')->nullable();
            $table->unsignedBigInteger('coach_id')->nullable();
            
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('set null');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('set null');
            $table->foreign('coach_id')->references('id')->on('coaches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropForeign(['player_id']);
            $table->dropForeign(['coach_id']);
            
            $table->dropColumn(['club_id', 'player_id', 'coach_id']);
        });
    }
};
