<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    // Laravel will assume the table is named 'countries'
    protected $fillable = [
        'sortname',
        'name',
    ];
}
