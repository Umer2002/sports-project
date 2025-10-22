<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blog_category_id');
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->string('slug')->nullable(); // included here directly
            $table->text('content');
            $table->string('image')->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Optional: add foreign keys
            // $table->foreign('blog_category_id')->references('id')->on('blog_categories')->onDelete('cascade');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('blog_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blog_id');
            $table->string('name');
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('comment');
            $table->timestamps();
            $table->softDeletes();

            // Optional foreign key
            // $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Backup tables before drop (optional logic can be moved to Seeder or Artisan Command)
        foreach (['blog_comments', 'blogs', 'blog_categories'] as $table) {
            \Illuminate\Support\Facades\Storage::disk('local')->put(
                $table . '_' . date('Y-m-d_H-i-s') . '.bak',
                json_encode(DB::table($table)->get())
            );
            Schema::dropIfExists($table);
        }
    }
};
