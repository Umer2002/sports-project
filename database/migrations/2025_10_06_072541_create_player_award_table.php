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
        Schema::create('player_award', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('award_id')->constrained('awards')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade'); // Coach who assigned the award
            $table->timestamp('awarded_at');
            $table->enum('visibility', ['public', 'team', 'private'])->default('public');
            $table->text('coach_note')->nullable();
            $table->boolean('notify_player')->default(true);
            $table->boolean('post_to_feed')->default(false);
            $table->boolean('add_to_profile')->default(true);
            $table->timestamps();

            $table->unique(['player_id', 'award_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_award');
    }
};
