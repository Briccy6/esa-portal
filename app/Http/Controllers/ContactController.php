<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:5120', // 5MB max
        ]);

        $data = $request->only(['username', 'phone', 'email', 'subject', 'content']);

        Mail::send('emails.contact_admin', $data, function ($message) use ($request) {
            $message->to(config('mail.from.address'), config('mail.from.name'))
                ->subject('[Contact Us] ' . $request->subject)
                ->replyTo($request->email, $request->username);

            if ($request->hasFile('attachment')) {
                $message->attach($request->file('attachment')->getRealPath(), [
                    'as' => $request->file('attachment')->getClientOriginalName(),
                    'mime' => $request->file('attachment')->getMimeType(),
                ]);
            }
        });

        return back()->with('success', 'Your message has been sent!');
    }
}