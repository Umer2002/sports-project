<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sender_id'); // inviter
            $table->unsignedBigInteger('receiver_id')->nullable(); // may not exist yet
            $table->string('receiver_email'); // store email even if no user exists yet

            $table->string('type'); // event, club, game
            $table->unsignedBigInteger('reference_id'); // target ID

            $table->boolean('is_accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();

            $table->timestamps();

            $table->index(['type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
