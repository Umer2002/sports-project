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
        Schema::table('clubs', function (Blueprint $table) {
            // Payout tracking fields
            $table->timestamp('registration_date')->nullable()->after('is_registered');
            $table->integer('initial_player_count')->default(0)->after('registration_date');
            $table->integer('final_player_count')->nullable()->after('initial_player_count');
            $table->decimal('estimated_payout', 10, 2)->nullable()->after('final_player_count');
            $table->decimal('final_payout', 10, 2)->nullable()->after('estimated_payout');
            $table->timestamp('payout_calculated_at')->nullable()->after('final_payout');
            $table->timestamp('payout_paid_at')->nullable()->after('payout_calculated_at');
            $table->enum('payout_status', ['pending', 'calculated', 'paid'])->default('pending')->after('payout_paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn([
                'registration_date',
                'initial_player_count',
                'final_player_count',
                'estimated_payout',
                'final_payout',
                'payout_calculated_at',
                'payout_paid_at',
                'payout_status'
            ]);
        });
    }
}; 