<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamPlayerTable extends Migration
{
    public function up()
    {
        Schema::create('team_player', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->nullable()->constrained('positions')->onDelete('set null');
            $table->string('role')->nullable(); // e.g., Captain, Substitute
            $table->unsignedInteger('goals')->default(0);
            $table->unsignedInteger('assists')->default(0);
            $table->unsignedInteger('matches')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_player');
    }
}

