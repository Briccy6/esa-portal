<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->q;
        $courses = Course::where('name', 'like', '%' . $search . '%')
                         ->select('id', 'name as text')
                         ->limit(10)
                         ->get();
        return response()->json($courses);
    }
}
