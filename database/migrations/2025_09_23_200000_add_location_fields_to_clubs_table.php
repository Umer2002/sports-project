<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('clubs', 'country_id')) {
            Schema::table('clubs', function (Blueprint $table) {
                $table->dropColumn('country_id');
            });
        }

        Schema::table('clubs', function (Blueprint $table) {
            if (!Schema::hasColumn('clubs', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('sport_id');
            }
            if (!Schema::hasColumn('clubs', 'state_id')) {
                $table->foreignId('state_id')->nullable()->after('country_id')->constrained('states')->nullOnDelete();
            }
            if (!Schema::hasColumn('clubs', 'city_id')) {
                $table->foreignId('city_id')->nullable()->after('state_id')->constrained('cities')->nullOnDelete();
            }
        });

        Schema::table('clubs', function (Blueprint $table) {
            if (Schema::hasColumn('clubs', 'country_id')) {
                $table->foreign('country_id')
                    ->references('id')
                    ->on('countries')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            if (Schema::hasColumn('clubs', 'country_id')) {
                $table->dropForeign(['country_id']);
            }
            if (Schema::hasColumn('clubs', 'state_id')) {
                $table->dropConstrainedForeignId('state_id');
            }
            if (Schema::hasColumn('clubs', 'city_id')) {
                $table->dropConstrainedForeignId('city_id');
            }
            foreach (['city_id', 'state_id', 'country_id'] as $column) {
                if (Schema::hasColumn('clubs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
