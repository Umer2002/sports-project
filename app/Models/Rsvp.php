<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    protected $fillable = ['match_id', 'user_id', 'status'];
    public function game() { return $this->belongsTo(Game::class, 'match_id'); }
    public function user() { return $this->belongsTo(User::class); }
}

