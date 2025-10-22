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
        Schema::table('pickup_games', function (Blueprint $table) {
            $table->foreignId('referee_id')->nullable()->constrained('referees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_games', function (Blueprint $table) {
            $table->dropForeign(['referee_id']);
            $table->dropColumn('referee_id');
        });
    }
};