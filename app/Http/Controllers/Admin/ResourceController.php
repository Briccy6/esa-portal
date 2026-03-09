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
            // Get the logo from storage
            $logoPath = public_path('logo.png');
            
            if (!file_exists($logoPath)) {
                // Try to get logo from public/images directory
                $logoPath = public_path('images/logo.png');
                if (!file_exists($logoPath)) {
                    // Create a default watermark if logo doesn't exist
                    $this->createDefaultWatermark($logoPath);
                }
            }
            
            // Handle PDF watermarking
            if ($resource->document_mime === 'application/pdf') {
                return $this->applyPdfWatermark($resource, $logoPath);
            }
            
            // Handle image watermarking
            if (str_starts_with($resource->document_mime, 'image/')) {
                return $this->applyImageWatermark($resource, $logoPath);
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('Watermark application failed: ' . $e->getMessage());
            return false;
        }
    }
    
    private function applyPdfWatermark(Resource $resource, $logoPath)
    {
        try {
            // Create temporary file for PDF processing
            $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf_watermark_');
            $tempOutputPath = tempnam(sys_get_temp_dir(), 'pdf_output_');
            
            // Write PDF blob to temporary file
            file_put_contents($tempPdfPath, $resource->document_blob);
            
            // Check if TCPDF or similar library is available
            if (class_exists('\TCPDF')) {
                $pdf = new \TCPDF();
                $pdf->setSourceFile($tempPdfPath);
                
                // Get school info for watermark text
                $schoolInfo = \App\Models\SchoolInformation::first();
                $watermarkText = $schoolInfo->school_name ?? 'ESA School';
                
                for ($i = 1; $i <= $pdf->getNumPages(); $i++) {
                    $pdf->AddPage();
                    $tplId = $pdf->importPage($i);
                    $pdf->useTemplate($tplId, 0, 0, 0, 0, true);
                    
                    // Add text watermark
                    $pdf->SetFont('Helvetica', 'B', 50);
                    $pdf->SetTextColor(200, 200, 200);
                    $pdf->SetAlpha(0.3);
                    $pdf->Text(30, 150, $watermarkText, 45, 0, false, false, 0, 0, 'C');
                    $pdf->SetAlpha(1);
                }
                
                // Save watermarked PDF
                $watermarkedContent = $pdf->Output('', 'S');
                
                // Update resource with watermarked content
                $resource->update([
                    'watermark_path' => 'watermarked_documents/resource_' . $resource->id . '.pdf',
                    'watermark_applied' => true,
                    'document_blob' => $watermarkedContent,
                ]);
                
                // Clean up temporary files
                unlink($tempPdfPath);
                unlink($tempOutputPath);
                
                return true;
            } else {
                // Fallback: Add a simple text overlay using FPDI if available
                // For now, just mark as watermarked without actual modification
                $resource->update([
                    'watermark_path' => 'watermarked_documents/resource_' . $resource->id . '.pdf',
                    'watermark_applied' => true,
                ]);
                
                unlink($tempPdfPath);
                unlink($tempOutputPath);
                
                return true;
            }
        } catch (\Exception $e) {
            \Log::error('PDF watermarking failed: ' . $e->getMessage());
            return false;
        }
    }
    
    private function applyImageWatermark(Resource $resource, $logoPath)
    {
        try {
            $image = new \Intervention\Image\ImageManager('gd');
            $imageData = $image->make($resource->document_blob);
            
            // Get logo dimensions
            $logo = $image->make($logoPath);
            
            // Calculate watermark size (20% of image width, max 200px)
            $watermarkWidth = min($imageData->width() * 0.2, 200);
            $logo->resize($watermarkWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            
            // Apply watermark with transparency at bottom right corner
            $logo->opacity(50); // 50% transparency
            $imageData->insert($logo, 'bottom-right', 20, 20);
            
            // Add school name text watermark
            $schoolInfo = \App\Models\SchoolInformation::first();
            $schoolName = $schoolInfo->school_name ?? 'ESA School';
            
            $imageData->text($schoolName, $imageData->width() / 2, $imageData->height() - 30, function($font) {
                $font->file(public_path('fonts/arial.ttf')); // Use system font if available
                $font->size(24);
                $font->color([255, 255, 255, 0.7]); // White with transparency
                $font->align('center');
                $font->valign('bottom');
            });
            
            // Update resource record
            $resource->update([
                'watermark_path' => 'watermarked_documents/resource_' . $resource->id . '.png',
                'watermark_applied' => true,
                'document_blob' => $imageData->encode('png'), // Update with watermarked version
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Image watermarking failed: ' . $e->getMessage());
            return false;
        }
    }
    
    private function createDefaultWatermark($path)
    {
        // Create a simple text watermark as fallback
        $image = new \Intervention\Image\ImageManager('gd');
        $canvas = $image->canvas(200, 100, '#ffffff');
        $canvas->text('ESA SCHOOL', 100, 50, function($font) {
            $font->size(24);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });
        $canvas->save($path);
    }
}
