<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calendar_event_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('preferenceable_type');
            $table->unsignedBigInteger('preferenceable_id');
            $table->enum('attending_status', ['yes', 'maybe', 'no'])->nullable();
            $table->enum('carpool_status', ['driver', 'rider'])->nullable();
            $table->unsignedTinyInteger('seats_available')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('calendar_added_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'preferenceable_type', 'preferenceable_id'], 'calendar_pref_unique');
            $table->index(['preferenceable_type', 'preferenceable_id'], 'calendar_pref_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_event_preferences');
    }
};

