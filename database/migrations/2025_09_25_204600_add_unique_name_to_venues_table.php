<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('venues', 'name')) {
            return;
        }

        Schema::table('venues', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('venues', 'name')) {
            return;
        }

        Schema::table('venues', function (Blueprint $table) {
            $table->dropUnique('venues_name_unique');
        });
    }
};
