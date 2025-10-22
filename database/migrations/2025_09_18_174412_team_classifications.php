<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_classifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('option_id'); // FK to sport_classification_options
            $table->timestamps();

            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('sport_classification_options')->onDelete('cascade');
            $table->unique(['team_id','option_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('team_classifications');
    }
};
