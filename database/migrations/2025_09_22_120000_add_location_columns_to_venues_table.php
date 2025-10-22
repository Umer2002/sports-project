<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('club_id')->constrained()->nullOnDelete();
            $table->foreignId('state_id')->nullable()->after('country_id')->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('state_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('state_id');
            $table->dropConstrainedForeignId('country_id');
        });
    }
};
