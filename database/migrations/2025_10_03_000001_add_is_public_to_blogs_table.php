<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('blogs', 'is_public')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->boolean('is_public')->default(true)->after('image');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('blogs', 'is_public')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropColumn('is_public');
            });
        }
    }
};
