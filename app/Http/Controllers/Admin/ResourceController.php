<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Course;
use App\Models\Branch;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::with(['teacher', 'course', 'branch', 'lesson'])
            ->latest()
            ->paginate(15);
        
        return view('admin.resources.index', compact('resources'));
    }
    
    public function create()
    {
        $courses = Course::select('id', 'course_name')->get();
        $branches = Branch::select('id', 'name')->get();
        $teachers = Teacher::select('id', 'teacher_name')->get();
        $lessons = [];
        
        return view('admin.resources.create', compact('courses', 'branches', 'teachers', 'lessons'));
    }
    
    public function getLessons($courseId)
    {
        $lessons = Lesson::where('course_id', $courseId)
            ->select('id', 'title')
            ->get();
        
        return response()->json(['lessons' => $lessons]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'required|exists:lessons,id',
            'youtube_link' => 'nullable|url',
            'document' => 'nullable|file|mimes:pdf,doc,docx,ppt,ppt,xls,xlsx,txt|max:10240',
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv|max:51200',
            'document_link' => 'nullable|url',
            'price' => 'required|numeric|min:0',
            'is_public' => 'boolean',
            'is_free' => 'boolean',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            $resourceData = [
                'teacher_id' => $request->teacher_id,
                'branch_id' => $request->branch_id,
                'course_id' => $request->course_id,
                'lesson_id' => $request->lesson_id,
                'youtube_link' => $request->youtube_link,
                'price' => $request->price,
                'is_public' => $request->boolean('is_public'),
                'is_free' => $request->boolean('is_free'),
                'admin_notes' => $request->admin_notes,
                'watermark_applied' => false,
            ];
            
            // Handle document upload
            if ($request->hasFile('document')) {
                $document = $request->file('document');
                if ($document->isValid()) {
                    $documentPath = $document->store('documents', 'public');
                    $resourceData['document_name'] = $document->getClientOriginalName();
                    $resourceData['document_mime'] = $document->getMimeType();
                    
                    // Store document content in blob
                    $resourceData['document_blob'] = file_get_contents($document->getRealPath());
                } else {
                    return back()
                        ->withInput()
                        ->with('error', 'Invalid document file: ' . $document->getErrorMessage());
                }
            } elseif ($request->filled('document_link')) {
                // Handle document link
                $resourceData['document_name'] = $request->document_link;
                $resourceData['document_mime'] = 'text/url';
                $resourceData['document_blob'] = null;
            }
            
            // Handle video upload
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                if ($video->isValid()) {
                    $resourceData['video_name'] = $video->getClientOriginalName();
                    $resourceData['video_mime'] = $video->getMimeType();
                    $resourceData['video_blob'] = file_get_contents($video->getRealPath());
                } else {
                    return back()
                        ->withInput()
                        ->with('error', 'Invalid video file: ' . $video->getErrorMessage());
                }
            }
            
            $resource = Resource::create($resourceData);
            
            // Apply watermark if document is public and has content
            if ($request->boolean('is_public') && isset($resourceData['document_blob'])) {
                $this->applyWatermark($resource);
            }
            
            return redirect()->route('admin.resources.index')
                ->with('success', 'Resource uploaded successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error uploading resource: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $resource = Resource::with(['teacher', 'course', 'branch', 'lesson'])->findOrFail($id);
        $courses = Course::select('id', 'course_name')->get();
        $branches = Branch::select('id', 'name')->get();
        $teachers = Teacher::select('id', 'teacher_name')->get();
        $lessons = Lesson::where('course_id', $resource->course_id)->get();
        
        return view('admin.resources.edit', compact('resource', 'courses', 'branches', 'teachers', 'lessons'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'required|exists:lessons,id',
            'youtube_link' => 'nullable|url',
            'document' => 'nullable|file|mimes:pdf,doc,docx,ppt,ppt,xls,xlsx,txt|max:10240',
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv|max:51200',
            'document_link' => 'nullable|url',
            'price' => 'required|numeric|min:0',
            'is_public' => 'boolean',
            'is_free' => 'boolean',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            $resource = Resource::findOrFail($id);
            
            $resourceData = [
                'teacher_id' => $request->teacher_id,
                'branch_id' => $request->branch_id,
                'course_id' => $request->course_id,
                'lesson_id' => $request->lesson_id,
                'youtube_link' => $request->youtube_link,
                'price' => $request->price,
                'is_public' => $request->boolean('is_public'),
                'is_free' => $request->boolean('is_free'),
                'admin_notes' => $request->admin_notes,
            ];
            
            // Handle document upload
            if ($request->hasFile('document')) {
                $document = $request->file('document');
                if ($document->isValid()) {
                    $resourceData['document_name'] = $document->getClientOriginalName();
                    $resourceData['document_mime'] = $document->getMimeType();
                    $resourceData['document_blob'] = file_get_contents($document->getRealPath());
                    $resourceData['watermark_applied'] = false; // Reset watermark status
                } else {
                    return back()
                        ->withInput()
                        ->with('error', 'Invalid document file: ' . $document->getErrorMessage());
                }
            } elseif ($request->filled('document_link')) {
                $resourceData['document_name'] = $request->document_link;
                $resourceData['document_mime'] = 'text/url';
                $resourceData['document_blob'] = null;
                $resourceData['watermark_applied'] = false;
            }
            
            // Handle video upload
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                if ($video->isValid()) {
                    $resourceData['video_name'] = $video->getClientOriginalName();
                    $resourceData['video_mime'] = $video->getMimeType();
                    $resourceData['video_blob'] = file_get_contents($video->getRealPath());
                } else {
                    return back()
                        ->withInput()
                        ->with('error', 'Invalid video file: ' . $video->getErrorMessage());
                }
            }
            
            $resource->update($resourceData);
            
            // Apply watermark if document is public and has content
            if ($request->boolean('is_public') && $resource->document_blob && !$resource->watermark_applied) {
                $this->applyWatermark($resource);
            }
            
            return redirect()->route('admin.resources.index')
                ->with('success', 'Resource updated successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating resource: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        try {
            $resource = Resource::findOrFail($id);
            $resource->delete();
            
            return redirect()->route('admin.resources.index')
                ->with('success', 'Resource deleted successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error deleting resource: ' . $e->getMessage());
        }
    }
    
    public function downloadDocument($id)
    {
        $resource = Resource::findOrFail($id);
        
        if (!$resource->document_blob) {
            return back()->with('error', 'Document not available for download');
        }
        
        return response($resource->document_blob)
            ->header('Content-Type', $resource->document_mime)
            ->header('Content-Disposition', 'attachment; filename="'.$resource->document_name.'"');
    }
    
    public function previewDocument($id)
    {
        $resource = Resource::findOrFail($id);
        
        if (!$resource->document_blob) {
            return back()->with('error', 'Document not available for preview');
        }
        
        return response($resource->document_blob)
            ->header('Content-Type', $resource->document_mime)
            ->header('Content-Disposition', 'inline; filename="'.$resource->document_name.'"');
    }
    
    private function applyWatermark(Resource $resource)
    {
        try {
            // Get the logo from storage (assuming logo is stored at public/logo.png)
            $logoPath = public_path('logo.png');
            
            if (!file_exists($logoPath)) {
                // Create a default watermark if logo doesn't exist
                $this->createDefaultWatermark($logoPath);
            }
            
            // Create image from document blob (only if it's an image)
            if (str_starts_with($resource->document_mime, 'image/')) {
                $image = \Intervention\Image\Facades\Image::make($resource->document_blob);
                
                // Get logo dimensions
                $logo = \Intervention\Image\Facades\Image::make($logoPath);
                
                // Calculate watermark position (bottom right corner)
                $watermarkX = $image->width() - $logo->width() - 20;
                $watermarkY = $image->height() - $logo->height() - 20;
                
                // Apply watermark with transparency
                $logo->opacity(50); // 50% transparency
                $image->insert($logo, 'bottom-right', 20, 20);
                
                // Update resource record
                $resource->update([
                    'watermark_path' => 'watermarked_documents/resource_' . $resource->id . '.png',
                    'watermark_applied' => true,
                    'document_blob' => $image->encode('png'), // Update with watermarked version
                ]);
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('Watermark application failed: ' . $e->getMessage());
            return false;
        }
    }
    
    private function createDefaultWatermark($path)
    {
        // Create a simple text watermark as fallback
        $image = \Intervention\Image\Facades\Image::canvas(200, 100, '#ffffff');
        $image->text('ESA SCHOOL', 100, 50, function($font) {
            $font->size(24);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });
        $image->save($path);
    }
}
