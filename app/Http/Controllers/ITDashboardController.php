<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\DismissedStudent;
use App\Models\Branch;
use App\Models\AcademicYear;
use App\Models\AccountLockStatus;
use App\Models\Admin;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AcademicYearContext;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ITDashboardController extends Controller
{
    /**
     * Display the IT dashboard
     */
    public function index(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        // Get selected academic year
        $selectedAcademicYear = AcademicYearContext::selected();
        $academicYearId = $selectedAcademicYear?->id;

        // Get filter options
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $branches = $staff->branches;

        // Build base query with filters
        $studentQuery = Student::with(['branch', 'course', 'academicYear'])
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id));

        // Get dismissed student IDs to exclude from active lists
        $dismissedStudentIds = DismissedStudent::pluck('original_student_id')->toArray();

        // Statistics
        $totalStudents = (clone $studentQuery)->whereNotIn('id', $dismissedStudentIds)->count();
        $blockedStudents = (clone $studentQuery)->where('is_blocked', true)->whereNotIn('id', $dismissedStudentIds)->count();
        $dismissedStudentsCount = DismissedStudent::when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->count();

        // Profile completion statistics
        $baseQuery = (clone $studentQuery)->whereNotIn('id', $dismissedStudentIds);
        $completedProfileCount = (clone $baseQuery)
            ->whereNotNull('email')
            ->whereNotNull('phone')
            ->whereNotNull('sex')
            ->whereNotNull('identification_type')
            ->whereNotNull('id_number')
            ->where('email', '!=', '')
            ->where('phone', '!=', '')
            ->where('sex', '!=', '')
            ->where('identification_type', '!=', '')
            ->where('id_number', '!=', '')
            ->count();

        $incompleteProfileCount = $totalStudents - $completedProfileCount;

        // Get students lists
        $blockedStudentsList = (clone $studentQuery)
            ->where('is_blocked', true)
            ->whereNotIn('id', $dismissedStudentIds)
            ->orderBy('blocked_at', 'desc')
            ->get();

        $dismissedStudentsList = DismissedStudent::with(['branch', 'course'])
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->orderBy('dismissal_date', 'desc')
            ->get();

        $incompleteProfileStudents = (clone $studentQuery)
            ->whereNotIn('id', $dismissedStudentIds)
            ->where(function($q) {
                $q->whereNull('email')
                    ->orWhere('email', '')
                    ->orWhereNull('phone')
                    ->orWhere('phone', '')
                    ->orWhereNull('sex')
                    ->orWhere('sex', '')
                    ->orWhereNull('identification_type')
                    ->orWhere('identification_type', '')
                    ->orWhereNull('id_number')
                    ->orWhere('id_number', '');
            })
            ->orderBy('full_name')
            ->get();

        $completedProfileStudents = (clone $studentQuery)
            ->whereNotIn('id', $dismissedStudentIds)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->whereNotNull('sex')
            ->where('sex', '!=', '')
            ->whereNotNull('identification_type')
            ->where('identification_type', '!=', '')
            ->whereNotNull('id_number')
            ->where('id_number', '!=', '')
            ->orderBy('full_name')
            ->get();

        // Statistics for KPI cards
        $stats = [
            'total_students' => $totalStudents,
            'blocked_students' => $blockedStudents,
            'dismissed_students' => $dismissedStudentsCount,
            'profile_completed' => $completedProfileCount,
            'profile_incomplete' => $incompleteProfileCount,
        ];

        $activeSince = now()->subMinutes(15);
        $activityRoles = [
            'student' => [
                'label' => 'Students',
                'model' => Student::class,
                'name' => 'full_name',
            ],
            'staff' => [
                'label' => 'Staff',
                'model' => \App\Models\Staff::class,
                'name' => 'name',
            ],
            'teacher' => [
                'label' => 'Teachers',
                'model' => Teacher::class,
                'name' => 'teacher_name',
            ],
            'admin' => [
                'label' => 'Administrators',
                'model' => Admin::class,
                'name' => null,
            ],
        ];

        $activityGroups = [];
        foreach ($activityRoles as $guard => $role) {
            $model = $role['model'];
            $latestActivities = DB::table('user_activity_statuses')
                ->where('guard', $guard)
                ->select('user_id', 'last_active_at')
                ->orderByDesc('last_active_at')
                ->limit(6)
                ->get();

            $accounts = $model::whereIn('id', $latestActivities->pluck('user_id'))
                ->get()
                ->keyBy('id');

            $people = $latestActivities->map(function ($activity) use ($accounts, $role, $activeSince) {
                $account = $accounts->get($activity->user_id);
                if (!$account) {
                    return null;
                }

                $lastActiveAt = Carbon::parse($activity->last_active_at);
                $name = $role['name']
                    ? $account->{$role['name']}
                    : trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? ''));

                return [
                    'name' => $name ?: 'Unnamed account',
                    'email' => $account->email ?? null,
                    'last_active_at' => $lastActiveAt,
                    'is_active' => $lastActiveAt->greaterThanOrEqualTo($activeSince),
                ];
            })->filter()->values();

            $activityGroups[$guard] = [
                'label' => $role['label'],
                'total' => $model::count(),
                'active' => DB::table('user_activity_statuses')
                    ->where('guard', $guard)
                    ->where('last_active_at', '>=', $activeSince)
                    ->count(),
                'people' => $people,
            ];
        }

        $popularPages = DB::table('user_activity_events')
            ->where('activity_date', '>=', now()->subDays(29)->toDateString())
            ->select('route_name')
            ->selectRaw('SUM(visits) as visits')
            ->groupBy('route_name')
            ->orderByDesc('visits')
            ->limit(8)
            ->get()
            ->map(function ($page) {
                $page->label = Str::of($page->route_name)
                    ->replace(['.', '_', '-'], ' ')
                    ->title()
                    ->toString();

                return $page;
            });

        $activityDates = collect(range(6, 0))->map(
            fn ($daysAgo) => now()->subDays($daysAgo)->toDateString()
        );
        $activityChartLabels = $activityDates->map(
            fn ($date) => Carbon::parse($date)->format('M j')
        )->values();
        $dailyActivity = DB::table('user_activity_events')
            ->whereBetween('activity_date', [$activityDates->first(), $activityDates->last()])
            ->select('activity_date', 'guard')
            ->selectRaw('COUNT(DISTINCT user_id) as users')
            ->groupBy('activity_date', 'guard')
            ->get()
            ->keyBy(fn ($row) => $row->activity_date . ':' . $row->guard);

        $activityChartDatasets = collect($activityRoles)->map(function ($role, $guard) use ($activityDates, $dailyActivity) {
            return [
                'label' => $role['label'],
                'data' => $activityDates->map(
                    fn ($date) => (int) ($dailyActivity->get($date . ':' . $guard)->users ?? 0)
                )->values(),
            ];
        })->values();

        $lockedAccounts = AccountLockStatus::locked()
            ->latest('locked_at')
            ->limit(20)
            ->get()
            ->map(function ($lockStatus) {
                $lockStatus->account = $lockStatus->getUser();

                return $lockStatus;
            });

        return view('staff.it_dashboard', compact(
            'stats',
            'blockedStudentsList',
            'dismissedStudentsList',
            'incompleteProfileStudents',
            'completedProfileStudents',
            'academicYears',
            'branches',
            'selectedAcademicYear',
            'activityGroups',
            'popularPages',
            'activityDates',
            'activityChartLabels',
            'activityChartDatasets',
            'lockedAccounts'
        ));
    }

    /**
     * Download NESA registration documents as ZIP
     */
    public function downloadNesaDocuments(Request $request)
    {
        $request->validate([
            'student_category' => 'required|in:all,completed,incomplete,selected',
            'student_ids' => 'array',
            'student_ids.*' => 'exists:students,id',
            'documents' => 'required|array',
            'documents.*' => 'in:name,email,phone,id_number,id_photo,passport_photo,result_slip,passport',
        ]);

        $studentCategory = $request->student_category;
        $selectedDocuments = $request->documents;

        // Get dismissed student IDs to exclude
        $dismissedStudentIds = DismissedStudent::pluck('original_student_id')->toArray();

        // Build student query based on category
        $studentQuery = Student::with(['branch', 'course'])
            ->whereNotIn('id', $dismissedStudentIds);

        if ($studentCategory === 'completed') {
            $studentQuery->whereNotNull('email')
                ->where('email', '!=', '')
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->whereNotNull('sex')
                ->where('sex', '!=', '')
                ->whereNotNull('identification_type')
                ->where('identification_type', '!=', '')
                ->whereNotNull('id_number')
                ->where('id_number', '!=', '');
        } elseif ($studentCategory === 'incomplete') {
            $studentQuery->where(function($q) {
                $q->whereNull('email')
                    ->orWhere('email', '')
                    ->orWhereNull('phone')
                    ->orWhere('phone', '')
                    ->orWhereNull('sex')
                    ->orWhere('sex', '')
                    ->orWhereNull('identification_type')
                    ->orWhere('identification_type', '')
                    ->orWhereNull('id_number')
                    ->orWhere('id_number', '');
            });
        } elseif ($studentCategory === 'selected' && $request->has('student_ids')) {
            $studentQuery->whereIn('id', $request->student_ids);
        }

        $students = $studentQuery->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No students found for download.');
        }

        $zip = new ZipArchive();
        $zipFileName = 'nesa_registration_documents_' . now()->format('Y-m-d_His') . '.zip';
        $tempDir = storage_path('app/temp');

        // Ensure temp directory exists
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = $tempDir . '/' . $zipFileName;

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            return back()->with('error', 'Could not create ZIP file.');
        }

        foreach ($students as $student) {
            $studentFolderName = $this->sanitizeFolderName($student->full_name);
            $folderPath = $studentFolderName . '/';

            // Add text file with student info
            $infoContent = "Student Information\n";
            $infoContent .= "=================\n";

            if (in_array('name', $selectedDocuments)) {
                $infoContent .= "Name: {$student->full_name}\n";
            }
            if (in_array('email', $selectedDocuments)) {
                $infoContent .= "Email: {$student->email}\n";
            }
            if (in_array('phone', $selectedDocuments)) {
                $infoContent .= "Phone: {$student->phone}\n";
            }
            if (in_array('id_number', $selectedDocuments)) {
                $infoContent .= "ID Number: {$student->id_number}\n";
            }

            $zip->addFromString($folderPath . 'student_info.txt', $infoContent);

            // Add ID photo
            if (in_array('id_photo', $selectedDocuments) && !empty($student->id_photo)) {
                $idPhotoPath = $this->getFilePath($student->id_photo);
                if ($idPhotoPath && file_exists($idPhotoPath)) {
                    $zip->addFile($idPhotoPath, $folderPath . 'id_photo' . $this->getFileExtension($student->id_photo));
                }
            }

            // Add passport photo
            if (in_array('passport_photo', $selectedDocuments) && !empty($student->passport_photo)) {
                $passportPhotoPath = $this->getFilePath($student->passport_photo);
                if ($passportPhotoPath && file_exists($passportPhotoPath)) {
                    $zip->addFile($passportPhotoPath, $folderPath . 'passport_photo' . $this->getFileExtension($student->passport_photo));
                }
            }

            // Add result slip (senior6_document or senior3_document)
            if (in_array('result_slip', $selectedDocuments)) {
                if (!empty($student->senior6_document)) {
                    $resultPath = $this->getFilePath($student->senior6_document);
                    if ($resultPath && file_exists($resultPath)) {
                        $zip->addFile($resultPath, $folderPath . 'result_slip_s6' . $this->getFileExtension($student->senior6_document));
                    }
                }
                if (!empty($student->senior3_document)) {
                    $resultPath = $this->getFilePath($student->senior3_document);
                    if ($resultPath && file_exists($resultPath)) {
                        $zip->addFile($resultPath, $folderPath . 'result_slip_s3' . $this->getFileExtension($student->senior3_document));
                    }
                }
            }

            // Add passport document
            if (in_array('passport', $selectedDocuments) && !empty($student->other_supporting_document)) {
                $passportPath = $this->getFilePath($student->other_supporting_document);
                if ($passportPath && file_exists($passportPath)) {
                    $zip->addFile($passportPath, $folderPath . 'passport' . $this->getFileExtension($student->other_supporting_document));
                }
            }
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Sanitize folder name for file system
     */
    private function sanitizeFolderName($name)
    {
        // Remove special characters and replace spaces with underscores
        $sanitized = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $name);
        $sanitized = trim($sanitized);
        $sanitized = preg_replace('/\s+/', '_', $sanitized);
        return $sanitized ?: 'student_' . uniqid();
    }

    /**
     * Get file path from storage path
     */
    private function getFilePath($storagePath)
    {
        if (filter_var($storagePath, FILTER_VALIDATE_URL)) {
            // It's a URL, try to download it
            return null; // Would need to implement URL download
        }

        // Check if it's a storage path
        if (str_starts_with($storagePath, 'storage/')) {
            $localPath = public_path($storagePath);
        } elseif (str_starts_with($storagePath, 'public/')) {
            $localPath = storage_path('app/' . $storagePath);
        } else {
            $localPath = storage_path('app/public/' . $storagePath);
        }

        return $localPath;
    }

    /**
     * Get file extension from path
     */
    private function getFileExtension($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return $extension ? '.' . $extension : '';
    }
}
