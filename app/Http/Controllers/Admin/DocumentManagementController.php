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

class DocumentManagementController extends Controller
{
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
            $this->applyWatermark($resource);
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
    
    public function applyWatermark(Resource $resource)
    {
        try {
            // Get the logo from storage (assuming logo is stored at public/logo.png)
            $logoPath = public_path('logo.png');
            
            if (!file_exists($logoPath)) {
                // Create a default watermark if logo doesn't exist
                $this->createDefaultWatermark($logoPath);
            }
            
            // Create image from document blob
            $image = Image::make($resource->document_blob);
            
            // Get logo dimensions
            $logo = Image::make($logoPath);
            $logoWidth = $logo->width();
            $logoHeight = $logo->height();
            
            // Calculate watermark position (bottom right corner)
            $watermarkX = $image->width() - $logoWidth - 20;
            $watermarkY = $image->height() - $logoHeight - 20;
            
            // Apply watermark with transparency
            $logo->opacity(50); // 50% transparency
            $image->insert($logo, 'bottom-right', 20, 20);
            
            // Save the watermarked image
            $watermarkedPath = 'watermarked_documents/resource_' . $resource->id . '.png';
            Storage::put($watermarkedPath, $image->encode());
            
            // Update resource record
            $resource->update([
                'watermark_path' => $watermarkedPath,
                'watermark_applied' => true,
                'document_blob' => $image->encode('png'), // Update with watermarked version
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Watermark application failed: ' . $e->getMessage());
            return false;
        }
    }
    
    private function createDefaultWatermark($path)
    {
        // Create a simple text watermark as fallback
        $image = Image::canvas(200, 100, '#ffffff');
        $image->text('ESA SCHOOL', 100, 50, function($font) {
            $font->size(24);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });
        $image->save($path);
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
                        $this->applyWatermark($resource);
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
                        $this->applyWatermark($resource);
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
