<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'image', 'achievement'];
    public function player_rewards()
    {
        return $this->hasMany(PlayerReward::class, 'reward_id');
    }
}
