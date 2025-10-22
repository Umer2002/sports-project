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
        // If video_type is ENUM
        DB::statement("ALTER TABLE videos MODIFY video_type ENUM('training','match','tutorial','skill') NOT NULL");

        // If video_type is VARCHAR and just too short, use this instead:
        // Schema::table('videos', function (Blueprint $table) {
        //     $table->string('video_type', 50)->change();
        // });
    }

    public function down(): void
    {
        // Rollback ENUM without 'skill'
        DB::statement("ALTER TABLE videos MODIFY video_type ENUM('training','match','tutorial') NOT NULL");

        // Or rollback VARCHAR size if you used that path:
        // Schema::table('videos', function (Blueprint $table) {
        //     $table->string('video_type', 20)->change();
        // });
    }
};
