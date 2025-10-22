<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'sport_id',
        'category',
        'name',
        'sort_order',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }
}
