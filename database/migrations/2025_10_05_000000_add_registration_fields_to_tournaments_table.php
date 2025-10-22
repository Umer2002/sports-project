<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (! Schema::hasColumn('tournaments', 'registration_cutoff_date')) {
                $table->date('registration_cutoff_date')
                    ->nullable()
                    ->after('end_date');
            }

            if (! Schema::hasColumn('tournaments', 'joining_fee')) {
                $table->decimal('joining_fee', 10, 2)
                    ->default(0)
                    ->after('registration_cutoff_date');
            }

            if (! Schema::hasColumn('tournaments', 'joining_type')) {
                $table->string('joining_type', 20)
                    ->default('per_club')
                    ->after('joining_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (Schema::hasColumn('tournaments', 'joining_type')) {
                $table->dropColumn('joining_type');
            }

            if (Schema::hasColumn('tournaments', 'joining_fee')) {
                $table->dropColumn('joining_fee');
            }

            if (Schema::hasColumn('tournaments', 'registration_cutoff_date')) {
                $table->dropColumn('registration_cutoff_date');
            }
        });
    }
};
