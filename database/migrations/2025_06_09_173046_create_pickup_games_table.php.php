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
        Schema::create('pickup_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sport_id');
            $table->timestamp('game_datetime');
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('max_players');
            $table->enum('privacy', ['public', 'private']);
            $table->decimal('join_fee', 8, 2)->nullable();
            $table->unsignedBigInteger('host_id');
            $table->timestamps();

            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->foreign('host_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_games');
    }
};
