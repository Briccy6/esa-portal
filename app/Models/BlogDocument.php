<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogDocument extends Model
{
    protected $fillable = [
        'blog_id',
        'document_path',
        'document_name',
        'document_size',
        'document_type'
    ];

    protected $casts = [
        'document_size' => 'integer'
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function getDownloadUrlAttribute()
    {
        return asset('storage/' . $this->document_path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->document_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
