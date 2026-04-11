<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'image_data',
        'mime_type',
        'filename',
        'image_path',
        'caption',
        'alt_text',
        'sort_order',
        'featured',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the blog that owns the image.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the full URL for the image (as data URI from database).
     */
    public function getUrlAttribute(): string
    {
        // If image is stored in database
        if ($this->image_data) {
            $base64 = base64_encode($this->image_data);
            return 'data:' . $this->mime_type . ';base64,' . $base64;
        }

        // Fallback to file system (for legacy data)
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }

        return '';
    }

    /**
     * Get the thumbnail URL for the image.
     */
    public function getThumbnailUrlAttribute(): string
    {
        // For now, return the same URL. You can implement thumbnail generation later.
        return $this->getUrlAttribute();
    }

    /**
     * Get the image display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->filename ?? ($this->image_path ? basename($this->image_path) : 'Image');
    }
}
