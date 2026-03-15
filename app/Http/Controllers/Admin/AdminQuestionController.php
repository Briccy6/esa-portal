<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class AdminQuestionController extends Controller
{
    public function index($exam)
    {
        $exam = Exam::findOrFail($exam);
        $exam->load(['questions', 'questions.questionBank']);
        
        return view('admin.questions.index', compact('exam'));
    }
    
    public function create($exam)
    {
        $exam = Exam::findOrFail($exam);
        
        return view('admin.questions.create', compact('exam'));
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
