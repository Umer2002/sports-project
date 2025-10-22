<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ads') || !Schema::hasColumn('ads', 'type')) {
            return;
        }

        // Remove legacy YouTube references so admins can re-upload local files
        DB::table('ads')
            ->where('type', 'youtube')
            ->update([
                'type' => 'video',
                'media' => null,
                'active' => false,
            ]);

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE ads MODIFY COLUMN type ENUM('image','video') NOT NULL DEFAULT 'image'");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE ads DROP CONSTRAINT IF EXISTS ads_type_check');
            DB::statement("ALTER TABLE ads ALTER COLUMN type TYPE TEXT");
            DB::statement("ALTER TABLE ads ALTER COLUMN type SET DEFAULT 'image'");
            DB::statement("ALTER TABLE ads ADD CONSTRAINT ads_type_check CHECK (type IN ('image','video'))");
        } elseif ($driver !== 'sqlite') {
            // For other drivers, map enum to a constrained string
            Schema::table('ads', function (Blueprint $table) {
                $table->string('type', 16)->default('image')->change();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('ads') || !Schema::hasColumn('ads', 'type')) {
            return;
        }

        DB::table('ads')
            ->where('type', 'video')
            ->update([
                'type' => 'youtube',
                'active' => false,
            ]);

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE ads MODIFY COLUMN type ENUM('image','youtube') NOT NULL DEFAULT 'image'");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE ads DROP CONSTRAINT IF EXISTS ads_type_check');
            DB::statement("ALTER TABLE ads ALTER COLUMN type TYPE TEXT");
            DB::statement("ALTER TABLE ads ALTER COLUMN type SET DEFAULT 'image'");
            DB::statement("ALTER TABLE ads ADD CONSTRAINT ads_type_check CHECK (type IN ('image','youtube'))");
        } elseif ($driver !== 'sqlite') {
            Schema::table('ads', function (Blueprint $table) {
                $table->string('type', 16)->default('image')->change();
            });
        }
    }
};
