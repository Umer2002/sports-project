<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blog_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('blog_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['blog_id', 'user_id']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_likes');
    }
};
