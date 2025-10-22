<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $primaryKey = 'post_id'; // Custom primary key

    protected $fillable = [
        'user_id',
        'publisher',
        'publisher_id',
        'post_type',
        'privacy',
        'tagged_user_ids',
        'feel_and_activity',
        'location',
        'description',
        'user_reacts',
        'status',
        'album_image_id',
    ];

    protected $casts = [
        'tagged_user_ids' => 'array',
        'user_reacts' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mediaFiles()
    {
        return $this->hasMany(Media_files::class, 'post_id', 'post_id');
    }
}
