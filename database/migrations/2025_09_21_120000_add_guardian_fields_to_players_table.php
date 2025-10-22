<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('guardian_first_name')->nullable()->after('referee_affiliation');
            $table->string('guardian_last_name')->nullable()->after('guardian_first_name');
            $table->string('guardian_email')->nullable()->after('guardian_last_name');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'guardian_first_name',
                'guardian_last_name',
                'guardian_email',
            ]);
        });
    }
};
