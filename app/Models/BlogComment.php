<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'blog_id',
        'name',
        'email',
        'website',
        'comment',
    ];

    protected $dates = ['deleted_at'];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
