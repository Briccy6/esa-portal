<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Branch; 
class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth('teacher')->user();
        $branches = Branch::all();
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();
    
        $query = Resource::with(['course:id,course_name', 'lesson:id,title'])
            ->where('teacher_id', $teacher->id);
    
        if ($request->filled('branch_ids')) {
            $query->whereIn('branch_id', $request->branch_ids);
        }
    
        $resources = $query->latest()->paginate(10);
    
        $courses = $teacher->courses()
            ->select('courses.id', 'courses.course_name')
            ->get();
    
        // Add this line to fetch all lessons for the teacher's courses
        $lessons = Lesson::whereIn('course_id', $courses->pluck('id'))->get();
    
        return view('teacher.resources.index', compact('resources', 'branches', 'courses', 'lessons', 'announcements'));
    }
    

    public function create()
    {
        $teacher = auth('teacher')->user();
        
        // Load lesson_branch_course data for this teacher
        $lessonBranchCourses = \App\Models\LessonBranchCourse::where('teacher_id', $teacher->id)
            ->with(['branch:id,name', 'course:id,course_name', 'lesson:id,title,course_id'])
            ->get();
        
        // Get unique branches from lesson_branch_course
        $branches = $lessonBranchCourses->pluck('branch')->unique('id')->values();
        
        // Organize data by branch and course for JavaScript
        $branchCourseData = [];
        $branchLessonData = [];
        
        foreach ($lessonBranchCourses as $lbc) {
            $branchId = $lbc->branch_id;
            $courseId = $lbc->course_id;
            $lessonId = $lbc->lesson_id;
            
            // Build branch -> courses mapping
            if (!isset($branchCourseData[$branchId])) {
                $branchCourseData[$branchId] = [];
            }
            if (!in_array($courseId, $branchCourseData[$branchId])) {
                $branchCourseData[$branchId][] = $courseId;
            }
            
            // Build (branch, course) -> lessons mapping
            $key = "{$branchId}_{$courseId}";
            if (!isset($branchLessonData[$key])) {
                $branchLessonData[$key] = [];
            }
            $branchLessonData[$key][] = [
                'id' => $lessonId,
                'title' => $lbc->lesson->title,
            ];
        }
        
        // Get course details for all courses used
        $allCourseIds = array_unique(array_merge(...array_values($branchCourseData)));
        $courses = \App\Models\Course::whereIn('id', $allCourseIds)->get();
        
        // Get all lessons used
        $allLessonIds = [];
        foreach ($branchLessonData as $lessons) {
            foreach ($lessons as $lesson) {
                $allLessonIds[] = $lesson['id'];
            }
        }
        $lessons = \App\Models\Lesson::whereIn('id', array_unique($allLessonIds))->get();
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();

        return view('teacher.resources.create', compact(
            'branches', 
            'courses', 
            'lessons', 
            'branchCourseData',
            'branchLessonData',
            'announcements'
        ));
    }
    

    public function getLessons($teacherId, $courseId)
    {
        // Optional: Verify the course belongs to this teacher
        $teacher = auth('teacher')->user();
        if (!$teacher || $teacher->id != $teacherId) {
            return response()->json(['lessons' => []], 403);
        }
    
        // Fetch lessons for the selected course
        $lessons = Lesson::where('course_id', $courseId)
            ->select('id', 'title')
            ->get();
    
        return response()->json(['lessons' => $lessons]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'required|exists:lessons,id',
            'youtube_link' => 'nullable|url',
            'document_upload' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,xls,xlsx|max:10240', // 10MB max
            'document_name' => 'nullable|required_without:document_upload|url', // Required if no file upload
            'video_upload' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // 100MB max
        ]);

        $resourceData = [
            'teacher_id' => auth('teacher')->id(),
            'branch_id' => $request->branch_id,
            'course_id' => $request->course_id,
            'lesson_id' => $request->lesson_id,
            'youtube_link' => $request->youtube_link,
            'price' => 0.00, // Default price
            'is_free' => true, // Default to free
            'is_public' => false, // Default to private (admin controls visibility)
        ];

        // Handle document upload or link
        if ($request->hasFile('document_upload')) {
            $documentFile = $request->file('document_upload');
            $documentContent = file_get_contents($documentFile->getPathname());
            $resourceData['document_blob'] = base64_encode($documentContent);
            $resourceData['document_name'] = $documentFile->getClientOriginalName();
            $resourceData['document_mime'] = $documentFile->getMimeType();
        } elseif ($request->filled('document_name')) {
            $resourceData['document_name'] = $request->document_name;
            $resourceData['document_blob'] = null;
            $resourceData['document_mime'] = null;
        }

        // Handle video upload
        if ($request->hasFile('video_upload')) {
            $videoFile = $request->file('video_upload');
            $videoContent = file_get_contents($videoFile->getPathname());
            $resourceData['video_blob'] = base64_encode($videoContent);
            $resourceData['video_name'] = $videoFile->getClientOriginalName();
            $resourceData['video_mime'] = $videoFile->getMimeType();
        } else {
            $resourceData['video_blob'] = null;
            $resourceData['video_name'] = null;
            $resourceData['video_mime'] = null;
        }

        $resource = Resource::create($resourceData);
        
        // Note: Watermarks will be applied by admin when resource is made public
        // Teachers only upload content, admins control pricing and visibility

        return redirect()->route('teacher.resources.index')->with('success', 'Resource uploaded successfully!');
    }
