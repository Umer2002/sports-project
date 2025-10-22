<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('post_id');
            $table->unsignedBigInteger('user_id');
            $table->string('publisher')->nullable();
            $table->unsignedBigInteger('publisher_id')->nullable();
            $table->string('post_type')->nullable();
            $table->enum('privacy', ['public', 'private', 'friends'])->default('public');
            $table->json('tagged_user_ids')->nullable();
            $table->string('feel_and_activity')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->json('user_reacts')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('album_image_id')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
