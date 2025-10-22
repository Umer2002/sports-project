<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            if (!Schema::hasColumn('clubs', 'stripe_public_key')) {
                $table->string('stripe_public_key')->nullable()->after('paypal_link');
            }
            if (!Schema::hasColumn('clubs', 'stripe_secret_key')) {
                $table->string('stripe_secret_key')->nullable()->after('stripe_public_key');
            }
            if (!Schema::hasColumn('clubs', 'stripe_account_id')) {
                $table->string('stripe_account_id')->nullable()->after('stripe_secret_key');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'club_id')) {
                $table->foreignId('club_id')->nullable()->constrained('clubs')->nullOnDelete()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'club_id')) {
                $table->dropConstrainedForeignId('club_id');
            }
        });
        Schema::table('clubs', function (Blueprint $table) {
            foreach (['stripe_account_id','stripe_secret_key','stripe_public_key'] as $col) {
                if (Schema::hasColumn('clubs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

