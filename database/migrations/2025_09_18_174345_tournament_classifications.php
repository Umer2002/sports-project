<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tournament_classifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('option_id'); // FK to sport_classification_options
            $table->timestamps();

            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('sport_classification_options')->onDelete('cascade');
            $table->unique(['tournament_id','option_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('tournament_classifications');
    }
};
