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
        Schema::table('player_transfers', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('to_sport_id');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->text('notes')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_transfers', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejected_at', 'notes']);
        });
    }
};
