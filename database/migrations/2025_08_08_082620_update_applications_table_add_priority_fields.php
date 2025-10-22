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
        Schema::table('applications', function (Blueprint $table) {
            // Drop existing foreign key constraint if it exists
            $table->dropForeign(['game_id']);
            
            // Rename game_id to match_id
            $table->renameColumn('game_id', 'match_id');
            
            // Add new foreign key constraint for match_id
            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            
            // Add new fields
            $table->timestamp('applied_at')->nullable()->after('status');
            $table->integer('priority_score')->default(0)->after('applied_at');
            
            // Add indexes
            $table->index(['match_id', 'status']);
            $table->index(['referee_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['match_id', 'status']);
            $table->dropIndex(['referee_id', 'status']);
            
            // Drop new fields
            $table->dropColumn(['applied_at', 'priority_score']);
            
            // Drop foreign key constraint
            $table->dropForeign(['match_id']);
            
            // Rename back to game_id
            $table->renameColumn('match_id', 'game_id');
            
            // Restore original foreign key constraint
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });
    }
};
