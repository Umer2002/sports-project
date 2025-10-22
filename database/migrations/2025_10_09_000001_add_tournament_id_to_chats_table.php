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
        Schema::table('chats', function (Blueprint $table) {
            if (!Schema::hasColumn('chats', 'tournament_id')) {
                $table->foreignId('tournament_id')
                    ->nullable()
                    ->after('team_id')
                    ->constrained()
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            if (Schema::hasColumn('chats', 'tournament_id')) {
                $table->dropConstrainedForeignId('tournament_id');
            }
        });
    }
};
