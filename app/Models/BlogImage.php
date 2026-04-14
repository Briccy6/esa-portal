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
        'image_path',      // stores binary image data
        'image_alt',       // stores mime_type
        'image_caption',   // stores filename
        'sort_order',
    ];

    protected $casts = [
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
     * Get mime type (stored in image_alt column).
     */
    public function getMimeTypeAttribute(): ?string
    {
        return $this->attributes['image_alt'] ?? 'image/jpeg';
    }

    /**
     * Get filename (stored in image_caption column).
     */
    public function getFilenameAttribute(): ?string
    {
        return $this->attributes['image_caption'];
    }

    /**
     * Get the image as a data URI (for binary image data stored in image_path).
     */
    public function getUrlAttribute(): string
    {
        if ($this->image_path && $this->image_alt) {
            $base64 = base64_encode($this->image_path);
            return 'data:' . $this->image_alt . ';base64,' . $base64;
        }

        return '';
    }

    /**
     * Get the thumbnail URL for the image.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->getUrlAttribute();
    }

    /**
     * Get the image display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->image_caption ?? 'Image';
    }
}
