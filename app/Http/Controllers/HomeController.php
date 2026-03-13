<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Course; // Make sure you import your Course model
use App\Models\Announcement; // Make sure you import your Announcement model

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Find the ID of the ongoing academic year
    $ongoingAcademicYearId = AcademicYear::where('is_ongoing', true)->value('id');

    $courses = [];
    if ($ongoingAcademicYearId) {
        // Fetch courses for the ongoing academic year
        $courses = Course::where('academic_year_id', $ongoingAcademicYearId)
                           ->orderBy('course_name', 'asc')
                           ->get();
    }

    // Fetch recent public announcements
    $publicAnnouncements = Announcement::where('is_public', true)
                                       ->orderBy('created_at', 'desc')
                                       ->take(5)
                                       ->get();
    
    // This part is crucial - it sends the variables to your view
    return view('welcome', [
        'courses' => $courses,
        'publicAnnouncements' => $publicAnnouncements,
    ]);
    }

    /**
     * Handle the contact form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $details = [
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ];

        // Send email to the school's address
        // Ensure your .env file is configured for email sending
        try {
            Mail::to('excellentservantacademy@gmail.com')->send(new \App\Mail\ContactFormMail($details));

            // Add this to a Mailable: App/Mail/ContactFormMail.php as shown in the previous response

            return back()->with('success', 'Your message has been sent successfully!');

        } catch (\Exception $e) {
            // You can log the error here if you want
            return back()->with('error', 'Sorry, there was an issue sending your message. Please try again later.');
        }
    }
}