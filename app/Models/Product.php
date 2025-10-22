<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'club_id', 'name', 'slug', 'description', 'price', 'stock', 'image'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
