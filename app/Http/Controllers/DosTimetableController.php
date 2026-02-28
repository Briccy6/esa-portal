<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;

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

        Timetable::create([
            'title' => $request->title,
            'image' => $imageData,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
        ]);

        return redirect()->route('dos.timetable.create')->with('success', 'Timetable uploaded successfully!');
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
