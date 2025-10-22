<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameEvent extends Model
{
    protected $fillable = [
        'game_id',
        'description',
        'minute',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
