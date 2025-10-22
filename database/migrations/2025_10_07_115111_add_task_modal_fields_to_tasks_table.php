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
        Schema::table('tasks', function (Blueprint $table) {
            $table->json('subtasks')->nullable()->after('description');
            $table->json('attachments')->nullable()->after('subtasks');
            $table->unsignedBigInteger('related_team_id')->nullable()->after('assigned_to');
            $table->boolean('notify_email')->default(false)->after('status');
            $table->boolean('notify_chat')->default(false)->after('notify_email');
            
            // Add foreign key for related_team_id
            $table->foreign('related_team_id')->references('id')->on('teams')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['related_team_id']);
            $table->dropColumn(['subtasks', 'attachments', 'related_team_id', 'notify_email', 'notify_chat']);
        });
    }
};
