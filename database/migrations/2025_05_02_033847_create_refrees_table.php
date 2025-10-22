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
        Schema::create('referees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('government_id')->nullable();
            $table->string('languages_spoken')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('license_type')->nullable();
            $table->string('certifying_body')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->boolean('background_check_passed')->default(false);
            $table->boolean('liability_insurance')->default(false);
            $table->string('liability_document')->nullable();
            $table->json('sports_officiated')->nullable();
            $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referees');
    }
};
