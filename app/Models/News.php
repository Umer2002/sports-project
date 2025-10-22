<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'News'; // Specify the table name if it doesn't match the plural of model

    protected $fillable = [
        'title',
        'content',
        'image',
        'category',
    ];
}
