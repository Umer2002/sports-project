<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('college')->nullable()->after('email');
            $table->string('university')->nullable()->after('college');
            $table->string('referee_affiliation')->nullable()->after('university');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['college', 'university', 'referee_affiliation']);
        });
    }
};
