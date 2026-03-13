<?php
// app/Http/Controllers/TimetableSessionController.php

namespace App\Http\Controllers;

use App\Models\TimetableSession;
use Illuminate\Http\Request;

class TimetableSessionController extends Controller
{
    // List all timetable sessions (optional, implement if needed)
    public function index()
    {
        $sessions = TimetableSession::with(['lesson', 'timetable'])->paginate(15);
        return view('timetablesession.index', compact('sessions'));
    }

    // Show form to create new timetable session manually
    public function create()
    {
        // You will likely need lessons and timetables to assign sessions
        $lessons = \App\Models\Lesson::all();
        $timetables = \App\Models\Timetable::all();

        return view('timetablesession.create', compact('lessons', 'timetables'));
    }

    // Store a new timetable session
    public function store(Request $request)
    {
        $request->validate([
            'timetable_id' => 'required|exists:timetables,id',
            'lesson_id' => 'required|exists:lessons,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255'
        ]);

        // Validate minimum 3 hours for session
        $start = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $end = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
        if ($end->diffInHours($start) < 3) {
            return back()->withErrors(['end_time' => 'Each lesson must be at least 3 hours long.'])->withInput();
        }

        TimetableSession::create($request->only([
            'timetable_id',
            'lesson_id',
            'day_of_week',
            'start_time',
            'end_time',
            'classroom',
            'notes'
        ]));

        return redirect()->route('timetablesession.index')
            ->with('success', 'Timetable session created successfully!');
    }

    // Display a specific timetable session
    public function show(TimetableSession $timetablesession)
    {
        $timetablesession->load(['lesson', 'timetable']);
        return view('timetablesession.show', compact('timetablesession'));
    }

    // Show form to edit timetable session
}
