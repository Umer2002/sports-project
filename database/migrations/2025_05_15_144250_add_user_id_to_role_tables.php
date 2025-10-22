<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add user_id to clubs
        Schema::table('clubs', function (Blueprint $table) {
            if (!Schema::hasColumn('clubs', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }
        });

        // Add user_id to players
        Schema::table('players', function (Blueprint $table) {
            if (!Schema::hasColumn('players', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }
        });

        // Add user_id to coaches
        Schema::table('coaches', function (Blueprint $table) {
            if (!Schema::hasColumn('coaches', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }
        });

        // Add user_id to referees
        Schema::table('referees', function (Blueprint $table) {
            if (!Schema::hasColumn('referees', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('coaches', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('referees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
