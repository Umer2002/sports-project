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
        Schema::table('player_award', function (Blueprint $table) {
            $table->string('award_image')->nullable()->after('add_to_profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_award', function (Blueprint $table) {
            $table->dropColumn('award_image');
        });
    }
};
