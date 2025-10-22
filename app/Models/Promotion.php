<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'sport_id', 'title', 'description', 'video_path', 'youtube_url', 'share_text'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
