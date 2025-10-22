<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('age_groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK to sports
            $table->unsignedBigInteger('sport_id');
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');

            // Per-sport unique code (e.g., U5, SENIOR_OPEN, MASTERS_25_PLUS)
            $table->string('code', 32);

            // Human-friendly label
            $table->string('label', 191);

            // Optional numeric bounds (years)
            $table->unsignedSmallInteger('min_age_years')->nullable();
            $table->unsignedSmallInteger('max_age_years')->nullable();
            $table->boolean('is_open_ended')->default(false);

            // Context/notes help show sport-specific wording in dropdown tooltips
            $table->string('context', 191)->nullable(); // e.g. "Minor Hockey", "Athletics"
            $table->text('notes')->nullable();

            // For ordered dropdowns
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            // Each sport can reuse "U5", etc., but (sport_id, code) must be unique
            $table->unique(['sport_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('age_groups');
    }
};
