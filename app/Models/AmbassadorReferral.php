<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmbassadorReferral extends Model
{
    protected $fillable = ['ambassador_id','referred_user_id','type'];

    public function ambassador()
    {
        return $this->belongsTo(User::class, 'ambassador_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}

