<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'venue_id')) {
                $table->foreignId('venue_id')
                      ->nullable()
                      ->after('tournament_id')
                      ->constrained()
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'venue_id')) {
                $table->dropForeign(['venue_id']);
                $table->dropColumn('venue_id');
            }
        });
    }
};
