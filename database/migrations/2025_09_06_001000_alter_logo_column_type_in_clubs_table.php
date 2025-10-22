<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to avoid requiring doctrine/dbal for column type changes
        DB::statement('ALTER TABLE clubs MODIFY logo TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE clubs MODIFY logo VARCHAR(191) NULL');
    }
};

