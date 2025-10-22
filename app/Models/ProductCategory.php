<?php

// ProductCategory.php - Model
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';

    protected $fillable = ['name', 'slug', 'club_id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
