<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('paypal_link')->nullable();
            $table->date('birthday')->nullable();

            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->date('debut')->nullable();
            $table->integer('jersey_no')->nullable();
            $table->foreignId('club_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('sport_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('cascade');

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('team_id')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        // Pivot table for player stats
        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('stat_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('player_stats');
        Schema::dropIfExists('players');
    }
};
