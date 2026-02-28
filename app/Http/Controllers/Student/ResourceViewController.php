<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ResourceViewController extends Controller
{
    public function index() {
        $student = auth('student')->user();
        $student->load('course'); // Load the single course

        $resources = Resource::with(['course', 'lesson'])->get();
        $course = $student->course; // Get the single course

        return view('students.resources.index', compact('resources', 'student', 'course'));
    }
    

    public function show(Resource $resource, Request $request)
    {
        $student = auth('student')->user();

        if (!$student) {
            abort(403, 'You must be logged in as a student.');
        }

        // Ensure courses relation is loaded to avoid null
        $student->loadMissing('courses');

        // Defensive check in case courses is empty or null
        if (!$student->courses || !$student->courses->contains('id', $resource->course_id)) {
            abort(403, 'Unauthorized to view this resource.');
        }

        // Decide which file to serve (video preferred)
        if ($resource->video_path && Storage::disk('public')->exists($resource->video_path)) {
            $path = $resource->video_path;
            $mime = Storage::disk('public')->mimeType($path);
        } elseif ($resource->document_path && Storage::disk('public')->exists($resource->document_path)) {
            $path = $resource->document_path;
            $mime = Storage::disk('public')->mimeType($path);
        } else {
            abort(404, 'Resource file not found.');
        }

        // Serve file inline (e.g., video or PDF in browser)
        return Storage::disk('public')->response($path, null, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
}
