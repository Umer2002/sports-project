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
        Schema::create('tournament_formats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->integer('games_per_team')->nullable();
            $table->integer('group_count')->nullable();
            $table->string('elimination_type')->nullable();
            $table->timestamps();
        });

        // Insert default tournament formats
        DB::table('tournament_formats')->insert([
            [
                'id' => 1,
                'name' => 'Round Robin',
                'description' => 'Every team plays every other team once',
                'type' => 'round_robin',
                'games_per_team' => null,
                'group_count' => 1,
                'elimination_type' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Knockout',
                'description' => 'Single elimination tournament',
                'type' => 'knockout',
                'games_per_team' => null,
                'group_count' => null,
                'elimination_type' => 'single',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Group Stage',
                'description' => 'Teams divided into groups for round robin play',
                'type' => 'group',
                'games_per_team' => null,
                'group_count' => 2,
                'elimination_type' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_formats');
    }
};