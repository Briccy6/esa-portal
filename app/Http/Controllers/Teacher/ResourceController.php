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
    
        return view('teacher.resources.index', compact('resources', 'branches', 'courses', 'lessons'));
    }
    

    public function create()
    {
        $teacher = auth('teacher')->user();
        $branches = $teacher->branches; // Only branches assigned to this teacher
        $courses = $teacher->courses()->with('lessons:id,course_id,title')->get();
        $lessons = []; // Initialize as empty

        return view('teacher.resources.create', compact('courses', 'branches', 'lessons'));
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
            'document_name' => 'required|url', // Use document_name to store the link
        ]);

        Resource::create([
            'teacher_id' => auth('teacher')->id(),
            'branch_id' => $request->branch_id,
            'course_id' => $request->course_id,
            'lesson_id' => $request->lesson_id,
            'youtube_link' => $request->youtube_link,
            'document_name' => $request->document_name, // Store the link here
            'document_blob' => null,
            'document_mime' => null,
        ]);

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

    return view('teacher.resources.edit', compact('resource', 'branches', 'courses', 'lessons'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'branch_id' => 'required|exists:branches,id',
        'course_id' => 'required|exists:courses,id',
        'lesson_id' => 'required|exists:lessons,id',
        'youtube_link' => 'nullable|url',
        'document_name' => 'required|url',
    ]);

    $resource = Resource::findOrFail($id);
    $resource->update([
        'branch_id' => $request->branch_id,
        'course_id' => $request->course_id,
        'lesson_id' => $request->lesson_id,
        'youtube_link' => $request->youtube_link,
        'document_name' => $request->document_name,
    ]);

    return redirect()->route('teacher.resources.index')->with('success', 'Resource updated successfully!');
}
}