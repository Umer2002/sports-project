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
    Schema::table('players', function (Blueprint $table) {
        if (!Schema::hasColumn('players', 'address')) {
            $table->string('address')->nullable();
        }
        if (!Schema::hasColumn('players', 'city')) {
            $table->string('city')->nullable();
        }
        if (!Schema::hasColumn('players', 'state')) {
            $table->string('state')->nullable();
        }
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            //
        });
    }
};
