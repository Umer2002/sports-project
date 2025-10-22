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
        Schema::create('referee_match_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referee_id')->constrained('referees')->cascadeOnDelete();
            $table->string('title');
            $table->string('opponent')->nullable();
            $table->string('match_type')->nullable();
            $table->enum('home_away', ['home', 'away'])->nullable();
            $table->date('event_date');
            $table->time('kickoff_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('repeat_frequency')->nullable();
            $table->unsignedInteger('repeat_interval')->nullable();
            $table->unsignedInteger('repeat_occurrences')->nullable();
            $table->json('repeat_days')->nullable();
            $table->date('repeat_ends_at')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('venue_address')->nullable();
            $table->string('notification_lead_times')->nullable();
            $table->string('color')->default('#38bdf8');
            $table->json('roster')->nullable();
            $table->string('attachment_path')->nullable();
            $table->boolean('is_draft')->default(false);
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referee_match_events');
    }
};
