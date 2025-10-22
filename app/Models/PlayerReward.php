<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerReward extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'reward_id', 'issued_by', 'status'];
    public function player()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship to the reward
    public function reward()
    {
        return $this->belongsTo(Reward::class, 'reward_id');
    }
}
