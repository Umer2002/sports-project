<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('News', function (Blueprint $table) {
            $table->id(); // id (Primary, UNSIGNED, AUTO_INCREMENT)
            $table->string('title', 191); // title (varchar 191)
            $table->text('content'); // content (text)
            $table->string('image', 191)->nullable(); // image (varchar 191)
            $table->string('category', 191); // category (varchar 191)
            $table->timestamps(); // created_at, updated_at (timestamp, nullable)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('News');
    }
}
