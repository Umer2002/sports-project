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
        Schema::table('invites', function (Blueprint $table) {
            $table->boolean('payout_processed')->default(false)->after('accepted_at');
            $table->timestamp('payout_processed_at')->nullable()->after('payout_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invites', function (Blueprint $table) {
            $table->dropColumn(['payout_processed', 'payout_processed_at']);
        });
    }
};