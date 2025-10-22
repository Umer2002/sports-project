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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // Assuming a conversation/chat exists
            $table->unsignedBigInteger('chat_id');

            // Both sender and receiver IDs to track who is communicating.
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');

            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Foreign key constraints:
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
