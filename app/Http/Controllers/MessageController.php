<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::latest()->paginate(10);
        $view = $this->getViewPrefix() . 'messages.index';
        return view($view, compact('messages'));
    }

    public function create()
    {
        $view = $this->getViewPrefix() . 'messages.create';
        return view($view);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:students,staff,public',
            'file' => 'nullable|file|max:10240' // 10MB max
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('messages', 'public');
        }

        Message::create([
            'title' => $request->title,
            'content' => $request->content,
            'recipient_type' => $request->recipient_type,
            'file_path' => $filePath,
        ]);

        $route = $this->getRoutePrefix() . 'messages.index';
        return redirect()->route($route)->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        $view = $this->getViewPrefix() . 'messages.show';
        return view($view, compact('message'));
    }

    public function destroy(Message $message)
    {
        if ($message->file_path) {
            Storage::disk('public')->delete($message->file_path);
        }

        $message->delete();
        $route = $this->getRoutePrefix() . 'messages.index';
        return redirect()->route($route)->with('success', 'Message deleted.');
    }

    /**
     * Helper to get the view prefix based on user type.
     */
    protected function getViewPrefix()
    {
        if (auth('teacher')->check()) {
            return 'teacher.';
        }
        if (auth('staff')->check()) {
            return 'staff.';
        }
        return 'admin.';
    }

    /**
     * Helper to get the route prefix based on user type.
     */
    protected function getRoutePrefix()
    {
        if (auth('teacher')->check()) {
            return 'teacher.';
        }
        if (auth('staff')->check()) {
            return 'staff.';
        }
        return 'admin.';
    }
}
