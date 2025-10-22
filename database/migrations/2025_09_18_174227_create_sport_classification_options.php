<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sport_classification_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('group_id');     // FK to sport_classification_groups
            $table->string('code', 64);                 // e.g., FMT_3V3, DIV_1, TIER_1, AAA
            $table->string('label', 191);               // e.g., "3v3", "Division 1"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->integer('numeric_rank')->nullable(); // for divisions/tiers sorting (lower is higher)
            $table->json('meta')->nullable();            // extra attributes (e.g., {team_size: 5, surface: "indoor"})
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('sport_classification_groups')->onDelete('cascade');
            $table->unique(['group_id','code']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('sport_classification_options');
    }
};
