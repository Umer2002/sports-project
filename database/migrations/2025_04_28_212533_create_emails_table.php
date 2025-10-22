<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration
{
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');       // Sender user_id
            $table->string('email_id');                  // Receiver email address
            $table->string('subject')->nullable();
            $table->text('email_message')->nullable();
            $table->enum('status', ['draft', 'sent', 'trashed'])->default('draft');
            $table->unsignedBigInteger('deleted_userid')->nullable(); // Who deleted it
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('emails');
    }
}
