<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('users', 'coach_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['coach_id']);
                });
            } catch (\Throwable $e) {
                // Foreign key might not exist
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('coach_id');
            });
        }

        if (Schema::hasColumn('users', 'player_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['player_id']);
                });
            } catch (\Throwable $e) {}

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('player_id');
            });
        }

        if (Schema::hasColumn('users', 'club_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['club_id']);
                });
            } catch (\Throwable $e) {}

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('club_id');
            });
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'coach_id')) {
                $table->unsignedBigInteger('coach_id')->nullable();
                $table->foreign('coach_id')->references('id')->on('coaches');
            }

            if (!Schema::hasColumn('users', 'player_id')) {
                $table->unsignedBigInteger('player_id')->nullable();
                $table->foreign('player_id')->references('id')->on('players');
            }

            if (!Schema::hasColumn('users', 'club_id')) {
                $table->unsignedBigInteger('club_id')->nullable();
                $table->foreign('club_id')->references('id')->on('clubs');
            }
        });
    }
};
