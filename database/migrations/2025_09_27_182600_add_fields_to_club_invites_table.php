<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('club_invites', function (Blueprint $table) {
            if (! Schema::hasColumn('club_invites', 'token')) {
                $table->uuid('token')->unique()->after('id');
            }

            if (! Schema::hasColumn('club_invites', 'inviter_club_id')) {
                $table->foreignId('inviter_club_id')->nullable()->after('tournament_id')
                    ->constrained('clubs')->nullOnDelete();
            }

            if (! Schema::hasColumn('club_invites', 'registered_club_id')) {
                $table->foreignId('registered_club_id')->nullable()->after('status')
                    ->constrained('clubs')->nullOnDelete();
            }

            if (! Schema::hasColumn('club_invites', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('email');
            }

            if (! Schema::hasColumn('club_invites', 'registered_at')) {
                $table->timestamp('registered_at')->nullable()->after('accepted_at');
            }

            if (! Schema::hasColumn('club_invites', 'reward_amount')) {
                $table->decimal('reward_amount', 10, 2)->default(0)->after('registered_club_id');
            }

            if (! Schema::hasColumn('club_invites', 'reward_status')) {
                $table->string('reward_status', 30)->default('pending')->after('reward_amount');
            }

            if (! Schema::hasColumn('club_invites', 'reward_payout_scheduled_at')) {
                $table->timestamp('reward_payout_scheduled_at')->nullable()->after('reward_status');
            }

            if (! Schema::hasColumn('club_invites', 'reward_paid_at')) {
                $table->timestamp('reward_paid_at')->nullable()->after('reward_payout_scheduled_at');
            }

            if (! Schema::hasColumn('club_invites', 'notes')) {
                $table->text('notes')->nullable()->after('reward_paid_at');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_invites', function (Blueprint $table) {
            if (Schema::hasColumn('club_invites', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('club_invites', 'reward_paid_at')) {
                $table->dropColumn('reward_paid_at');
            }

            if (Schema::hasColumn('club_invites', 'reward_payout_scheduled_at')) {
                $table->dropColumn('reward_payout_scheduled_at');
            }

            if (Schema::hasColumn('club_invites', 'reward_status')) {
                $table->dropColumn('reward_status');
            }

            if (Schema::hasColumn('club_invites', 'reward_amount')) {
                $table->dropColumn('reward_amount');
            }

            if (Schema::hasColumn('club_invites', 'registered_at')) {
                $table->dropColumn('registered_at');
            }

            if (Schema::hasColumn('club_invites', 'accepted_at')) {
                $table->dropColumn('accepted_at');
            }

            if (Schema::hasColumn('club_invites', 'registered_club_id')) {
                $table->dropConstrainedForeignId('registered_club_id');
            }

            if (Schema::hasColumn('club_invites', 'inviter_club_id')) {
                $table->dropConstrainedForeignId('inviter_club_id');
            }

            if (Schema::hasColumn('club_invites', 'token')) {
                $table->dropUnique('club_invites_token_unique');
                $table->dropColumn('token');
            }
        });
    }
};
