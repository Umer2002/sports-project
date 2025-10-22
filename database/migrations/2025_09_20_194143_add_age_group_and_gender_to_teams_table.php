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
            $table->foreignId('age_group_id')
                ->nullable()
                ->after('sport_id')
                ->constrained('age_groups')
                ->nullOnDelete();

            $table->foreignId('gender_id')
                ->nullable()
                ->after('age_group_id')
                ->constrained('genders')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gender_id');
            $table->dropConstrainedForeignId('age_group_id');
        });
    }
};
