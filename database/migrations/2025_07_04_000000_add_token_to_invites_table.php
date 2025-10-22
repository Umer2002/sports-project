<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invites', function (Blueprint $table) {
            if (!Schema::hasColumn('invites', 'token')) {
                $table->uuid('token')->nullable()->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('invites', function (Blueprint $table) {
            if (Schema::hasColumn('invites', 'token')) {
                $table->dropColumn('token');
            }
        });
    }
};
