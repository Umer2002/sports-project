<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester');
            $table->unsignedBigInteger('accepter');
            $table->tinyInteger('importance')->default(0);
            $table->boolean('is_accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Optional foreign keys if you want to enforce user existence
            // $table->foreign('requester')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('accepter')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};

