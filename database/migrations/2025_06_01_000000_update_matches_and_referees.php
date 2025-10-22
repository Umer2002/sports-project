<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            if (!Schema::hasColumn('matches', 'age_group')) {
                $table->string('age_group')->nullable()->after('away_club_id');
            }
            if (!Schema::hasColumn('matches', 'required_referee_level')) {
                $table->unsignedInteger('required_referee_level')->default(0)->after('age_group');
            }
        });
        Schema::table('referees', function (Blueprint $table) {
            if (!Schema::hasColumn('referees', 'certification_level')) {
                $table->unsignedInteger('certification_level')->default(0)->after('license_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            if (Schema::hasColumn('matches', 'required_referee_level')) {
                $table->dropColumn('required_referee_level');
            }
            if (Schema::hasColumn('matches', 'age_group')) {
                $table->dropColumn('age_group');
            }
        });
        Schema::table('referees', function (Blueprint $table) {
            if (Schema::hasColumn('referees', 'certification_level')) {
                $table->dropColumn('certification_level');
            }
        });
    }
};
