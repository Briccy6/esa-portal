<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class TeacherQuestionController extends Controller
{
    public function index($exam)
    {
        $exam = Exam::findOrFail($exam);
        $exam->load(['questions', 'questions.questionBank']);
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();
        
        return view('teacher.questions.index', compact('exam', 'announcements'));
    }
    
    public function create($exam)
    {
        $exam = Exam::findOrFail($exam);
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();
        
        return view('teacher.questions.create', compact('exam', 'announcements'));
    }
    
    public function store(Request $request, $exam)
    {
        // Use the same logic as the main ExamQuestionController
        // This ensures consistency across all user types
        return app('App\Http\Controllers\ExamQuestionController')->store($request, $exam);
    }
    
    public function import(Request $request, $exam)
    {
        // Use the same logic as the main ExamQuestionController
        return app('App\Http\Controllers\ExamQuestionController')->import($request, $exam);
    }
}
