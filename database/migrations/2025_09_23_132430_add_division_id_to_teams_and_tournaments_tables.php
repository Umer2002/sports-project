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
        Schema::table('teams', function (Blueprint $table) {
            if (! Schema::hasColumn('teams', 'division_id')) {
                $table->foreignId('division_id')
                    ->nullable()
                    ->after('gender_id')
                    ->constrained('divisions')
                    ->nullOnDelete();
            }
        });

        Schema::table('tournaments', function (Blueprint $table) {
            if (! Schema::hasColumn('tournaments', 'division_id')) {
                $table->foreignId('division_id')
                    ->nullable()
                    ->after('tournament_format_id')
                    ->constrained('divisions')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (Schema::hasColumn('teams', 'division_id')) {
                $table->dropConstrainedForeignId('division_id');
            }
        });

        Schema::table('tournaments', function (Blueprint $table) {
            if (Schema::hasColumn('tournaments', 'division_id')) {
                $table->dropConstrainedForeignId('division_id');
            }
        });
    }
};
