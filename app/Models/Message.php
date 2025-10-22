<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $appends = ['attachment_url'];
    protected $fillable = [
        'chat_id',
        'sender_id',
        'receiver_id',
        'content',
        'is_read',
        'attachment_path',
        'attachment_type',
        'metadata',
    ];

    /**
     * Get the chat this message belongs to.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Alias for sender to simplify views expecting `user`.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) return null;
        // If already a full URL, return as-is
        if (str_starts_with($this->attachment_path, 'http://') || str_starts_with($this->attachment_path, 'https://')) {
            return $this->attachment_path;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->attachment_path);
    }
}
