<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Branch;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Services\WatermarkService;

class DocumentManagementController extends Controller
{
    private $watermarkService;
    
    public function __construct(WatermarkService $watermarkService)
    {
        $this->watermarkService = $watermarkService;
    }
    
    public function index(Request $request)
    {
        $query = Resource::with(['teacher', 'course', 'branch', 'lesson']);
        
        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        
        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        // Filter by visibility
        if ($request->filled('visibility')) {
            if ($request->visibility === 'public') {
                $query->where('is_public', true);
            } elseif ($request->visibility === 'private') {
                $query->where('is_public', false);
            }
        }
        
        // Filter by pricing
        if ($request->filled('pricing')) {
            if ($request->pricing === 'free') {
                $query->where('is_free', true);
            } elseif ($request->pricing === 'paid') {
                $query->where('is_free', false);
            }
        }
        
        $resources = $query->latest()->paginate(15);
        
        $teachers = Teacher::select('id', 'teacher_name')->get();
        $courses = Course::select('id', 'course_name')->get();
        $branches = Branch::select('id', 'name')->get();
        
        return view('admin.documents.index', compact('resources', 'teachers', 'courses', 'branches'));
    }
    
    public function edit($id)
    {
        $resource = Resource::with(['teacher', 'course', 'branch', 'lesson'])->findOrFail($id);
        $courses = Course::select('id', 'course_name')->get();
        $branches = Branch::select('id', 'name')->get();
        
        return view('admin.documents.edit', compact('resource', 'courses', 'branches'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'is_public' => 'boolean',
            'is_free' => 'boolean',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        $resource = Resource::findOrFail($id);
        
        // If document is being made public and has a file, apply watermark
        if ($request->is_public && !$resource->watermark_applied && $resource->document_blob) {
            $this->watermarkService->applyWatermark($resource);
        }
        
        $resource->update([
            'price' => $request->price,
            'is_public' => $request->boolean('is_public'),
            'is_free' => $request->boolean('is_free'),
            'admin_notes' => $request->admin_notes,
        ]);
        
        return redirect()->route('admin.documents.index')
            ->with('success', 'Document updated successfully!');
    }
    
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'resource_ids' => 'required|array',
            'resource_ids.*' => 'exists:resources,id',
            'action' => 'required|in:make_public,make_private,make_free,make_paid,apply_watermark',
            'price' => 'required_if:action,make_paid|numeric|min:0',
        ]);
        
        $resources = Resource::whereIn('id', $request->resource_ids);
        
        switch ($request->action) {
            case 'make_public':
                $resources->update(['is_public' => true]);
                // Apply watermark to documents with files
                foreach ($resources->get() as $resource) {
                    if ($resource->document_blob && !$resource->watermark_applied) {
                        $this->watermarkService->applyWatermark($resource);
                    }
                }
                break;
                
            case 'make_private':
                $resources->update(['is_public' => false]);
                break;
                
            case 'make_free':
                $resources->update(['is_free' => true, 'price' => 0]);
                break;
                
            case 'make_paid':
                $resources->update(['is_free' => false, 'price' => $request->price]);
                break;
                
            case 'apply_watermark':
                foreach ($resources->get() as $resource) {
                    if ($resource->document_blob && !$resource->watermark_applied) {
                        $this->watermarkService->applyWatermark($resource);
                    }
                }
                break;
        }
        
        return redirect()->route('admin.documents.index')
            ->with('success', 'Bulk update completed successfully!');
    }
    
    public function downloadDocument($id)
    {
        $resource = Resource::findOrFail($id);
        
        // Return the watermarked version if it exists
        $documentContent = $resource->document_blob;
        
        return response($documentContent)
            ->header('Content-Type', $resource->document_mime)
            ->header('Content-Disposition', 'attachment; filename="'.$resource->document_name.'"');
    }
    
    public function previewDocument($id)
    {
        $resource = Resource::findOrFail($id);
        
        // Return the watermarked version for preview
        $documentContent = $resource->document_blob;
        
        return response($documentContent)
            ->header('Content-Type', $resource->document_mime)
            ->header('Content-Disposition', 'inline; filename="'.$resource->document_name.'"');
    }
}
