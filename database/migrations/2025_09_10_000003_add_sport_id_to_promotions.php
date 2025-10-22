<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            if (!Schema::hasColumn('promotions', 'sport_id')) {
                $table->foreignId('sport_id')->nullable()->constrained('sports')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            if (Schema::hasColumn('promotions', 'sport_id')) {
                $table->dropConstrainedForeignId('sport_id');
            }
        });
    }
};

