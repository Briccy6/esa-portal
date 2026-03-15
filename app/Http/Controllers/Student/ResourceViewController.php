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
        $student->load('course'); // Load the course relationship

        $resources = Resource::with(['course', 'lesson'])->get();
        $course = $student->course; // Now this will work

        return view('students.resources.index', compact('resources', 'student', 'course'));
    }
    

    public function show(Resource $resource, Request $request)
    {
        $student = auth('student')->user();

        if (!$student) {
            abort(403, 'You must be logged in as a student.');
        }

        // Ensure course relation is loaded to avoid null
        $student->loadMissing('course');

        // Defensive check in case course is null
        if (!$student->course || $student->course->id != $resource->course_id) {
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

    public function download(Resource $resource, Request $request)
    {
        $student = auth('student')->user();

        if (!$student) {
            abort(403, 'You must be logged in as a student.');
        }

        // Check if resource is free or if student has access
        if (!$resource->is_free) {
            // Redirect to payment if not free
            return redirect()->route('student.resources.payment', $resource->id);
        }

        // Ensure course relation is loaded to avoid null
        $student->loadMissing('course');

        // Defensive check in case course is null
        if (!$student->course || $student->course->id != $resource->course_id) {
            abort(403, 'Unauthorized to download this resource.');
        }

        // Decide which file to serve (video preferred)
        if ($resource->video_path && Storage::disk('public')->exists($resource->video_path)) {
            $path = $resource->video_path;
        } elseif ($resource->document_path && Storage::disk('public')->exists($resource->document_path)) {
            $path = $resource->document_path;
        } else {
            abort(404, 'Resource file not found.');
        }

        // Serve file as download
        return Storage::disk('public')->download($path, basename($path));
    }

    public function payment(Resource $resource, Request $request)
    {
        $student = auth('student')->user();

        if (!$student) {
            abort(403, 'You must be logged in as a student.');
        }

        // Ensure course relation is loaded to avoid null
        $student->loadMissing('course');

        // Defensive check in case course is null
        if (!$student->course || $student->course->id != $resource->course_id) {
            abort(403, 'Unauthorized to access this resource.');
        }

        // If resource is free, redirect to download
        if ($resource->is_free) {
            return redirect()->route('student.resources.download', $resource->id);
        }

        return view('students.resources.payment', compact('resource', 'student'));
    }
}
