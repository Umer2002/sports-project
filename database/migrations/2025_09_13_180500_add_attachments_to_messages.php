<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('content');
            }
            if (!Schema::hasColumn('messages', 'attachment_type')) {
                $table->string('attachment_type')->nullable()->after('attachment_path');
            }
            if (!Schema::hasColumn('messages', 'metadata')) {
                $table->json('metadata')->nullable()->after('attachment_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'metadata')) {
                $table->dropColumn('metadata');
            }
            if (Schema::hasColumn('messages', 'attachment_type')) {
                $table->dropColumn('attachment_type');
            }
            if (Schema::hasColumn('messages', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
        });
    }
};

