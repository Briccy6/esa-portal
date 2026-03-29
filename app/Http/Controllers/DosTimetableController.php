<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Student;
use App\Mail\TimetableCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DosTimetableController extends Controller
{
    public function create()
    {
        return view('staff.timetable.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);

        $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));

        $timetable = Timetable::create([
            'title' => $request->title,
            'image' => $imageData,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
        ]);

        // Send email notifications to all students
        try {
            $students = Student::all(); // Get all students since there's no status column
            $emailCount = 0;
            
            foreach ($students as $student) {
                if ($student->email) {
                    try {
                        Mail::to($student->email)->send(new TimetableCreatedMail($timetable, $student));
                        $emailCount++;
                    } catch (\Exception $e) {
                        Log::warning("Failed to send timetable email to student {$student->id}: " . $e->getMessage());
                    }
                }
            }
            
            Log::info("Timetable {$timetable->id} created and notifications sent to {$emailCount} students");
            
        } catch (\Exception $e) {
            Log::error("Failed to send timetable notifications: " . $e->getMessage());
            // Don't fail the request if emails fail
        }

        return redirect()->route('dos.timetable.create')->with('success', 
            "Timetable uploaded successfully! " . 
            (isset($emailCount) && $emailCount > 0 ? "Email notifications sent to {$emailCount} students." : "")
        );
    }

    public function index()
    {
        $timetables = \App\Models\Timetable::orderBy('created_at', 'desc')->get();
        return view('staff.timetable.index', compact('timetables'));
    }

    public function edit($id)
    {
        $timetable = \App\Models\Timetable::findOrFail($id);
        return view('staff.timetable.edit', compact('timetable'));
    }

    public function update(Request $request, $id)
    {
        $timetable = \App\Models\Timetable::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);

        if ($request->hasFile('image')) {
            $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $timetable->image = $imageData;
        }

        $timetable->title = $request->title;
        $timetable->valid_from = $request->valid_from;
        $timetable->valid_until = $request->valid_until;
        $timetable->save();

        return redirect()->route('dos.timetable.index')->with('success', 'Timetable updated successfully!');
    }
}
