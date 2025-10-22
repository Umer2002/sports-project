<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sport_classification_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sport_id'); // FK to sports
            $table->string('code', 64);             // e.g., MATCH_FORMAT, COMP_TIER, SKILL_BAND
            $table->string('name', 191);            // Human label: "MATCH FORMATS"
            $table->string('description', 512)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('meta')->nullable();       // room for extra settings per group
            $table->timestamps();

            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->unique(['sport_id','code']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('sport_classification_groups');
    }
};
