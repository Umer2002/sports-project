<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = [
        'name', 'description', 'icon_path', 'is_top_sport',
    ];
}
