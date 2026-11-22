<?php

namespace App\Http\Controllers;

use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(StudentNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications for the current student
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        $notifications = $this->notificationService->getNotifications($student);

        return response()->json($notifications);
    }

    /**
     * Mark announcement as read
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|integer|exists:announcements,id',
        ]);

        $student = Auth::guard('student')->user();
        $this->notificationService->markAnnouncementAsRead($student, $request->announcement_id);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all announcements as read
     */
    public function markAllAsRead()
    {
        $student = Auth::guard('student')->user();
        $this->notificationService->markAllAnnouncementsAsRead($student);

        return response()->json(['success' => true]);
    }
}
