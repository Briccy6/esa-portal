<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientSettingsController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        
        return view('client.settings.index', compact('client'));
    }

    public function update(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'email_notifications' => 'boolean',
            'exam_reminders' => 'boolean',
            'dark_mode' => 'boolean',
        ]);

        $client->update($request->only([
            'email_notifications',
            'exam_reminders', 
            'dark_mode',
        ]));

        return back()->with('success', 'Settings updated successfully!');
    }
}
