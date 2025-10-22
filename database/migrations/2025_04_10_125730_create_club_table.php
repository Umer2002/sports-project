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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id(); // primary key, bigint unsigned auto-increment
            $table->string('name', 191);
            $table->string('logo', 191)->nullable();
            $table->text('social_links'); // you can store JSON or text data here; adjust as needed
            $table->string('paypal_link', 191)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('email', 191)->index();
            $table->string('joining_url', 191)->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_registered')->default(0);
            $table->timestamps(); // includes created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
