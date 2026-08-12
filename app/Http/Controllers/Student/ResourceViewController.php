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

        // Get resources for the student's course and branch (same logic as dashboard)
        $resources = \App\Models\Resource::where(function($query) use ($student) {
            $query->orWhere('course_id', $student->course_id)
                  ->orWhere('branch_id', $student->branch_id);
        })
        ->with(['teacher', 'course', 'branch'])
        ->orderByDesc('created_at')
        ->get();

        $course = $student->course; // Now this will work

        return view('students.resources.index', compact('resources', 'student', 'course'));
    }
    

    public function show(Resource $resource, Request $request)
    {
        $student = auth('student')->user();

        if (!$student) {
            abort(403, 'You must be logged in as a student.');
        }

        $student->loadMissing('course');

        if (!$student->course || $student->course->id != $resource->course_id) {
            abort(403, 'Unauthorized to view this resource.');
        }

        return view('students.resources.show', compact('resource', 'student'));
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
        if ($resource->video_blob) {
            $content = $resource->video_blob;
            $filename = $resource->video_name ?: 'video.mp4';
        } elseif ($resource->document_blob) {
            $content = $resource->document_blob;
            $filename = $resource->document_name ?: 'document.pdf';
        } else {
            abort(404, 'Resource file not found.');
        }

        // Serve file as download
        return response($content)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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
