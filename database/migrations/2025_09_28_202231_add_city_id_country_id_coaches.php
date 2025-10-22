<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coaches', function (Blueprint $table) {
            // add after your renamed columns for nicer ordering
            if (!Schema::hasColumn('coaches', 'country_id')) {
                $table->foreignId('country_id')->nullable()
                      ->after('country_code')
                      ->constrained('countries')->nullOnDelete();
            }

            if (!Schema::hasColumn('coaches', 'state_id')) {
                $table->foreignId('state_id')->nullable()
                      ->after('country_id')
                      ->constrained('states')->nullOnDelete();
            }

            if (!Schema::hasColumn('coaches', 'city_id')) {
                $table->foreignId('city_id')->nullable()
                      ->after('state_id')
                      ->constrained('cities')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('coaches', function (Blueprint $table) {
            if (Schema::hasColumn('coaches', 'city_id')) {
                $table->dropConstrainedForeignId('city_id');
            }
            if (Schema::hasColumn('coaches', 'state_id')) {
                $table->dropConstrainedForeignId('state_id');
            }
            if (Schema::hasColumn('coaches', 'country_id')) {
                $table->dropConstrainedForeignId('country_id');
            }
        });
    }
};
