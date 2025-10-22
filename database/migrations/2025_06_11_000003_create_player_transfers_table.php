<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('from_club_id')->nullable();
            $table->unsignedBigInteger('to_club_id')->nullable();
            $table->unsignedBigInteger('from_sport_id')->nullable();
            $table->unsignedBigInteger('to_sport_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('from_club_id')->references('id')->on('clubs')->nullOnDelete();
            $table->foreign('to_club_id')->references('id')->on('clubs')->nullOnDelete();
            $table->foreign('from_sport_id')->references('id')->on('sports')->nullOnDelete();
            $table->foreign('to_sport_id')->references('id')->on('sports')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_transfers');
    }
};
