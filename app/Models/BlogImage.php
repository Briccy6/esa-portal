<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    protected $fillable = [
        'blog_id',
        'image_path',
        'image_alt',
        'image_caption',
        'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer'
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
