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
        Schema::create('referee_expertises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referee_id')->constrained('referees')->onDelete('cascade');
            $table->foreignId('expertise_id')->constrained('expertises')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure a referee can't have the same expertise twice
            $table->unique(['referee_id', 'expertise_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referee_expertises');
    }
};
