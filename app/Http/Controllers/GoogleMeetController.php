<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use App\Models\LiveClass;

class GoogleMeetController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        return redirect($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope(Google_Service_Calendar::CALENDAR);

       $token = $client->fetchAccessTokenWithAuthCode($request->code);
session(['google_token' => $token]); // This is fine as long as $token is an array

        return redirect()->route('staff.dashboard')->with('success', 'Google account connected!');
    }

   public function createMeet(Request $request)
{
    $client = new Google_Client();
    $client->setClientId(env('GOOGLE_CLIENT_ID'));
    $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
    $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
    $client->addScope(Google_Service_Calendar::CALENDAR);

    $googleToken = session('google_token');

    // Fix: Ensure token is valid JSON or array
    if (is_string($googleToken)) {
        $googleToken = json_decode($googleToken, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->route('google.redirect')->with('error', 'Invalid token format. Please reconnect Google.');
        }
    }

    if (!is_array($googleToken)) {
        return redirect()->route('google.redirect')->with('error', 'Google token missing or corrupted.');
    }

    $client->setAccessToken($googleToken);

    if ($client->isAccessTokenExpired()) {
        return redirect()->route('google.redirect')->with('error', 'Access token expired. Please reconnect Google.');
    }

    $branch = \App\Models\Branch::find($request->branch_id);
    $course = \App\Models\Course::find($request->course_id);
    $lesson = \App\Models\Lesson::find($request->lesson_id);

    $event = new \Google_Service_Calendar_Event([
        'summary' => $request->input('summary', 'Live Class'),
        'description' => "Branch: " . ($branch?->name ?? '') . "\nCourse: " . ($course?->course_name ?? '') . "\nLesson: " . ($lesson?->title ?? ''),
        'start' => ['dateTime' => $request->input('start'), 'timeZone' => 'Africa/Kigali'],
        'end' => ['dateTime' => $request->input('end'), 'timeZone' => 'Africa/Kigali'],
        'conferenceData' => [
            'createRequest' => [
                'requestId' => uniqid(),
                'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
            ],
        ],
    ]);

    $service = new \Google_Service_Calendar($client);
    $createdEvent = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
    $meetLink = $createdEvent->getHangoutLink();

    // Save to database
    LiveClass::create([
        'summary' => $request->input('summary', 'Live Class'),
        'meet_link' => $meetLink,
        'start_time' => $request->input('start'),
        'end_time' => $request->input('end'),
        'branch_id' => $request->branch_id,
        'course_id' => $request->course_id,
        'lesson_id' => $request->lesson_id,
    ]);

    return back()->with('meet_link', $meetLink);
}

}
