<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->unsignedBigInteger('club_id')->nullable()->change();
            $table->unsignedBigInteger('player_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->unsignedBigInteger('club_id')->nullable(false)->change();
            $table->unsignedBigInteger('player_id')->nullable(false)->change();
        });
    }

};
