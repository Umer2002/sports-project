<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Make the column nullable
        Schema::table('coaches', function (Blueprint $table) {
            $table->text('socail_links')->nullable()->change();
        });

        // 2) Normalize any empty strings to NULL (optional but tidy)
        DB::table('coaches')
            ->where('socail_links', '')
            ->update(['socail_links' => null]);
    }

    public function down(): void
    {
        // Revert: set NULLs back to empty string, then make NOT NULL again
        DB::table('coaches')
            ->whereNull('socail_links')
            ->update(['socail_links' => '']);

        Schema::table('coaches', function (Blueprint $table) {
            $table->text('socail_links')->nullable(false)->change();
        });
    }
};
