<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (! Schema::hasColumn('tournaments', 'gender_id')) {
                $table->foreignId('gender_id')
                    ->nullable()
                    ->after('division_id')
                    ->constrained('genders')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('tournaments', 'age_group_id')) {
                $table->foreignId('age_group_id')
                    ->nullable()
                    ->after('gender_id')
                    ->constrained('age_groups')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (Schema::hasColumn('tournaments', 'age_group_id')) {
                $table->dropForeign(['age_group_id']);
                $table->dropColumn('age_group_id');
            }

            if (Schema::hasColumn('tournaments', 'gender_id')) {
                $table->dropForeign(['gender_id']);
                $table->dropColumn('gender_id');
            }
        });
    }
};
