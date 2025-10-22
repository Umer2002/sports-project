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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->string('stripe_session_id')->nullable();
            $table->integer('amount'); // Amount in cents
            $table->string('currency')->default('usd');
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
