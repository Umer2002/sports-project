<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('help_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->string('role')->nullable();
            $table->string('role_label')->nullable();
            $table->string('intent')->nullable();
            $table->string('intent_label')->nullable();
            $table->string('intent_group')->nullable();
            $table->string('stage')->nullable();
            $table->string('status')->default('open');
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamps();
        });

        Schema::create('help_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('help_chat_sessions')->cascadeOnDelete();
            $table->enum('sender', ['user', 'assistant', 'system']);
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('help_chat_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('help_chat_sessions')->cascadeOnDelete();
            $table->string('ticket_number')->unique();
            $table->string('status')->default('open');
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('help_chat_tickets');
        Schema::dropIfExists('help_chat_messages');
        Schema::dropIfExists('help_chat_sessions');
    }
};
