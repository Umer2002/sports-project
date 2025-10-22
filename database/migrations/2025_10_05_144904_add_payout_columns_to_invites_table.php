<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invites', function (Blueprint $table) {
            // Add only if missing (safe to run on any env)
            if (!Schema::hasColumn('invites', 'payout_processed')) {
                $table->boolean('payout_processed')->default(false)->after('accepted_at')->index();
            }
            if (!Schema::hasColumn('invites', 'payout_processed_at')) {
                $table->timestamp('payout_processed_at')->nullable()->after('payout_processed')->index();
            }
            if (!Schema::hasColumn('invites', 'payout_batch_id')) {
                $table->string('payout_batch_id', 191)->nullable()->after('payout_processed_at')->index();
            }
            // (optional) helpful index if you do a lot of “ready” queries
            if (!Schema::hasColumn('invites', 'accepted_at')) {
                // If you *don’t* have accepted_at at all, add it:
                $table->timestamp('accepted_at')->nullable()->after('receiver_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('invites', function (Blueprint $table) {
            if (Schema::hasColumn('invites', 'payout_batch_id')) {
                $table->dropColumn('payout_batch_id');
            }
            if (Schema::hasColumn('invites', 'payout_processed_at')) {
                $table->dropColumn('payout_processed_at');
            }
            if (Schema::hasColumn('invites', 'payout_processed')) {
                $table->dropColumn('payout_processed');
            }
            // Do not drop accepted_at if it predates this migration
        });
    }
};
