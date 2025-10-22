<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop legacy city column(s)
        if (Schema::hasColumn('coaches', 'city')) {
            Schema::table('coaches', function (Blueprint $table) {
                $table->dropColumn('city');
            });
        }
        if (Schema::hasColumn('coaches', 'city_name')) {
            Schema::table('coaches', function (Blueprint $table) {
                $table->dropColumn('city_name');
            });
        }

        // Drop legacy country string column(s)
        if (Schema::hasColumn('coaches', 'country_code')) {
            Schema::table('coaches', function (Blueprint $table) {
                $table->dropColumn('country_code');
            });
        }

        // If country_id exists but is a string (legacy), drop it;
        // if it's a bigint FK, keep it.
        if (Schema::hasColumn('coaches', 'country_id')) {
            try {
                $type = Schema::getColumnType('coaches', 'country_id'); // requires doctrine/dbal
            } catch (\Throwable $e) {
                $type = null;
            }

            if ($type === 'string' || $type === 'text') {
                Schema::table('coaches', function (Blueprint $table) {
                    $table->dropColumn('country_id'); // legacy string version
                });
            }
        }
    }

    public function down(): void
    {
        // Recreate legacy columns as nullable (to avoid clashing with FK names)
        Schema::table('coaches', function (Blueprint $table) {
            if (!Schema::hasColumn('coaches', 'city')) {
                $table->string('city', 191)->nullable()->after('socail_links');
            }
            // Use country_code to avoid name clash with FK country_id
            if (!Schema::hasColumn('coaches', 'country_code')) {
                $table->string('country_code', 191)->nullable()->after('bio');
            }
        });
    }
};
