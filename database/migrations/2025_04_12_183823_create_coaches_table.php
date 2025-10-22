<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 191);
            $table->string('last_name', 191);
            $table->string('email', 191)->unique();
            $table->string('phone', 191);
            $table->string('gender', 191);
            $table->text('socail_links');
            $table->string('city', 191);
            $table->text('bio');
            $table->string('country_id', 191);
            $table->string('photo', 191)->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->foreignId('sport_id')->constrained()->onDelete('cascade'); // links to sports table
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
