<?php

// 1. MIGRATION: create_injury_reports_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInjuryReportsTable extends Migration
{
    public function up()
    {
        Schema::create('injury_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->dateTime('injury_datetime');
            $table->string('team_name');
            $table->string('location');
            $table->string('injury_type');
            $table->text('injury_type_other')->nullable();
            $table->text('incident_description');
            $table->json('images')->nullable();
            $table->boolean('first_aid')->default(false);
            $table->string('first_aid_description')->nullable();
            $table->boolean('emergency_called')->default(false);
            $table->boolean('hospital_referred')->default(false);
            $table->string('assisted_by')->nullable();
            $table->string('assisted_by_other')->nullable();
            $table->date('expected_recovery')->nullable();
            $table->string('medical_note')->nullable();
            $table->boolean('return_to_play_required')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('injury_reports');
    }
}
