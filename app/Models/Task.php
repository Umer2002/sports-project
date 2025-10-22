<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title', 
        'description', 
        'status', 
        'assigned_to', 
        'priority', 
        'due_date', 
        'created_by',
        'subtasks',
        'attachments',
        'related_team_id',
        'notify_email',
        'notify_chat'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'subtasks' => 'array',
        'attachments' => 'array',
        'notify_email' => 'boolean',
        'notify_chat' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'related_team_id');
    }
}
