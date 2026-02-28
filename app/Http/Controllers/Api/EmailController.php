<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendBulkEmail(Request $request)
    {
        $request->validate([
            'emails' => 'required|array',
            'emails.*' => 'email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        foreach ($request->emails as $email) {
            Mail::raw($request->message, function ($msg) use ($email, $request) {
                $msg->to($email)->subject($request->subject);
            });
        }

        return response()->json(['status' => 'Emails sent']);
    }
}
