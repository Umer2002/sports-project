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
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->after('ambassador_token');
            $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code');
            
            $table->index('referral_code');
            $table->index('referred_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['referral_code']);
            $table->dropIndex(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by']);
        });
    }
};