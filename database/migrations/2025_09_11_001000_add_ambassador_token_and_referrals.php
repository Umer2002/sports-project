<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'ambassador_token')) {
                $table->string('ambassador_token', 64)->nullable()->unique()->after('coach_id');
            }
        });

        Schema::create('ambassador_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ambassador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['player','club']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambassador_referrals');
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'ambassador_token')) {
                $table->dropUnique(['ambassador_token']);
                $table->dropColumn('ambassador_token');
            }
        });
    }
};

