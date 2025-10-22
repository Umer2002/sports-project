<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpChatTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ticket_number',
        'status',
        'reason',
        'created_by',
        'closed_at',
    ];

    protected $dates = [
        'closed_at',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(HelpChatSession::class, 'session_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
