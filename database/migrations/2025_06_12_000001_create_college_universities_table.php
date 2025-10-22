<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('college_universities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sport_id')->nullable()->constrained()->nullOnDelete();
            $table->string('college_name');
            $table->string('logo')->nullable();
            $table->text('social_links');
            $table->string('paypal_link')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->index();
            $table->string('joining_url')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_registered')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('college_universities');
    }
};
