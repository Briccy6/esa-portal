<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            // Keep in sync with the *actual* DB enum values in production.
            // (Some deployments only allow: public, students, staff)
            'audience' => 'required|in:students,staff,public',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:2048',
            'sender' => 'nullable|string|max:255',
        ]);

        // Normalize any legacy/new UI values to the DB-supported enum set.
        // If the UI ever sends teachers/admin/all, store them under staff/public.
        $data['audience'] = match ($data['audience']) {
            'teachers', 'admin', 'staff' => 'staff',
            'all' => 'public',
            default => $data['audience'],
        };

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('announcements', 'public');
        }

        Announcement::create($data);
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement sent successfully.');
    }

   public function edit($id)
{
    $announcement = Announcement::findOrFail($id);
    return view('admin.announcements.edit', compact('announcement'));
}


    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            // Keep in sync with the *actual* DB enum values in production.
            // (Some deployments only allow: public, students, staff)
            'audience' => 'required|in:students,staff,public',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:2048',
            'sender' => 'nullable|string|max:255',
        ]);

        // Normalize any legacy/new UI values to the DB-supported enum set.
        $data['audience'] = match ($data['audience']) {
            'teachers', 'admin', 'staff' => 'staff',
            'all' => 'public',
            default => $data['audience'],
        };

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('announcements', 'public');
        }

        $announcement->update($data);
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted.');
    }
}
