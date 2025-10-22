<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class Video extends Model
{
    protected $fillable = [
        'title',
        'description',
        'url',
        'video_type',
        'category',
        'user_id',
        'playtube_id',
        'is_ad',
    ];

    protected $casts = [
        'is_ad' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\VideoComment::class);
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\VideoLike::class);
    }

    public function getPlaybackUrlAttribute(): string
    {
        $path = $this->url;

        if (! is_string($path) || $path === '') {
            return '';
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $disk = env('VIDEO_DISK', config('filesystems.default'));
        $config = config("filesystems.disks.{$disk}", []);
        $storage = Storage::disk($disk);

        $servedPrivately = ($config['driver'] ?? null) === 'local'
            && ($config['serve'] ?? false)
            && ($config['visibility'] ?? 'private') !== 'public';

        if ($servedPrivately && method_exists($storage, 'providesTemporaryUrls') && $storage->providesTemporaryUrls()) {
            try {
                return $storage->temporaryUrl($path, now()->addMinutes(30));
            } catch (Throwable $e) {
                // fall through to regular URL generation
            }
        }

        try {
            return $storage->url($path);
        } catch (Throwable $e) {
            return $path;
        }
    }
}
