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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo')->nullable(); // Optional logo field
            $table->timestamps();

            // Define foreign key constraint: each team belongs to a club.
            $table->foreign('club_id')
                  ->references('id')->on('clubs')
                  ->onDelete('cascade');  // Delete teams if the parent club is deleted.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
