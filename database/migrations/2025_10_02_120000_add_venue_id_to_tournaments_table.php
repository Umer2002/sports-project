<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (! Schema::hasColumn('tournaments', 'venue_id')) {
                $table->foreignId('venue_id')
                    ->nullable()
                    ->after('city_id')
                    ->constrained('venues')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (Schema::hasColumn('tournaments', 'venue_id')) {
                $table->dropConstrainedForeignId('venue_id');
            }
        });
    }
};
