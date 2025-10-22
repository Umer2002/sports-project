<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('genders', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK to sports
            $table->unsignedBigInteger('sport_id');
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');

            // Per-sport unique gender code
            $table->string('code', 32);     // BOYS, GIRLS, MEN, WOMEN
            $table->string('label', 100);   // Boys, Girls, Men, Women
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique(['sport_id','code']); // each sport gets its own set
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genders');
    }
};
