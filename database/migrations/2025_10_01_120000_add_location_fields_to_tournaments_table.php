<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('host_club_id')->constrained('countries')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->after('country_id')->constrained('states')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('state_id')->constrained('cities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);

            $table->dropColumn(['country_id', 'state_id', 'city_id']);
        });
    }
};
