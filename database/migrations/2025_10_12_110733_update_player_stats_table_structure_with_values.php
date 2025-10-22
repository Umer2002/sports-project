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
        Schema::table('player_stats', function (Blueprint $table) {
            // Add stat value columns (note: there's a typo in the original - "vlaue" instead of "value")
            $table->string('stat1_vlaue')->nullable()->after('stat4');
            $table->string('stat2_vlaue')->nullable()->after('stat1_vlaue');
            $table->string('stat3_vlaue')->nullable()->after('stat2_vlaue');
            $table->string('stat4_vlaue')->nullable()->after('stat3_vlaue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_stats', function (Blueprint $table) {
            $table->dropColumn(['stat1_vlaue', 'stat2_vlaue', 'stat3_vlaue', 'stat4_vlaue']);
        });
    }
};