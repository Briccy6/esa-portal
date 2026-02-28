<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'teacher_id', 'course_id', 'branch_id', 'lesson_id', 'youtube_link',
        'document_blob', 'document_name', 'document_mime',
        'video_blob', 'video_name', 'video_mime',
        'price', 'is_public', 'is_free', 'watermark_path', 'watermark_applied', 'admin_notes'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'is_public' => 'boolean',
        'is_free' => 'boolean',
        'watermark_applied' => 'boolean',
    ];
    

    public function teacher()
    {
        return $this->belongsTo(\App\Models\Teacher::class);
    }
    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class);
    }
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }
    public function lesson()
{
    return $this->belongsTo(\App\Models\Lesson::class);
}

}
