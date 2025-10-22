<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_game_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pickup_game_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('attendance_confirmed')->default(false);
            $table->tinyInteger('rating')->nullable();
            $table->timestamps();

            $table->foreign('pickup_game_id')->references('id')->on('pickup_games')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['pickup_game_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_game_participants');
    }
};
