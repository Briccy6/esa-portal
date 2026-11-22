<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Announcement;
use App\Models\StudentAnnouncementRead;
use App\Models\CourseTermBranchFee;
use App\Models\StudentOptionalFeeResponse;
use Illuminate\Support\Facades\DB;

class StudentNotificationService
{
    /**
     * Get all notifications for a student
     */
    public function getNotifications(Student $student): array
    {
        $notifications = [];

        // Get unread announcements
        $unreadAnnouncements = $this->getUnreadAnnouncements($student);
        if (!empty($unreadAnnouncements)) {
            $notifications['announcements'] = $unreadAnnouncements;
        }

        // Get pending mandatory optional fees
        $pendingMandatoryFees = $this->getPendingMandatoryFees($student);
        if (!empty($pendingMandatoryFees)) {
            $notifications['mandatory_fees'] = $pendingMandatoryFees;
        }

        return $notifications;
    }

    /**
     * Get unread announcements for a student
     */
    private function getUnreadAnnouncements(Student $student): array
    {
        $readAnnouncementIds = StudentAnnouncementRead::where('student_id', $student->id)
            ->pluck('announcement_id')
            ->toArray();

        return Announcement::where(function ($query) {
                $query->where('audience', 'students')
                    ->orWhere('audience', 'all')
                    ->orWhere('audience', 'public');
            })
            ->whereNotIn('id', $readAnnouncementIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'attachment' => $announcement->attachment,
                    'sender' => $announcement->sender,
                    'created_at' => $announcement->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    /**
     * Get pending mandatory optional fees for a student
     */
    private function getPendingMandatoryFees(Student $student): array
    {
        // Get the student's course, branch, and academic year
        $studentCourseId = $student->course_id;
        $studentBranchId = $student->branch_id;
        $studentAcademicYearId = $student->academic_year_id;

        if (!$studentCourseId || !$studentBranchId || !$studentAcademicYearId) {
            return [];
        }

        // Get mandatory optional fees for the student's course, branch, and academic year
        $mandatoryFees = CourseTermBranchFee::where('course_id', $studentCourseId)
            ->where('branch_id', $studentBranchId)
            ->whereHas('term', function ($query) use ($studentAcademicYearId) {
                $query->where('academic_year_id', $studentAcademicYearId);
            })
            ->where(function ($query) {
                $query->where('is_practical_mandatory', true)
                    ->orWhere('is_trip_mandatory', true);
            })
            ->where('notify_students', true)
            ->get();

        $pendingFees = [];

        foreach ($mandatoryFees as $fee) {
            // Check if student has already responded
            $response = StudentOptionalFeeResponse::where('student_id', $student->id)
                ->where('course_term_branch_fee_id', $fee->id)
                ->first();

            if (!$response || $response->response === 'pending') {
                $feeType = null;
                if ($fee->is_practical_mandatory && $fee->practical_fee > 0) {
                    $feeType = 'practical';
                } elseif ($fee->is_trip_mandatory && $fee->trip_fee > 0) {
                    $feeType = 'trip';
                }

                if ($feeType) {
                    $pendingFees[] = [
                        'id' => $fee->id,
                        'fee_type' => $feeType,
                        'amount' => $feeType === 'practical' ? $fee->practical_fee : $fee->trip_fee,
                        'due_date' => $feeType === 'practical' ? $fee->practical_due_date?->format('Y-m-d') : $fee->trip_due_date?->format('Y-m-d'),
                        'course_name' => $fee->course->course_name ?? 'N/A',
                        'term_name' => $fee->term->name ?? 'N/A',
                    ];
                }
            }
        }

        return $pendingFees;
    }

    /**
     * Mark announcement as read
     */
    public function markAnnouncementAsRead(Student $student, int $announcementId): void
    {
        StudentAnnouncementRead::updateOrCreate(
            [
                'student_id' => $student->id,
                'announcement_id' => $announcementId,
            ],
            [
                'read_at' => now(),
            ]
        );
    }

    /**
     * Mark all announcements as read
     */
    public function markAllAnnouncementsAsRead(Student $student): void
    {
        $unreadAnnouncementIds = Announcement::where(function ($query) {
                $query->where('audience', 'students')
                    ->orWhere('audience', 'all')
                    ->orWhere('audience', 'public');
            })
            ->whereNotIn('id', function ($query) use ($student) {
                $query->select('announcement_id')
                    ->from('student_announcement_reads')
                    ->where('student_id', $student->id);
            })
            ->pluck('id');

        foreach ($unreadAnnouncementIds as $announcementId) {
            $this->markAnnouncementAsRead($student, $announcementId);
        }
    }
}
