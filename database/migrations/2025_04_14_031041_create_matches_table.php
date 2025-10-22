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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('home_club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('away_club_id')->nullable()->constrained('clubs')->onDelete('cascade');
            $table->date('match_date');
            $table->time('match_time');
            $table->string('venue');
            $table->json('score')->nullable(); // e.g. {"home": 2, "away": 1}
            $table->foreignId('referee_id')->nullable()->constrained('users');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
