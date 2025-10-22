<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    protected $fillable = [
        'video_id',
        'user_id',
        'author_name',
        'author_avatar',
        'content',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

