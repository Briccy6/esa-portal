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
use App\Services\WatermarkService;

class ResourceController extends Controller
{
    private $watermarkService;
    
    public function __construct(WatermarkService $watermarkService)
    {
        $this->watermarkService = $watermarkService;
    }
    
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
            'document' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt|max:10240',
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv|max:51200',
            'document_link' => 'nullable|url',
            'price' => 'required|numeric|min:0',
            'is_public' => 'sometimes|in:0,1,true,false',
            'is_free' => 'sometimes|in:0,1,true,false',
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
                    
                    // Store document content in blob (base64 encoded for longtext storage)
                    $documentContent = file_get_contents($document->getRealPath());
                    $resourceData['document_blob'] = base64_encode($documentContent);
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
                    $videoContent = file_get_contents($video->getRealPath());
                    $resourceData['video_blob'] = base64_encode($videoContent);
                } else {
                    return back()
                        ->withInput()
                        ->with('error', 'Invalid video file: ' . $video->getErrorMessage());
                }
            } else {
                // Ensure video fields are set when no video is uploaded
                $resourceData['video_name'] = null;
                $resourceData['video_mime'] = null;
                $resourceData['video_blob'] = null;
            }
            
            $resource = Resource::create($resourceData);
            
            // Watermark is automatically applied by model events when is_public = true
            
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
            'document' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt|max:10240',
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv|max:51200',
            'document_link' => 'nullable|url',
            'price' => 'required|numeric|min:0',
            'is_public' => 'sometimes|in:0,1,true,false',
            'is_free' => 'sometimes|in:0,1,true,false',
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
                    $documentContent = file_get_contents($document->getRealPath());
                    $resourceData['document_blob'] = base64_encode($documentContent);
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
                    $videoContent = file_get_contents($video->getRealPath());
                    $resourceData['video_blob'] = base64_encode($videoContent);
                } else {
                    return back()
                        ->withInput()
                        ->with('error', 'Invalid video file: ' . $video->getErrorMessage());
                }
            }
            
            $resource->update($resourceData);
            
            // Watermark is automatically applied by model events when is_public = true
            
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
    
    /**
     * Make a resource public (trigger automatic watermarking)
     */
    public function makePublic($id)
    {
        try {
            $resource = Resource::findOrFail($id);
            
            // Update resource to be public
            $resource->update([
                'is_public' => true,
            ]);
            
            // Watermark is automatically applied by model events
            
            return redirect()->route('admin.resources.index')
                ->with('success', 'Resource made public successfully! Watermark has been applied.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error making resource public: ' . $e->getMessage());
        }
    }
    
    // Assignment Management Methods for DOS
    public function assignmentsIndex()
    {
        // For now, return a placeholder view or redirect to resources
        return view('admin.assignments.index');
    }
    
    public function assignmentsCreate()
    {
        // For now, return a placeholder view
        return view('admin.assignments.create');
    }
    
    public function assignmentsStore(Request $request)
    {
        // Placeholder implementation
        return redirect()->route('dos.assignments.index')
            ->with('success', 'Assignment created successfully!');
    }
    
    public function assignmentsShow($id)
    {
        // Placeholder implementation
        return view('admin.assignments.show', ['assignment' => (object)['id' => $id]]);
    }
    
    public function assignmentsEdit($id)
    {
        // Placeholder implementation
        return view('admin.assignments.edit', ['assignment' => (object)['id' => $id]]);
    }
    
    public function assignmentsUpdate(Request $request, $id)
    {
        // Placeholder implementation
        return redirect()->route('dos.assignments.index')
            ->with('success', 'Assignment updated successfully!');
    }
    
    public function assignmentsDestroy($id)
    {
        // Placeholder implementation
        return redirect()->route('dos.assignments.index')
            ->with('success', 'Assignment deleted successfully!');
    }
}
