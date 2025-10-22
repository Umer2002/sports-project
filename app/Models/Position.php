<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['position_name', 'position_value', 'sports_id', 'is_active'];

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sports_id');
    }
}
