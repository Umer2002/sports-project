<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payout_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('player_count')->unique();
            $table->decimal('payout_amount', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payout_plans');
    }
}; 