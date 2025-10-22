<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSportIdToTeamsTable extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('sport_id')->nullable()->after('club_id')->constrained('sports')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });
    }
}

