<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Services\WatermarkService;

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
    
    protected static function booted()
    {
        static::updated(function ($resource) {
            // Apply watermark when resource is made public and has document content
            if ($resource->wasChanged('is_public') && $resource->is_public && $resource->document_blob && !$resource->watermark_applied) {
                try {
                    $watermarkService = app(WatermarkService::class);
                    $watermarkService->applyWatermark($resource);
                    Log::info('Watermark automatically applied to resource: ' . $resource->id);
                } catch (\Exception $e) {
                    Log::error('Auto-watermark failed for resource ' . $resource->id . ': ' . $e->getMessage());
                }
            }
        });
        
        static::created(function ($resource) {
            // Apply watermark if created as public and has document content
            if ($resource->is_public && $resource->document_blob && !$resource->watermark_applied) {
                try {
                    $watermarkService = app(WatermarkService::class);
                    $watermarkService->applyWatermark($resource);
                    Log::info('Watermark automatically applied to new public resource: ' . $resource->id);
                } catch (\Exception $e) {
                    Log::error('Auto-watermark failed for new resource ' . $resource->id . ': ' . $e->getMessage());
                }
            }
        });
    }
    
    // Accessors to decode base64 blob data
    public function getDocumentBlobAttribute($value)
    {
        return $value ? base64_decode($value) : $value;
    }
    
    public function getVideoBlobAttribute($value)
    {
        return $value ? base64_decode($value) : $value;
    }
    

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

    public function academicYears()
    {
        return $this->belongsToMany(AcademicYear::class, 'academic_year_resource', 'resource_id', 'academic_year_id');
    }
}
