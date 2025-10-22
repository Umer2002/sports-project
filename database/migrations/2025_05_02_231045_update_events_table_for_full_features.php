<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventsTableForFullFeatures extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('group_id')->nullable()->after('user_id');
            $table->text('description')->nullable()->after('title');
            $table->date('event_date')->nullable()->after('description');
            $table->time('event_time')->nullable()->after('event_date');
            $table->string('location')->nullable()->after('event_time');
            $table->string('banner')->nullable()->after('location');
            $table->json('going_users_id')->nullable()->after('banner');
            $table->json('interested_users_id')->nullable()->after('going_users_id');
            $table->enum('privacy', ['public', 'private', 'friends'])->default('public')->after('interested_users_id');

            // Optional: add foreign key constraint if users table exists
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'group_id',
                'description',
                'event_date',
                'event_time',
                'location',
                'banner',
                'going_users_id',
                'interested_users_id',
                'privacy',
            ]);
        });
    }
}
