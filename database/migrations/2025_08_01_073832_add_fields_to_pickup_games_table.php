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
            $table->string('title')->after('sport_id');
            $table->text('description')->nullable()->after('title');
            $table->enum('skill_level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('all_levels')->after('description');
            $table->text('equipment_needed')->nullable()->after('skill_level');
            $table->string('share_link')->nullable()->after('equipment_needed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_games', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'skill_level', 'equipment_needed', 'share_link']);
        });
    }
};
