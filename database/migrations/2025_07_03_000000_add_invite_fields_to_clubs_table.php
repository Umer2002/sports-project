<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            if (!Schema::hasColumn('clubs', 'invite_token')) {
                $table->string('invite_token')->nullable()->unique();
            }
            if (!Schema::hasColumn('clubs', 'invites_count')) {
                $table->unsignedInteger('invites_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            if (Schema::hasColumn('clubs', 'invite_token')) {
                $table->dropColumn('invite_token');
            }
            if (Schema::hasColumn('clubs', 'invites_count')) {
                $table->dropColumn('invites_count');
            }
        });
    }
};
