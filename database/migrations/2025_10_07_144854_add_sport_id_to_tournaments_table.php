<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->unsignedBigInteger('sport_id')->nullable()->after('host_club_id');
            $table->foreign('sport_id')->references('id')->on('sports')->nullOnDelete();
            $table->index('sport_id');
        });

        // Optional backfill from host club’s sport_id if your schema supports it
        if (Schema::hasColumn('clubs', 'sport_id')) {
            DB::statement("
                UPDATE tournaments t
                JOIN clubs c ON c.id = t.host_club_id
                SET t.sport_id = c.sport_id
                WHERE t.sport_id IS NULL
            ");
        }
    }

    public function down(): void {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });
    }
};