//     public function show($id)
// {
//     $resource = Resource::with(['course:id,course_name', 'lesson:id,title'])
//         ->findOrFail($id);

//     return view('teacher.resources.show', compact('resource'));
// }
public function destroy($id)
{
    $teacher = auth('teacher')->user();
    $resource = Resource::where('id', $id)
        ->where('teacher_id', $teacher->id)
        ->firstOrFail();
    
    $resource->delete();

    return redirect()->route('teacher.resources.index')->with('success', 'Resource deleted successfully!');
}
public function downloadDocument($id)
{
    $resource = Resource::findOrFail($id);
    return response($resource->document_blob)
        ->header('Content-Type', $resource->document_mime)
        ->header('Content-Disposition', 'attachment; filename="'.$resource->document_name.'"');
}
public function downloadVideo($id)
{
    $resource = Resource::findOrFail($id);
    return response($resource->video_blob)
        ->header('Content-Type', $resource->video_mime)
        ->header('Content-Disposition', 'attachment; filename="'.$resource->video_name.'"');
}
public function streamVideo($id)
{
    $resource = Resource::findOrFail($id);
    return response($resource->video_blob)
        ->header('Content-Type', $resource->video_mime)
        ->header('Accept-Ranges', 'bytes');
}
public function viewDocument($id)
{
    $resource = Resource::findOrFail($id);
    return response($resource->document_blob)
        ->header('Content-Type', $resource->document_mime)
        ->header('Content-Disposition', 'inline; filename="'.$resource->document_name.'"');
}
public function edit($id)
{
    $teacher = auth('teacher')->user();
    $resource = Resource::where('id', $id)
        ->where('teacher_id', $teacher->id)
        ->firstOrFail();

    $branches = $teacher->branches;
    $courses = $teacher->courses()->with('lessons:id,course_id,title')->get();
    $lessons = Lesson::where('course_id', $resource->course_id)->get();
    
    // Get announcements for teacher layout
    $announcements = \App\Models\Announcement::latest()->take(5)->get();

    return view('teacher.resources.edit', compact('resource', 'branches', 'courses', 'lessons', 'announcements'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'branch_id' => 'required|exists:branches,id',
        'course_id' => 'required|exists:courses,id',
        'lesson_id' => 'required|exists:lessons,id',
        'youtube_link' => 'nullable|url',
        'document_upload' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,xls,xlsx|max:10240', // 10MB max
        'document_name' => 'nullable|url', // Optional for updates
        'video_upload' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // 100MB max
    ]);

    $resource = Resource::where('id', $id)
        ->where('teacher_id', auth('teacher')->id())
        ->firstOrFail();

    $resourceData = [
        'branch_id' => $request->branch_id,
        'course_id' => $request->course_id,
        'lesson_id' => $request->lesson_id,
        'youtube_link' => $request->youtube_link,
    ];

    // Handle document upload or link
    $documentUpdated = false;
    if ($request->hasFile('document_upload')) {
        $documentFile = $request->file('document_upload');
        $documentContent = file_get_contents($documentFile->getPathname());
        $resourceData['document_blob'] = base64_encode($documentContent);
        $resourceData['document_name'] = $documentFile->getClientOriginalName();
        $resourceData['document_mime'] = $documentFile->getMimeType();
        $documentUpdated = true;
    } elseif ($request->filled('document_name')) {
        $resourceData['document_name'] = $request->document_name;
        $resourceData['document_blob'] = null;
        $resourceData['document_mime'] = null;
        $documentUpdated = true;
    }
    // If neither file nor link provided, keep existing values

    // Handle video upload
    if ($request->hasFile('video_upload')) {
        $videoFile = $request->file('video_upload');
        $videoContent = file_get_contents($videoFile->getPathname());
        $resourceData['video_blob'] = base64_encode($videoContent);
        $resourceData['video_name'] = $videoFile->getClientOriginalName();
        $resourceData['video_mime'] = $videoFile->getMimeType();
    }
    // If no video uploaded, keep existing values

    $resource->update($resourceData);
    
    // Note: Watermarks and pricing are handled by admin
    // Teachers only upload and update content

    return redirect()->route('teacher.resources.index')->with('success', 'Resource updated successfully!');
}
}