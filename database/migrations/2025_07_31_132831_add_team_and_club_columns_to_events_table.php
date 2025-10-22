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
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('club_id')->nullable()->after('team_id');
            
            // Add foreign key constraints
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['club_id']);
            $table->dropColumn(['team_id', 'club_id']);
        });
    }
};
