<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['yes', 'no', 'maybe'])->default('maybe');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_responses');
    }
};
