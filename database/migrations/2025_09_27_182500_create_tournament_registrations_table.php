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
        Schema::create('tournament_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->foreignId('club_invite_id')->nullable()->constrained('club_invites')->nullOnDelete();
            $table->string('status', 40)->default('pending_payment');
            $table->string('joining_type', 20)->default('per_club');
            $table->decimal('joining_fee', 10, 2)->default(0);
            $table->unsignedInteger('team_quantity')->default(1);
            $table->decimal('amount_due', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['tournament_id', 'club_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_registrations');
    }
};
