<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search branches
     */
    public function branches(Request $request)
    {
        $query = $request->get('q');
        
        $branches = Branch::when($query, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->select('id', 'name as text')
            ->limit(10)
            ->get();
            
        return response()->json($branches);
    }

    /**
     * Search courses
     */
    public function courses(Request $request)
    {
        $query = $request->get('q');
        
        $courses = Course::when($query, function ($query, $search) {
                return $query->where('course_name', 'like', "%{$search}%");
            })
            ->select('id', 'course_name as text')
            ->limit(10)
            ->get();
            
        return response()->json($courses);
    }

    /**
     * Search lessons (filtered by courses)
     */
    public function lessons(Request $request)
    {
        $query = $request->get('q');
        $courseIds = $request->get('course_ids', []);
        
        $lessons = Lesson::when(count($courseIds) > 0, function ($query) use ($courseIds) {
                return $query->whereIn('course_id', $courseIds);
            })
            ->when($query, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->select('id', 'title as text', 'course_id')
            ->limit(10)
            ->get();
            
        return response()->json($lessons);
    }

    /**
     * Search teachers
     */
    public function teachers(Request $request)
    {
        $query = $request->get('q');
        
        $teachers = Teacher::when($query, function ($query, $search) {
                return $query->where('teacher_name', 'like', "%{$search}%");
            })
            ->select('id', 'teacher_name as text')
            ->limit(10)
            ->get();
            
        return response()->json($teachers);
    }
}
