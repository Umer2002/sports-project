<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bug_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('category')->default('general');
            $table->string('severity', 20)->default('low');
            $table->text('description')->nullable();
            $table->text('steps')->nullable();
            $table->text('environment')->nullable();
            $table->boolean('include_logs')->default(false);
            $table->string('contact')->nullable();
            $table->boolean('share_diagnostics')->default(false);
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_reports');
    }
};
