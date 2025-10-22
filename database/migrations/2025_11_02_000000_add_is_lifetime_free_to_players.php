<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            if (! Schema::hasColumn('players', 'is_lifetime_free')) {
                $table->boolean('is_lifetime_free')
                    ->default(false)
                    ->after('paypal_link');
            }

            if (! Schema::hasColumn('players', 'lifetime_free_granted_at')) {
                $table->timestamp('lifetime_free_granted_at')
                    ->nullable()
                    ->after('is_lifetime_free');
            }
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            if (Schema::hasColumn('players', 'lifetime_free_granted_at')) {
                $table->dropColumn('lifetime_free_granted_at');
            }

            if (Schema::hasColumn('players', 'is_lifetime_free')) {
                $table->dropColumn('is_lifetime_free');
            }
        });
    }
};
