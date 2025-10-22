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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('club_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('stripe_session_id')->nullable();
            $table->integer('amount');
            $table->string('currency');
            $table->enum('type', ['player', 'club_payout'])->default('player');
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};