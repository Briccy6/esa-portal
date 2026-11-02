<?php


// Removed conflicting fees route - using existing PublicFeesController routes instead

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\Route;
use App\Models\Student;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PaymentController; // corrected import (singular)
use App\Http\Controllers\MarksController; // added for AJAX route
use App\Http\Controllers\BranchController;
use App\Http\Controllers\Admin\AcademicYearController; 
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TariffController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonDetailsController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\SchoolFeeController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FeePaymentController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\TeacherDailyController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\YouTubeController;
use App\Models\Announcement;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Teacher\ResourceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\LiveClassController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\ExamTakingController;
use App\Http\Controllers\ExamResultsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ToWhomLetterController;
use App\Http\Controllers\BabyeyiController;
use App\Http\Controllers\SecretaryAuthController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\PublicExamController;
use App\Http\Controllers\PopulateLessonBranchCourseController;
use App\Http\Controllers\PaymentScheduleController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Routes (updated)
|--------------------------------------------------------------------------
|
| This routes file ensures:
| - staff.* route names are available (e.g. staff.students.edit)
| - the legacy/name-used-in-views route students.StudentCards is defined
|   (some blade templates reference 'students.StudentCards' without the
|    staff. prefix).
|
| After replacing this file run:
|   composer dump-autoload
|   php artisan route:clear
|   php artisan cache:clear
|   php artisan config:clear
|
*/

Route::prefix('student')->name('student.')->middleware(['auth:student'])->group(function() {
    Route::get('/resources', [\App\Http\Controllers\Student\ResourceViewController::class, 'index'])->name('resources.index');
    Route::get('/resources/{resource}', [\App\Http\Controllers\Student\ResourceViewController::class, 'show'])->name('resources.show');
    Route::get('/resources/{resource}/download', [\App\Http\Controllers\Student\ResourceViewController::class, 'download'])->name('resources.download');
    Route::get('/resources/{resource}/payment', [\App\Http\Controllers\Student\ResourceViewController::class, 'payment'])->name('resources.payment');
    Route::post('/payment/process', [\App\Http\Controllers\FlutterwaveController::class, 'addPayment'])->name('payment.process');
    Route::get('/payment/callback', [\App\Http\Controllers\FlutterwaveController::class, 'handleCallback'])->name('payment.callback');
    Route::get('/payment/success', [\App\Http\Controllers\FlutterwaveController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failed', [\App\Http\Controllers\FlutterwaveController::class, 'paymentFailed'])->name('payment.failed');
});

Route::prefix('timetable')->name('timetable.')->group(function () {
    Route::get('/', [TimetableController::class, 'index'])->name('index');
    Route::get('/courses-by-branch', [TimetableController::class, 'getCoursesByBranch'])->name('courses.by-branch');
    Route::get('/export/pdf', [TimetableController::class, 'exportPdfByFilter'])->name('export.pdf');
    Route::get('/print', [TimetableController::class, 'index'])->defaults('print', true)->name('print');
    Route::get('/session/{session}/details', [TimetableController::class, 'sessionDetails'])->name('session.details');
    Route::get('/{timetable}', [TimetableController::class, 'show'])->name('show');
    Route::get('teacher/{teacherId}', [TimetableController::class, 'getByTeacher'])->name('byTeacher');
    Route::get('course/{courseId}', [TimetableController::class, 'getByCourse'])->name('byCourse');
    Route::get('day/{day}', [TimetableController::class, 'getByDay'])->name('byDay');
});

Route::get('/student/invoices', [\App\Http\Controllers\StudentController::class, 'invoices'])
    ->middleware('auth:student')
    ->name('student.invoices');

Route::get('/student/exams', [\App\Http\Controllers\StudentController::class, 'studentExams'])
    ->middleware('auth:student')
    ->name('student.exams');

Route::get('/timetables/create', [TimetableController::class, 'create'])->name('timetables.create');
Route::post('/timetables', [TimetableController::class, 'store'])->name('timetable.store');

Route::get('/student/courses', [\App\Http\Controllers\StudentController::class, 'courses'])
    ->middleware('auth:student')
    ->name('student.courses');

Route::get('/student/grades', [\App\Http\Controllers\StudentController::class, 'grades'])
    ->middleware('auth:student')
    ->name('student.grades');

Route::middleware('auth:student')->get('/student/exams/{exam}/take', [\App\Http\Controllers\ExamController::class, 'take'])
    ->name('student.exams.take');

Route::middleware('auth:student')->post('/student/exams/{exam}/submit', [\App\Http\Controllers\ExamController::class, 'submit'])
    ->name('student.exams.submit');

Route::middleware('auth:student')->get('/student/exams/{exam}/disqualified', [\App\Http\Controllers\ExamController::class, 'disqualify'])
    ->name('student.exams.disqualified');

Route::middleware('auth:student')->get('/student/exams/{exam}/marks', [\App\Http\Controllers\StudentController::class, 'studentExams'])
    ->name('student.exams.marks');

Route::get('/', function () {
    $publicAnnouncements = \App\Models\Announcement::where('audience', 'public')
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('welcome', compact('publicAnnouncements'));
})->name('welcome');

// Public Exam Routes - Fully accessible without authentication
Route::prefix('public')->name('public.')->middleware(['web'])->group(function () {
    Route::get('/exams', [PublicExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{exam}', [PublicExamController::class, 'show'])->name('exams.show');
    Route::get('/exams/{exam}/payment', [PublicExamController::class, 'showPayment'])->name('exams.payment');
    Route::post('/exams/{exam}/payment', [PublicExamController::class, 'processPayment'])->name('exams.payment.process');
    Route::get('/exams/{exam}/payment/success', [PublicExamController::class, 'paymentSuccess'])->name('exams.payment.success');
    Route::get('/exams/{exam}/start', [PublicExamController::class, 'startExam'])->name('exams.start');
    Route::get('/exams/{exam}/take', [PublicExamController::class, 'takeExam'])->name('exams.take');
    Route::get('/exams/{exam}/take/{session}/{questionNumber?}', [PublicExamController::class, 'takeQuestion'])->name('exams.take.question');
    Route::post('/exams/{exam}/save-answer/{session}/{questionNumber}', [PublicExamController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('/exams/{exam}/save-session/{session}', [PublicExamController::class, 'saveSession'])->name('exams.save.session');
    Route::post('/exams/{exam}/submit/{session}', [PublicExamController::class, 'submitExam'])->name('exams.submit');
    Route::get('/api/exams/{exam}/check-access', [PublicExamController::class, 'checkAccess'])->name('exams.check-access');

    // Babyeyi Routes - Accessible without authentication
    Route::prefix('babyeyi')->name('babyeyi.')->group(function () {
        Route::get('/', [BabyeyiController::class, 'publicIndex'])->name('index');
        Route::get('/{babyeyi}', [BabyeyiController::class, 'publicShow'])->name('show');
        Route::get('/{babyeyi}/download', [BabyeyiController::class, 'publicDownload'])->name('download');
    });
});

// Public Fees Schedule Routes
Route::prefix('fees')->name('fees.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PublicFeesController::class, 'index'])->name('index');
    Route::get('/debug', [\App\Http\Controllers\PublicFeesController::class, 'debugInfo'])->name('debug');
    Route::get('/branches/{branchId}/courses', [\App\Http\Controllers\PublicFeesController::class, 'getCoursesByBranch'])->name('courses.by-branch');
    Route::get('/courses/{courseId}', [\App\Http\Controllers\PublicFeesController::class, 'getCourseFees'])->name('course-fees');
    Route::get('/courses/{courseId}/download', [\App\Http\Controllers\PublicFeesController::class, 'downloadPDF'])->name('download-pdf');
});

// Course AJAX
Route::get('/branches-by-course', [CourseController::class, 'getBranchesByCourse']);
// Marks AJAX endpoint
Route::get('/marks/ajax-students', [\App\Http\Controllers\MarksController::class, 'ajaxStudents']);

// Registration & Login
Route::post('/register', [RegisteredUserController::class, 'register'])->name('register');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');

// API routes
Route::prefix('api')->group(function () {
    Route::get('/academic-years', [StudentController::class, 'getAcademicYears'])->name('api.academic.years');
    Route::get('/academic-years/{id}/terms', [TermController::class, 'getTermsByAcademicYear'])->name('api.terms.by.year');
    Route::get('/academic-years/{id}/courses', [StudentController::class, 'getCoursesByAcademicYear'])->name('api.academic_year.courses');
    Route::get('/branches', [StudentController::class, 'getBranchesByAcademicYear'])->name('api.branches.by.academicYear');
    Route::get('/branches-by-year', [StudentController::class, 'getBranchesByAcademicYear'])->name('api.branches.by.year');
    Route::get('/branches-by-academic-year', [StudentController::class, 'getBranchesByAcademicYear'])->name('students.getBranchesByAcademicYear');
    Route::get('/courses', [StudentController::class, 'getCoursesByBranchAndYear'])->name('api.courses.by.branch.and.year');
    Route::get('/courses-by-branch-year', [StudentController::class, 'getCoursesByBranchAndYear'])->name('api.courses.by.branch.year');
    Route::get('/courses-by-branch-and-year', [StudentController::class, 'getCoursesByBranchAndYear'])->name('students.getCoursesByBranchAndYear');
});

// admin expenses export
Route::get('admin/expenses/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportExpenses'])->name('admin.expenses.export');

// Registration choice page
Route::get('/register', function () {
    return view('auth.register-choice');
})->name('register.choice');

// Student registration
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::middleware(['auth:admin,staff,teacher'])->post('/students', [StudentController::class, 'store'])->name('students.store');
Route::get('/students/get-course-fee', [StudentController::class, 'getCourseFee'])->name('students.getCourseFee');

// Legacy endpoints
Route::get('/get-academic-years/{branch}', [StudentController::class, 'getAcademicYears'])->name('get.academic.years');
Route::get('/get-courses/{branch}/{academicYear}', [StudentController::class, 'getCourses'])->name('get.courses');

// Student login routes (non-authenticated)
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/login', [StudentController::class, 'showLoginForm'])->name('login');
    Route::get('/login/identification-type', [StudentController::class, 'showIdentificationTypeForm'])->name('login.identificationType');
    Route::post('/login/id-type-selected', [StudentController::class, 'showIdNumberForm'])->name('login.idTypeSelected');
    Route::post('/login/validate-id', [StudentController::class, 'validateIdNumber'])->name('login.validateId');
    Route::get('/login/password', [StudentController::class, 'showPasswordForm'])->name('showPasswordForm');
    Route::post('/login/password', [StudentController::class, 'loginWithPassword'])->name('login.withPassword');
});

// Authenticated user routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Shared academic year switcher for admin and staff portals only
Route::post('/academic-year/switch', [\App\Http\Controllers\Admin\AcademicYearController::class, 'switch'])
    ->middleware('auth:admin,staff')
    ->name('academic_year.switch');

// Admin auth routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Admin password reset via email code
    Route::get('/forgot-password', [\App\Http\Controllers\AdminPasswordResetController::class, 'showRequestForm'])
        ->name('admin.password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\AdminPasswordResetController::class, 'sendCode'])
        ->name('admin.password.email');
    Route::get('/reset-password', [\App\Http\Controllers\AdminPasswordResetController::class, 'showResetForm'])
        ->name('admin.password.reset.form');
    Route::post('/reset-password/code', [\App\Http\Controllers\AdminPasswordResetController::class, 'resetWithCode'])
        ->name('admin.password.reset.code');
});


// Admin Teacher Routes - Resource route handles all CRUD operations (removed duplicate individual routes)

// Teacher protected routes
Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\TeacherAuthController::class, 'dashboard'])->name('dashboard');
    
    // Teacher Question Banks
    Route::get('question-banks', [\App\Http\Controllers\QuestionBankController::class, 'index'])->name('question-banks.index');
    Route::get('question-banks/create', [\App\Http\Controllers\QuestionBankController::class, 'create'])->name('question-banks.create');
    Route::post('question-banks', [\App\Http\Controllers\QuestionBankController::class, 'store'])->name('question-banks.store');
    Route::get('question-banks/{questionBank}', [\App\Http\Controllers\QuestionBankController::class, 'show'])->name('question-banks.show');
    Route::get('question-banks/{questionBank}/edit', [\App\Http\Controllers\QuestionBankController::class, 'edit'])->name('question-banks.edit');
    Route::put('question-banks/{questionBank}', [\App\Http\Controllers\QuestionBankController::class, 'update'])->name('question-banks.update');
    Route::delete('question-banks/{questionBank}', [\App\Http\Controllers\QuestionBankController::class, 'destroy'])->name('question-banks.destroy');
    Route::get('question-banks/{questionBank}/add-question', [\App\Http\Controllers\QuestionBankController::class, 'addQuestion'])->name('question-banks.add-question');
    Route::post('question-banks/{questionBank}/questions', [\App\Http\Controllers\QuestionBankController::class, 'storeQuestion'])->name('question-banks.store-question');
    Route::post('question-banks/{questionBank}/import', [\App\Http\Controllers\QuestionBankController::class, 'importQuestions'])->name('question-banks.import');
    Route::get('question-banks/{questionBank}/export', [\App\Http\Controllers\QuestionBankController::class, 'exportQuestions'])->name('question-banks.export');
    
    // Teacher Live Classes
    Route::get('live-classes', [\App\Http\Controllers\TeacherLiveClassController::class, 'index'])->name('live-classes.index');
    Route::get('live-classes/{liveClass}', [\App\Http\Controllers\TeacherLiveClassController::class, 'show'])->name('live-classes.show');
    Route::get('live-classes/{liveClass}/room', [\App\Http\Controllers\TeacherLiveClassController::class, 'room'])->name('live-classes.room');
    Route::post('live-classes/{liveClass}/start', [\App\Http\Controllers\TeacherLiveClassController::class, 'start'])->name('live-classes.start');
    Route::post('live-classes/{liveClass}/end', [\App\Http\Controllers\TeacherLiveClassController::class, 'end'])->name('live-classes.end');
    Route::get('live-classes/{liveClass}/requests', [\App\Http\Controllers\TeacherLiveClassController::class, 'manageRequests'])->name('live-classes.requests');
    Route::post('live-classes/{liveClass}/requests/{requestId}/approve', [\App\Http\Controllers\TeacherLiveClassController::class, 'approveRequest'])->name('live-classes.requests.approve');
    Route::post('live-classes/{liveClass}/requests/{requestId}/reject', [\App\Http\Controllers\TeacherLiveClassController::class, 'rejectRequest'])->name('live-classes.requests.reject');
});

// Admin protected routes
Route::middleware(['auth:admin', 'admin.academic_year'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminAuthController::class, 'dashboard'])->name('dashboard');

    Route::resource('students', StudentController::class);
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::post('/students/send-bulk-information-emails', [StudentController::class, 'sendBulkStudentInformationEmails'])->name('students.sendBulkInformationEmails');
    // NESA student endpoints removed

    Route::resource('academic_year', AcademicYearController::class)->except(['show']);
    Route::resource('messages', \App\Http\Controllers\MessageController::class);
    Route::resource('announcements', \App\Http\Controllers\AnnouncementController::class);
    Route::resource('tariffs', TariffController::class);
    Route::resource('staff', \App\Http\Controllers\StaffController::class);
    Route::resource('terms', TermController::class)->except(['show']);
    Route::resource('payments', PaymentTypeController::class);
    Route::resource('branch', BranchController::class);
    Route::resource('teachers', TeacherController::class);
    
    // Attendance routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    
    // Client Management Routes
    Route::get('/clients', [App\Http\Controllers\Admin\ClientManagementController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [App\Http\Controllers\Admin\ClientManagementController::class, 'create'])->name('clients.create');
    Route::post('/clients', [App\Http\Controllers\Admin\ClientManagementController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [App\Http\Controllers\Admin\ClientManagementController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [App\Http\Controllers\Admin\ClientManagementController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [App\Http\Controllers\Admin\ClientManagementController::class, 'update'])->name('clients.update');
    Route::get('/clients/{client}/add-tokens', [App\Http\Controllers\Admin\ClientManagementController::class, 'addTokens'])->name('clients.add-tokens');
    Route::post('/clients/{client}/store-tokens', [App\Http\Controllers\Admin\ClientManagementController::class, 'storeTokens'])->name('clients.store-tokens');
    Route::get('/clients/{client}/subscription', [App\Http\Controllers\Admin\ClientManagementController::class, 'recordSubscription'])->name('clients.subscription');
    Route::post('/clients/{client}/store-subscription', [App\Http\Controllers\Admin\ClientManagementController::class, 'storeSubscription'])->name('clients.store-subscription');
    Route::post('/clients/{client}/toggle-status', [App\Http\Controllers\Admin\ClientManagementController::class, 'toggleStatus'])->name('clients.toggle-status');
    Route::delete('/clients/{client}', [App\Http\Controllers\Admin\ClientManagementController::class, 'destroy'])->name('clients.destroy');

    Route::get('/fees', [SchoolFeeController::class, 'index'])->name('fees.index');
    Route::get('/fees/create', [SchoolFeeController::class, 'create'])->name('fees.create');
    Route::post('/fees', [SchoolFeeController::class, 'store'])->name('fees.store');

    // Course routes
    Route::get('/course/create', [CourseController::class, 'create'])->name('course.create');
    Route::post('/course/store', [CourseController::class, 'store'])->name('course.store');
    Route::get('/courses', [CourseController::class, 'index'])->name('course.index');
    Route::get('/course/{id}/edit', [CourseController::class, 'edit'])->name('course.edit');
    Route::put('/course/{id}', [CourseController::class, 'update'])->name('course.update');
    Route::delete('/course/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
    
    // Course Fee Assignment routes
    Route::get('/course/{id}/assign-fees', [CourseController::class, 'assignFees'])->name('course.assign-fees');
    Route::get('/course/{id}/test-assign-fees', [CourseController::class, 'testAssignFees'])->name('course.test-assign-fees');
    Route::post('/course/{id}/store-fees', [CourseController::class, 'storeFees'])->name('course.store-fees');
        // Get course fee (optionally for branch and term)
        Route::get('/course/fee', [CourseController::class, 'getCourseFee'])->name('course.get-fee');
    Route::get('/course/{id}/edit-fees', [CourseController::class, 'editFees'])->name('course.edit-fees');
    Route::put('/course/{id}/update-fees', [CourseController::class, 'updateFees'])->name('course.update-fees');

    // Lessons Management Routes
    Route::resource('lessons', \App\Http\Controllers\Admin\LessonsController::class);
    Route::get('/lessons/course/{course}', [\App\Http\Controllers\Admin\LessonsController::class, 'getLessonsForCourse'])->name('lessons.get-by-course');

    // Teacher assignment
    Route::get('teachers/{teacher}/assign-course', [TeacherController::class, 'assignCourse'])->name('teachers.assignCourse');
    
    // Report Card Settings
    Route::get('/report-card-settings', [App\Http\Controllers\Admin\ReportCardSettingController::class, 'index'])->name('report-card-settings.index');
    Route::get('/report-card-settings/create', [App\Http\Controllers\Admin\ReportCardSettingController::class, 'create'])->name('report-card-settings.create');
    Route::post('/report-card-settings', [App\Http\Controllers\Admin\ReportCardSettingController::class, 'store'])->name('report-card-settings.store');
    Route::get('/report-card-settings/{reportCardSetting}/edit', [App\Http\Controllers\Admin\ReportCardSettingController::class, 'edit'])->name('report-card-settings.edit');
    Route::put('/report-card-settings/{reportCardSetting}', [App\Http\Controllers\Admin\ReportCardSettingController::class, 'update'])->name('report-card-settings.update');
    Route::delete('/report-card-settings/{reportCardSetting}', [App\Http\Controllers\Admin\ReportCardSettingController::class, 'destroy'])->name('report-card-settings.destroy');
    Route::post('/report-card-settings/{reportCardSetting}/toggle-status', [App\Http\Controllers\Admin\ReportCardSettingController::class, 'toggleStatus'])->name('report-card-settings.toggle-status');
    
    // Recalculate Report Card Positions
    Route::get('/recalculate-positions', [App\Http\Controllers\Admin\RecalculatePositionsController::class, 'index'])->name('recalculate-positions.index');
    Route::post('/recalculate-positions/execute', [App\Http\Controllers\Admin\RecalculatePositionsController::class, 'execute'])->name('recalculate-positions.execute');
    
    Route::post('teachers/{teacher}/assign-course', [TeacherController::class, 'storeCourseAssignment'])->name('teachers.storeCourseAssignment');

    // Student fees
    Route::get('students/{student}/fees', [StudentController::class, 'showStudentFees'])->name('students.fees');
    Route::patch('/students/{student}/discount', [StudentController::class, 'updateDiscount'])->name('students.updateDiscount');
    
    // Student payment transcript
    Route::get('students/{student}/payment-transcript', [\App\Http\Controllers\AccountingController::class, 'printPaymentTranscript'])->name('students.payment-transcript');

    // Preferences
    Route::get('preferences', [\App\Http\Controllers\Admin\PreferenceController::class, 'index'])->name('preferences');
    Route::post('preferences', [\App\Http\Controllers\Admin\PreferenceController::class, 'store'])->name('preferences.store');

    Route::resource('grades', GradeController::class)->names('grades');
    // community-spaces feature removed – no routes needed

    // Token Management
    Route::get('/tokens', [TokenController::class, 'secretaryIndex'])->name('tokens.index');
    Route::post('/tokens/add/{student}', [TokenController::class, 'secretaryAddTokens'])->name('tokens.add');
    Route::post('/tokens/deduct/{student}', [TokenController::class, 'secretaryDeductTokens'])->name('tokens.deduct');
    Route::get('/tokens/history/{student?}', [TokenController::class, 'transactionHistory'])->name('tokens.history');
    
    // Admin Token Management (same controller methods)
    Route::get('/admin/tokens', [TokenController::class, 'secretaryIndex'])->name('admin.tokens.index');
    Route::post('/admin/tokens/add/{student}', [TokenController::class, 'secretaryAddTokens'])->name('admin.tokens.add');
    Route::post('/admin/tokens/deduct/{student}', [TokenController::class, 'secretaryDeductTokens'])->name('admin.tokens.deduct');
    Route::get('/admin/tokens/history/{student?}', [TokenController::class, 'transactionHistory'])->name('admin.tokens.history');

    // Subscription Management
    Route::get('/subscriptions', [SubscriptionController::class, 'adminIndex'])->name('subscriptions.index');
    Route::get('/subscriptions/create', [SubscriptionController::class, 'adminCreate'])->name('subscriptions.create');
    Route::post('/subscriptions', [SubscriptionController::class, 'adminStore'])->name('subscriptions.store');
    Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'adminEdit'])->name('subscriptions.edit');
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'adminUpdate'])->name('subscriptions.update');
    Route::post('/subscriptions/{subscription}/toggle', [SubscriptionController::class, 'adminToggle'])->name('subscriptions.toggle');
    Route::get('/subscriptions/student-subscriptions', [SubscriptionController::class, 'studentSubscriptions'])->name('subscriptions.student-subscriptions');
    Route::post('/subscriptions/extend/{studentSubscription}', [SubscriptionController::class, 'extendSubscription'])->name('subscriptions.extend');
    Route::delete('/subscriptions/destroy/{studentSubscription}', [SubscriptionController::class, 'destroyStudentSubscription'])->name('subscriptions.destroy-student');

    // Live Classes
    Route::resource('live-classes', LiveClassController::class);
    Route::get('live-classes/{liveClass}/room', [LiveClassController::class, 'adminRoom'])->name('live-classes.room');
    Route::post('live-classes/{liveClass}/start', [LiveClassController::class, 'start'])->name('live-classes.start');
    Route::post('live-classes/{liveClass}/end', [LiveClassController::class, 'end'])->name('live-classes.end');
    
    // Live Class Request Management
    Route::get('live-classes/{liveClass}/requests', [LiveClassController::class, 'manageRequests'])->name('live-classes.requests');


    // Admin profile
    Route::get('profile', [\App\Http\Controllers\AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [\App\Http\Controllers\AdminProfileController::class, 'update'])->name('profile.update');

    // Question Banks
    Route::get('question-banks', [QuestionBankController::class, 'index'])->name('question-banks.index');
    Route::get('question-banks/create', [QuestionBankController::class, 'create'])->name('question-banks.create');
    Route::post('question-banks', [QuestionBankController::class, 'store'])->name('question-banks.store');
    Route::get('question-banks/{questionBank}', [QuestionBankController::class, 'show'])->name('question-banks.show');
    Route::get('question-banks/{questionBank}/edit', [QuestionBankController::class, 'edit'])->name('question-banks.edit');
    Route::put('question-banks/{questionBank}', [QuestionBankController::class, 'update'])->name('question-banks.update');
    Route::delete('question-banks/{questionBank}', [QuestionBankController::class, 'destroy'])->name('question-banks.destroy');
    Route::get('question-banks/{questionBank}/add-question', [QuestionBankController::class, 'addQuestion'])->name('question-banks.add-question');
    Route::post('question-banks/{questionBank}/questions', [QuestionBankController::class, 'storeQuestion'])->name('question-banks.store-question');
    Route::post('question-banks/{questionBank}/import', [QuestionBankController::class, 'importQuestions'])->name('question-banks.import');
    Route::get('question-banks/{questionBank}/export', [QuestionBankController::class, 'exportQuestions'])->name('question-banks.export');
    Route::post('question-banks/preview-questions', [ExamController::class, 'previewQuestions'])->name('question-banks.preview-questions');

    // NESA Registration Management
    Route::get('nesa', [\App\Http\Controllers\Admin\NesaRegistrationController::class, 'index'])->name('nesa.index');
    Route::get('nesa/registered', [\App\Http\Controllers\Admin\NesaRegistrationController::class, 'registered'])->name('nesa.registered');
    Route::get('nesa/pending', [\App\Http\Controllers\Admin\NesaRegistrationController::class, 'pending'])->name('nesa.pending');
    Route::post('nesa/bulk-register', [\App\Http\Controllers\Admin\NesaRegistrationController::class, 'bulkRegister'])->name('nesa.bulk-register');
    Route::post('nesa/{student}/mark-as-registered', [\App\Http\Controllers\Admin\NesaRegistrationController::class, 'markAsRegistered'])->name('nesa.markAsRegistered');
    Route::get('nesa/{student}/edit', [\App\Http\Controllers\Admin\NesaRegistrationController::class, 'edit'])->name('nesa.edit');
    Route::put('nesa/{student}', [\App\Http\Controllers\Admin\NesaRegistrationController::class, 'update'])->name('nesa.update');
    Route::get('nesa/{student}/download', [\App\Http\Controllers\Admin\NesaRegistrationController::class, 'downloadDocument'])->name('nesa.download');
    Route::get('nesa/export/registered', [\App\Http\Controllers\Admin\NesaRegistrationController::class, 'exportRegistered'])->name('nesa.export');

    // Accounting Reports
    Route::middleware(['role:admin,accountant'])->group(function () {
        Route::get('/accounting', [\App\Http\Controllers\AccountingController::class, 'index'])->name('accounting.index');
        Route::get('/accounting/cashbook', [\App\Http\Controllers\AccountingController::class, 'cashbook'])->name('accounting.cashbook');
        Route::get('/accounting/balance-sheet', [\App\Http\Controllers\AccountingController::class, 'balanceSheet'])->name('accounting.balance-sheet');
        Route::get('/accounting/trial-balance', [\App\Http\Controllers\AccountingController::class, 'trialBalance'])->name('accounting.trial-balance');
        Route::get('/accounting/cash-flow-statement', [\App\Http\Controllers\AccountingController::class, 'cashFlowStatement'])->name('accounting.cash-flow-statement');
        Route::get('/accounting/cash-flow-forecast', [\App\Http\Controllers\AccountingController::class, 'cashFlowForecast'])->name('accounting.cash-flow-forecast');
        Route::get('/accounting/income-statement', [\App\Http\Controllers\AccountingController::class, 'incomeStatement'])->name('accounting.income-statement');
        Route::get('/accounting/general-ledger', [\App\Http\Controllers\AccountingController::class, 'generalLedger'])->name('accounting.general-ledger');
        Route::get('/accounting/export/cashbook', [\App\Http\Controllers\AccountingController::class, 'exportCashbook'])->name('accounting.export.cashbook');
        Route::get('/accounting/export/balance-sheet', [\App\Http\Controllers\AccountingController::class, 'exportBalanceSheet'])->name('accounting.export.balance-sheet');
        Route::get('/accounting/export/trial-balance', [\App\Http\Controllers\AccountingController::class, 'exportTrialBalance'])->name('accounting.export.trial-balance');
        Route::get('/accounting/export/cash-flow-statement', [\App\Http\Controllers\AccountingController::class, 'exportCashFlowStatement'])->name('accounting.export.cash-flow-statement');
        Route::get('/accounting/export/cash-flow-forecast', [\App\Http\Controllers\AccountingController::class, 'exportCashFlowForecast'])->name('accounting.export.cash-flow-forecast');
        Route::get('/accounting/payment-transcript/{student}', [\App\Http\Controllers\AccountingController::class, 'printPaymentTranscript'])->name('accounting.payment-transcript');
        
        // Student Fee Management
        Route::get('/accounting/student-fees', [\App\Http\Controllers\AccountingController::class, 'studentFees'])->name('accounting.student-fees');
        Route::get('/accounting/student-fees/{student}', [\App\Http\Controllers\AccountingController::class, 'studentFeeDetails'])->name('accounting.student-fee-details');
    });

    // Document Management
    Route::get('documents', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'index'])->name('documents.index');
    Route::get('documents/{id}/edit', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'edit'])->name('documents.edit');
    Route::put('documents/{id}', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'update'])->name('documents.update');
    Route::post('documents/bulk-update', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'bulkUpdate'])->name('documents.bulk.update');
    Route::get('documents/{id}/download', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'downloadDocument'])->name('documents.download');
    Route::get('documents/{id}/preview', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'previewDocument'])->name('documents.preview');

    // Report Cards
    Route::get('report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
    Route::get('report-cards/search-sort', [ReportCardController::class, 'searchSort'])->name('report-cards.search-sort');
    Route::get('report-cards/create', [ReportCardController::class, 'create'])->name('report-cards.create');
    Route::post('report-cards', [ReportCardController::class, 'store'])->name('report-cards.store');
    Route::post('report-cards/check-fees-for-generation', [ReportCardController::class, 'checkOutstandingFeesForGeneration'])->name('report-cards.check-fees-for-generation');
    Route::post('report-cards/check-class-fees', [ReportCardController::class, 'checkClassFeesBeforeGeneration'])->name('report-cards.check-class-fees');
    Route::post('report-cards/send-fees-email-modal/{student}', [ReportCardController::class, 'sendFeesEmailFromModal'])->name('report-cards.send-fees-email-modal');
    Route::get('report-cards/consolidated', [ReportCardController::class, 'showConsolidated'])->name('report-cards.consolidated');
    Route::get('report-cards/consolidated/print', [ReportCardController::class, 'printConsolidated'])->name('report-cards.consolidated-print');
    Route::post('report-cards/generate-class', [ReportCardController::class, 'generateForClass'])->name('report-cards.generate-class');
    Route::post('report-cards/bulk-approve', [ReportCardController::class, 'bulkApprove'])->name('report-cards.bulk-approve');
    Route::get('report-cards/print-class', [ReportCardController::class, 'printClassReports'])->name('report-cards.print-class');
    Route::get('report-cards/{reportCard}', [ReportCardController::class, 'show'])->name('report-cards.show');
    Route::get('report-cards/{reportCard}/edit', [ReportCardController::class, 'edit'])->name('report-cards.edit');
    Route::put('report-cards/{reportCard}', [ReportCardController::class, 'update'])->name('report-cards.update');
    Route::delete('report-cards/{reportCard}', [ReportCardController::class, 'destroy'])->name('report-cards.destroy');
    Route::delete('report-cards/bulk', [ReportCardController::class, 'bulkDestroy'])->name('report-cards.destroy.bulk');
    Route::delete('report-cards/by-class/delete', [ReportCardController::class, 'deleteByClass'])->name('report-cards.delete-by-class');
    Route::delete('report-cards/selected/delete', [ReportCardController::class, 'deleteSelected'])->name('report-cards.delete-selected');
    Route::delete('report-cards/all/delete', [ReportCardController::class, 'deleteAll'])->name('report-cards.delete-all');
    Route::get('report-cards/{reportCard}/print', [ReportCardController::class, 'printReport'])->name('report-cards.print');

    // Outstanding Fees Management
    Route::get('report-cards/outstanding-fees/list', [ReportCardController::class, 'outstandingFees'])->name('report-cards.outstanding-fees');
    Route::get('report-cards/outstanding-fees/{student}/status', [ReportCardController::class, 'getStudentOutstandingStatus'])->name('report-cards.student-outstanding-status');
    Route::post('report-cards/outstanding-fees/{student}/email', [ReportCardController::class, 'sendOutstandingFeesEmail'])->name('report-cards.send-outstanding-email');
    Route::post('report-cards/outstanding-fees/bulk-email', [ReportCardController::class, 'bulkSendOutstandingFeesEmails'])->name('report-cards.bulk-send-outstanding-email');

    // School Information
    Route::get('school-information', [\App\Http\Controllers\SchoolInformationController::class, 'edit'])->name('school-information.edit');
    Route::put('school-information', [\App\Http\Controllers\SchoolInformationController::class, 'update'])->name('school-information.update');

    // Resource Management
    Route::get('resources', [\App\Http\Controllers\Admin\ResourceController::class, 'index'])->name('resources.index');
    Route::get('resources/create', [\App\Http\Controllers\Admin\ResourceController::class, 'create'])->name('resources.create');
    Route::post('resources', [\App\Http\Controllers\Admin\ResourceController::class, 'store'])->name('resources.store');
    Route::get('resources/{id}/edit', [\App\Http\Controllers\Admin\ResourceController::class, 'edit'])->name('resources.edit');
    Route::put('resources/{id}', [\App\Http\Controllers\Admin\ResourceController::class, 'update'])->name('resources.update');
    Route::delete('resources/{id}', [\App\Http\Controllers\Admin\ResourceController::class, 'destroy'])->name('resources.destroy');
    Route::get('resources/{id}/download', [\App\Http\Controllers\Admin\ResourceController::class, 'downloadDocument'])->name('resources.download');
    Route::get('resources/{id}/preview', [\App\Http\Controllers\Admin\ResourceController::class, 'previewDocument'])->name('resources.preview');
    Route::get('resources/get-lessons/{courseId}', [\App\Http\Controllers\Admin\ResourceController::class, 'getLessons'])->name('resources.get-lessons');
    Route::post('resources/{id}/make-public', [\App\Http\Controllers\Admin\ResourceController::class, 'makePublic'])->name('resources.make-public');
    Route::post('resources/{id}/update-price', [\App\Http\Controllers\Admin\ResourceController::class, 'updatePrice'])->name('resources.update-price');

    Route::get('chat-test', function() {
        return view('admin.profile.test');
    })->name('chat.test');

    // Dismissed Students Routes
    Route::get('dismissed-students', [\App\Http\Controllers\DismissedStudentController::class, 'index'])->name('dismissed-students.index');
    Route::get('dismissed-students/{dismissedStudent}', [\App\Http\Controllers\DismissedStudentController::class, 'show'])->name('dismissed-students.show');
    Route::post('dismissed-students/{dismissedStudent}/restore', [\App\Http\Controllers\DismissedStudentController::class, 'restore'])->name('dismissed-students.restore');

});

// ============================================================
// BLOG ROUTES — replace the two blog sections in your web.php
// ============================================================

// -------------------------------------------------------
// 1. Admin Blog Management Routes (protected)
//    FIND this block (~line 443) and replace with below:
// -------------------------------------------------------
Route::middleware(['auth:admin,staff,teacher'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/blogs',                              [BlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/create',                       [BlogController::class, 'create'])->name('blogs.create');
    Route::post('/blogs',                             [BlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{blog}/edit',                  [BlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{blog}',                       [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blog}',                    [BlogController::class, 'destroy'])->name('blogs.destroy');
    // ✅ Fixed route name: admin.blogs.toggle-featured (was blogs.toggle-featured)
    Route::post('/blogs/{blog}/toggle-featured',      [BlogController::class, 'toggleFeatured'])->name('blogs.toggle-featured');
    // ✅ Fixed route name: admin.blogs.delete-image (was blogs.delete-image)
    Route::delete('/blogs/images/{image}',            [BlogController::class, 'deleteImage'])->name('blogs.delete-image');
    // ✅ Fixed route name: admin.blogs.delete-document (was blogs.delete-document)
    Route::delete('/blogs/documents/{document}',      [BlogController::class, 'deleteDocument'])->name('blogs.delete-document');
});

// Secretary protected routes
Route::middleware(['auth:staff'])->prefix('secretary')->name('secretary.')->group(function () {
    Route::get('/login', [SecretaryAuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [SecretaryAuthController::class, 'login'])->name('login');
    Route::post('/logout', [SecretaryAuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [SecretaryAuthController::class, 'dashboard'])->name('dashboard');
    
    // Client Management Routes
    Route::get('/clients', [App\Http\Controllers\Admin\ClientManagementController::class, 'index'])->name('clients.index');
    Route::get('/clients/{client}', [App\Http\Controllers\Admin\ClientManagementController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/add-tokens', [App\Http\Controllers\Admin\ClientManagementController::class, 'addTokens'])->name('clients.add-tokens');
    Route::post('/clients/{client}/store-tokens', [App\Http\Controllers\Admin\ClientManagementController::class, 'storeTokens'])->name('clients.store-tokens');
    Route::get('/clients/{client}/subscription', [App\Http\Controllers\Admin\ClientManagementController::class, 'recordSubscription'])->name('clients.subscription');
    Route::post('/clients/{client}/store-subscription', [App\Http\Controllers\Admin\ClientManagementController::class, 'storeSubscription'])->name('clients.store-subscription');
    Route::post('/clients/{client}/toggle-status', [App\Http\Controllers\Admin\ClientManagementController::class, 'toggleStatus'])->name('clients.toggle-status');
    Route::delete('/clients/{client}', [App\Http\Controllers\Admin\ClientManagementController::class, 'destroy'])->name('clients.destroy');
    
    // Student Registration Management
    Route::get('/student-registration', [App\Http\Controllers\Secretary\StudentRegistrationController::class, 'index'])->name('student-registration.index');
    Route::get('/student-registration/{student}', [App\Http\Controllers\Secretary\StudentRegistrationController::class, 'show'])->name('student-registration.show');
    Route::get('/student-registration/{student}/edit', [App\Http\Controllers\Secretary\StudentRegistrationController::class, 'edit'])->name('student-registration.edit');
    Route::put('/student-registration/{student}', [App\Http\Controllers\Secretary\StudentRegistrationController::class, 'update'])->name('student-registration.update');
    Route::post('/student-registration/{student}/complete', [App\Http\Controllers\Secretary\StudentRegistrationController::class, 'completeRegistration'])->name('student-registration.complete');
    
    // Token Management (secretary-specific)
    Route::get('/tokens', [TokenController::class, 'secretaryIndex'])->name('tokens.index');
    Route::post('/tokens/add/{student}', [TokenController::class, 'secretaryAddTokens'])->name('tokens.add');
    Route::post('/tokens/deduct/{student}', [TokenController::class, 'secretaryDeductTokens'])->name('tokens.deduct');
    Route::get('/tokens/history/{student?}', [TokenController::class, 'transactionHistory'])->name('tokens.history');
});

// API helpers
Route::get('/api/course/{course}/exams', function($courseId) {
    $termId = request()->query('term_id');
    
    $query = \App\Models\Exam::where('is_active', true)
        ->whereHas('courses', function($query) use ($courseId) {
            $query->where('courses.id', $courseId);
        });
    
    // Filter by term if provided
    if ($termId) {
        $query->where('term_id', $termId);
    }
    
    $exams = $query->orderBy('title')
        ->get(['id', 'title', 'term_id', 'class_date']);
    
    return response()->json($exams);
});

Route::get('/api/teacher/branch/{branch}/course/{course}/students', function($branch, $course) {
    $students = \App\Models\Student::where('course_id', $course)
        ->where('branch_id', $branch)
        ->get(['id', 'full_name']);
    return response()->json($students);
});

// Misc endpoints

Route::post('/admin/teachers/{teacher}/reset-password', [\App\Http\Controllers\TeacherController::class, 'resetPassword'])->name('admin.teachers.resetPassword');
Route::get('/teachers/by-lesson', [\App\Http\Controllers\TeacherController::class, 'getByLesson'])->name('teachers.byLesson');
Route::get('/lessons/by-courses', [\App\Http\Controllers\LessonController::class, 'getByCourses'])->name('lessons.byCourses');

Route::post('/admin/staff/{staff}/reset-password', [StaffController::class, 'resetPassword'])->name('admin.staff.reset_password');
Route::get('/teacher/resources', [ResourceController::class, 'index'])->name('teacher.resources.index');

// Attendance
Route::get('/attendance/mark', [AttendanceController::class, 'showMarkForm'])->name('attendance.mark.form');
Route::post('/attendance/mark/{timetable}', [AttendanceController::class, 'mark'])->name('attendance.mark');

// protected student profile routes
Route::middleware(['auth:student'])->group(function () {
    Route::get('/student/profile', [App\Http\Controllers\StudentController::class, 'profile'])->name('students.profile');
    Route::post('/student/profile', [App\Http\Controllers\StudentController::class, 'updateProfile'])->name('students.profile.update');
});

// Misc teacher routes
Route::get('/teacher/resources', [ResourceController::class, 'index'])->name('teacher.resources.index');
Route::get('/teachers/export', [TeacherController::class, 'export'])->name('admin.teachers.export');
Route::get('/teacher/courses', [CourseController::class, 'index'])->name('teacher.courses.index');

Route::prefix('staff')->name('staff.')->group(function () {
    // Staff Authentication
    Route::get('login', [StaffAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [StaffAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [StaffAuthController::class, 'logout'])->name('logout');

    // Staff password reset via email code
    Route::get('forgot-password', [\App\Http\Controllers\StaffPasswordResetController::class, 'showRequestForm'])
        ->name('password.request');
    Route::post('forgot-password', [\App\Http\Controllers\StaffPasswordResetController::class, 'sendCode'])
        ->name('password.email');
    Route::get('reset-password', [\App\Http\Controllers\StaffPasswordResetController::class, 'showResetForm'])
        ->name('password.reset.form');
    Route::post('reset-password/code', [\App\Http\Controllers\StaffPasswordResetController::class, 'resetWithCode'])
        ->name('password.reset.code');
         // ===== PAYMENT ROUTES - ADD THESE HERE =====
        Route::get('/students/{student}/pay-cash', [StudentController::class, 'showPaymentForm'])->name('students.pay-cash.form');
        Route::post('/students/{student}/pay-cash', [StudentController::class, 'payCash'])->name('students.pay-cash');
        Route::get('/students/{student}/term-fees', [StudentController::class, 'getTermFees'])->name('students.term-fees');
        Route::get('/students/{student}/payments/ajax', [StudentController::class, 'ajaxPayments'])->name('students.payments.ajax');
        Route::get('/students/{student}/transcript/ajax', [StudentController::class, 'transcriptAjax'])->name('students.transcript.ajax');
        Route::get('/students/{student}/transcript/send', [StudentController::class, 'sendTranscript'])->name('students.transcript.send');
        // ============================================


    Route::middleware('auth:staff')->group(function () {
        Route::get('dashboard', [StaffAuthController::class, 'dashboard'])->name('dashboard');
        
        // Payment Schedule Routes
        Route::get('/payment-schedule', [PaymentScheduleController::class, 'index'])->name('accountant.payment-schedule.index');
        Route::get('/payment-schedule/course/{course}', [PaymentScheduleController::class, 'courseDetails'])->name('accountant.payment-schedule.course-details');
        Route::get('/payment-schedule/student/{student}', [PaymentScheduleController::class, 'studentDetails'])->name('accountant.payment-schedule.student-details');



        // Student Management Routes
        Route::get('/students/{student}/edit', [StudentController::class, 'editByStaff'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'updateByStaff'])->name('students.update');
        Route::put('/students/{student}/nesa', [StudentController::class, 'registerNesa'])->name('students.nesa');
        Route::post('/students/{student}/update-nesa-status', [StudentController::class, 'updateNesaStatus'])->name('students.update-nesa-status');
        Route::post('/students/{student}/send-nesa-email', [StudentController::class, 'sendNesaStatusEmail'])->name('students.send-nesa-email');
        Route::get('/students/{student}/download-nesa-document', [StudentController::class, 'downloadNesaDocument'])->name('students.download-nesa-document');
        Route::get('/students/download-all-nesa-documents', [StudentController::class, 'downloadAllNesaDocuments'])->name('students.download-all-nesa-documents');
        Route::get('/students/export-nesa-report', [StudentController::class, 'exportNesaReport'])->name('students.export-nesa-report');
        Route::post('/students/{student}/complete-registration', [StudentController::class, 'completeRegistration'])->name('students.complete_registration');
        Route::get('/students/{student}/view-document/{documentType}', [StudentController::class, 'viewDocument'])->name('students.view_document');

        Route::get('dashboard_dos', [StaffAuthController::class, 'dashboard'])->name('dashboard.dos');
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/courses/{courseId}/branch/{branchId}/students', [StaffAuthController::class, 'getStudentsByCourseAndBranch'])->name('courses.branch.students');
        Route::get('/students/cards', [StudentController::class, 'listStudentCards'])->name('students.StudentCards');
        Route::get('/students/report', [StudentController::class, 'report'])->name('students.report');
        Route::get('/students/attendance', [StudentController::class, 'attendance'])->name('students.attendance');
        Route::post('/students/generate-cards-bulk', [StudentController::class, 'generateCardsBulk'])->name('students.generate_cards_bulk');
        Route::get('/students/qr-scan/{qrData}', [StudentController::class, 'verifyQRCode'])->name('students.qr_scan');
        Route::post('/students/{student}/generate-card', [StudentController::class, 'generateStudentCard'])->name('students.generate_card');
        Route::get('/dashboard_accountant', [StaffAuthController::class, 'dashboard'])->name('dashboard.accountant');
        
        // Staff Profile
        Route::get('profile', function () {
            $staff = auth()->guard('staff')->user();
            return view('staff.profile', compact('staff'));
        })->name('profile');
        Route::put('profile/update', [\App\Http\Controllers\StaffProfileController::class, 'update'])->name('profile.update');

        // Subscription Management Routes
        Route::get('/subscriptions', [SubscriptionController::class, 'staffIndex'])->name('subscriptions.index');
        Route::get('/subscriptions/create', [SubscriptionController::class, 'staffCreate'])->name('subscriptions.create');
        Route::post('/subscriptions', [SubscriptionController::class, 'staffStore'])->name('subscriptions.store');
        Route::get('/subscriptions/{studentSubscription}', [SubscriptionController::class, 'staffShow'])->name('subscriptions.show');
        Route::get('/subscriptions/reports', [SubscriptionController::class, 'staffReports'])->name('subscriptions.reports');

        // Lesson Management
        Route::resource('lessons', LessonController::class);
        Route::get('/lessons/get-teachers/{courseId}', [LessonController::class, 'getTeachers'])->name('lessons.get-teachers');
        Route::post('/lessons/get-courses-for-branches', [LessonController::class, 'getCoursesForBranches'])->name('lessons.get-courses-for-branches');
        
        // Debug route for testing POST endpoint
        Route::post('/lessons/debug-courses', function(Request $request) {
            try {
                return response()->json([
                    'request_data' => $request->all(),
                    'authenticated' => auth('staff')->check(),
                    'user' => auth('staff')->user() ? [
                        'id' => auth('staff')->user()->id,
                        'name' => auth('staff')->user()->name,
                        'role' => auth('staff')->user()->role
                    ] : null,
                    'branch_ids' => $request->input('branch_ids'),
                    'csrf_valid' => csrf_token() === $request->header('X-CSRF-TOKEN')
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Debug route failed',
                    'message' => $e->getMessage()
                ], 500);
            }
        })->name('lessons.debug-courses');
        Route::get('/lesson-units', [LessonDetailsController::class, 'index'])->name('lesson-units.index');
        Route::get('/lessons/{lesson}/units/create', [LessonDetailsController::class, 'create'])->name('lesson-units.create');
        Route::post('/lessons/{lesson}/units', [LessonDetailsController::class, 'store'])->name('lesson-units.store');
        Route::get('/lesson-units/{unit}', [LessonDetailsController::class, 'show'])->name('lesson-units.show');
        Route::get('/lesson-units/{unit}/edit', [LessonDetailsController::class, 'edit'])->name('lesson-units.edit');
        Route::put('/lesson-units/{unit}', [LessonDetailsController::class, 'update'])->name('lesson-units.update');
        Route::delete('/lesson-units/{unit}', [LessonDetailsController::class, 'destroy'])->name('lesson-units.destroy');
        Route::get('/lessons/{lesson}/details/create', [LessonDetailsController::class, 'create'])->name('lesson.details.create');
        Route::post('/lessons/{lesson}/details', [LessonDetailsController::class, 'store'])->name('lesson.details.store');
    });
});

/*
|--------------------------------------------------------------------------
| Compatibility route(s) to satisfy templates that expect the name
| 'students.StudentCards' (without the staff. prefix).
|
| We expose the same controller action at /students/cards and name it
| 'students.StudentCards', and protect it with staff auth so it behaves
| like the staff-only listing.
|--------------------------------------------------------------------------
*/
Route::get('/students/cards', [App\Http\Controllers\StudentController::class, 'listStudentCards'])
    ->name('students.StudentCards')
    ->middleware('auth:staff');

/*
|--------------------------------------------------------------------------
| Transaction routes (without staff prefix) for accountant dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:staff'])->group(function () {
    Route::get('/transactions/{transaction}/edit', [StudentController::class, 'editTransaction'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [StudentController::class, 'updateTransaction'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [StudentController::class, 'deleteTransaction'])->name('transactions.destroy');
});

/*
|--------------------------------------------------------------------------
| Special routes for authorized user for editing/deleting transactions
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:admin,staff'])->group(function () {
    Route::get('/admin/transactions/{transaction}/edit', [StudentController::class, 'editTransaction'])->name('admin.transactions.edit');
    Route::put('/admin/transactions/{transaction}', [StudentController::class, 'updateTransaction'])->name('admin.transactions.update');
    Route::delete('/admin/transactions/{transaction}', [StudentController::class, 'deleteTransaction'])->name('admin.transactions.delete');
});

Route::get('/admin/report', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.report');
Route::get('/teacher/photo/{id}', [\App\Http\Controllers\TeacherController::class, 'photo'])->name('teacher.photo');
Route::get('marks', [\App\Http\Controllers\MarksController::class, 'index'])->name('marks.index');

// Fee Payment Routes
Route::prefix('fees')->group(function() {
    Route::get('segments/{studentId}/{schoolFeeId}', [FeePaymentController::class, 'showSegments'])->name('fees.segments');
    Route::get('payments/create/{studentId}/{segmentId}', [FeePaymentController::class, 'createPayment'])->name('payments.create');
    Route::post('payments/store', [FeePaymentController::class, 'storePayment'])->name('payments.store');
});

Route::middleware('auth:student')->get('/student/profile', [StudentController::class, 'profile'])->name('students.profile');

Route::get('/payments', [PaymentController::class, 'index']);
Route::get('/payments/{id}', [PaymentController::class, 'show']);
Route::post('/payments', [PaymentController::class, 'store']);
Route::get('/students/total-paid', [PaymentController::class, 'totalPaidByStudents']);

// Reset link
Route::post('/admin/students/{student}/send-reset-link', [StudentController::class, 'sendResetLink'])->name('admin.students.sendResetLink');

// Bulk email
Route::post('/send-bulk-email', [EmailController::class, 'sendBulkEmail']);

// Teacher Authentication
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('login', [\App\Http\Controllers\TeacherAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\TeacherAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [\App\Http\Controllers\TeacherAuthController::class, 'logout'])->name('logout');
    // Teacher password reset via email code
    Route::get('forgot-password', [\App\Http\Controllers\TeacherPasswordResetController::class, 'showRequestForm'])
        ->name('password.request');
    Route::post('forgot-password', [\App\Http\Controllers\TeacherPasswordResetController::class, 'sendCode'])
        ->name('password.email');
    Route::get('reset-password', [\App\Http\Controllers\TeacherPasswordResetController::class, 'showResetForm'])
        ->name('password.reset.form');
    Route::post('reset-password/code', [\App\Http\Controllers\TeacherPasswordResetController::class, 'resetWithCode'])
        ->name('password.reset.code');
    Route::middleware('auth:teacher')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\TeacherAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('timetables', [\App\Http\Controllers\TeacherTimetableController::class, 'index'])->name('timetables.index');
        Route::get('timetables/{timetable}', [\App\Http\Controllers\TeacherTimetableController::class, 'show'])->name('timetables.show');
    });
});

// Daily attendance
Route::prefix('teacher')->group(function () {
    Route::post('/daily', [TeacherDailyController::class, 'store'])->name('teacher.daily.store');
});

// AJAX for teacher
Route::middleware('auth:teacher')->prefix('teacher')->group(function () {
    Route::get('course/{course}/lessons', [\App\Http\Controllers\TeacherDailyController::class, 'getLessons'])->name('teacher.course.lessons');
    Route::get('lesson/{lesson}/units', [\App\Http\Controllers\TeacherDailyController::class, 'getUnits'])->name('teacher.lesson.units');
    Route::get('course/{course}/student-count', [\App\Http\Controllers\TeacherDailyController::class, 'getStudentCount'])->name('teacher.course.studentCount');
});

Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('profile/edit', [\App\Http\Controllers\TeacherProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/update', [\App\Http\Controllers\TeacherProfileController::class, 'update'])->name('profile.update');
});

Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('courses/details', [\App\Http\Controllers\TeacherAuthController::class, 'courseDetails'])->name('courses.details');
});

// Exams - Resource route removed (conflicts with exams. prefix group below)
Route::get('/api/courses/{course}/lessons', function($courseId) {
    $lessons = \App\Models\Lesson::where('course_id', $courseId)->get(['id', 'title']);
    return response()->json(['lessons' => $lessons]);
});
Route::get('/api/lessons/{lesson}/marks', function($lessonId) {
    $lesson = \App\Models\Lesson::find($lessonId);
    return response()->json(['total_marks' => $lesson ? $lesson->total_marks : 100]);
});

Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('marks', [\App\Http\Controllers\Teacher\MarksController::class, 'index'])->name('marks.index');
    Route::get('marks/create', [\App\Http\Controllers\Teacher\MarksController::class, 'create'])->name('marks.create');
    Route::post('marks', [\App\Http\Controllers\Teacher\MarksController::class, 'store'])->name('marks.store');
    Route::get('marks/view', [\App\Http\Controllers\Teacher\MarksController::class, 'viewMarks'])->name('marks.view');
    Route::get('marks/edit/{lessonId}/{testDate}', [\App\Http\Controllers\Teacher\MarksController::class, 'editMarks'])->name('marks.edit');
    Route::put('marks/update/{lessonId}/{testDate}', [\App\Http\Controllers\Teacher\MarksController::class, 'updateMarks'])->name('marks.update');
});

// Teacher marks recording helper: fetch existing marks for a context (to prefill inputs)
Route::middleware('auth:teacher')->get('/api/teacher/marks/existing', function (\Illuminate\Http\Request $request) {
    $teacher = auth('teacher')->user();

    $validated = \Validator::make($request->all(), [
        'branch_id' => 'required|integer',
        'course_id' => 'required|integer',
        'lesson_id' => 'required|integer',
        'term_id' => 'required|integer',
        'assessment_type' => 'required|in:test,exam',
        'test_date' => 'required_if:assessment_type,test|date',
        'exam_id' => 'required_if:assessment_type,exam|integer',
    ])->validate();

    $query = \App\Models\Mark::query()
        ->where('branch_id', $validated['branch_id'])
        ->where('course_id', $validated['course_id'])
        ->where('lesson_id', $validated['lesson_id'])
        ->where('term_id', $validated['term_id'])
        ->where('assessment_type', $validated['assessment_type'])
        ->whereExists(function($q) use ($teacher) {
            $q->selectRaw(1)
              ->from('lesson_branch_course as lbc')
              ->whereColumn('lbc.lesson_id', 'marks.lesson_id')
              ->whereColumn('lbc.branch_id', 'marks.branch_id')
              ->whereColumn('lbc.course_id', 'marks.course_id')
              ->where('lbc.teacher_id', $teacher->id);
        });

    if ($validated['assessment_type'] === 'test') {
        $query->whereDate('test_date', $validated['test_date']);
    } else {
        $query->where('exam_id', $validated['exam_id']);
    }

    $marks = $query->get(['id', 'student_id', 'value', 'marks_out_of', 'test_date', 'exam_id', 'assessment_type']);

    $byStudent = [];
    foreach ($marks as $m) {
        $byStudent[$m->student_id] = [
            'id' => $m->id,
            'value' => $m->value,
            'marks_out_of' => $m->marks_out_of,
        ];
    }

    return response()->json([
        'success' => true,
        'marks' => $byStudent,
        'count' => count($byStudent),
    ]);
});

// This route is now handled by the controller below with proper auth middleware
// Route moved to: Route::middleware(['auth:staff'])->prefix('api/courses') -> getCourseStudents()

Route::get('/api/teacher/{teacher}/branch/{branch}/courses', function($teacherId, $branchId) {
    $teacher = \App\Models\Teacher::findOrFail($teacherId);
    $courses = $teacher->courses()->where('branch_id', $branchId)->get(['id', 'course_name']);
    return response()->json(['courses' => $courses]);
});

Route::get('/api/teacher/{teacher}/course/{course}/lessons', function($teacherId, $courseId) {
    $lessons = \App\Models\Lesson::where('course_id', $courseId)
        ->where('teacher_id', $teacherId)
        ->get(['id', 'title']);
    return response()->json(['lessons' => $lessons]);
});

// Teacher marks recording - get branches where teacher has assignments
Route::middleware('auth:teacher')->get('/api/teacher/branches', function() {
    $teacher = auth('teacher')->user();
    
    try {
        // DEBUG: Log the teacher ID
        \Log::info('Teacher branches API called', ['teacher_id' => $teacher->id]);
        
        // Query the lesson_branch_course table directly for distinct branches
        $branchIds = \App\Models\LessonBranchCourse::where('teacher_id', $teacher->id)
            ->pluck('branch_id')
            ->unique()
            ->toArray();
        
        \Log::info('Branch IDs found', ['branch_ids' => $branchIds, 'count' => count($branchIds)]);
        
        // Now fetch the actual branch records
        $branches = \App\Models\Branch::whereIn('id', $branchIds)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        \Log::info('Branches retrieved', ['count' => $branches->count()]);
        
        return response()->json($branches);
    } catch (\Exception $e) {
        \Log::error('Error fetching branches for teacher', [
            'teacher_id' => $teacher->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'error' => 'Failed to load branches',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Diagnostic endpoint - shows teacher assignment data
Route::middleware('auth:teacher')->get('/api/teacher/diagnostic', function() {
    $teacher = auth('teacher')->user();
    
    // Get all lesson_branch_course assignments for this teacher
    $assignments = \App\Models\LessonBranchCourse::where('teacher_id', $teacher->id)
        ->with(['branch', 'course', 'lesson'])
        ->get();
    
    return response()->json([
        'teacher_id' => $teacher->id,
        'teacher_name' => $teacher->name,
        'assignment_count' => $assignments->count(),
        'assignments' => $assignments->map(function($item) {
            return [
                'lesson_id' => $item->lesson_id,
                'lesson_title' => $item->lesson->title ?? 'Unknown',
                'branch_id' => $item->branch_id,
                'branch_name' => $item->branch?->name ?? 'Unknown',
                'course_id' => $item->course_id,
                'course_name' => $item->course?->course_name ?? 'Unknown',
            ];
        })
    ]);
});

// Teacher marks recording - get courses for teacher in a specific branch
Route::middleware('auth:teacher')->get('/api/teacher/branch/{branchId}/courses', function($branchId) {
    $teacher = auth('teacher')->user();
    
    try {
        \Log::info('Teacher courses API called', [
            'teacher_id' => $teacher->id,
            'branch_id' => $branchId
        ]);

        // Query lesson_branch_course table directly for this branch and teacher
        $courseIds = \App\Models\LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('branch_id', $branchId)
            ->pluck('course_id')
            ->unique()
            ->toArray();
        
        \Log::info('Course IDs found', ['course_ids' => $courseIds, 'count' => count($courseIds)]);

        // Fetch the actual course records for the selected academic year
        $coursesQuery = \App\Models\Course::whereIn('id', $courseIds);
        if ($yearId = \App\Services\AcademicYearContext::selectedId()) {
            $coursesQuery->where('academic_year_id', $yearId);
        }
        $courses = $coursesQuery
            ->orderBy('course_name')
            ->get(['id', 'course_name', 'course_code']);
        
        \Log::info('Courses retrieved', ['count' => $courses->count()]);
        
        return response()->json($courses);
    } catch (\Exception $e) {
        \Log::error('Error fetching courses', [
            'teacher_id' => $teacher->id,
            'branch_id' => $branchId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'error' => 'Failed to load courses',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Teacher marks recording - filtered lessons by branch, course and teacher
Route::middleware('auth:teacher')->get('/api/teacher/branch/{branchId}/course/{courseId}/lessons', function($branchId, $courseId) {
    $teacher = auth('teacher')->user();
    
    try {
        \Log::info('Teacher lessons API called', [
            'teacher_id' => $teacher->id,
            'branch_id' => $branchId,
            'course_id' => $courseId
        ]);
    
        // Get lessons directly from lesson_branch_course for this teacher, branch, and course
        $lessons = \App\Models\LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('branch_id', $branchId)
            ->where('course_id', $courseId)
            ->with('lesson')
            ->orderBy('lesson_id')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->lesson_id,
                    'title' => $item->lesson ? ($item->lesson->title ?? $item->lesson->name ?? 'Lesson ' . $item->lesson_id) : 'Lesson ' . $item->lesson_id,
                    'branch_id' => $item->branch_id,
                    'course_id' => $item->course_id,
                    'teacher_id' => $item->teacher_id,
                ];
            });
        
        \Log::info('Lessons retrieved', ['count' => $lessons->count()]);
        
        return response()->json($lessons);
    } catch (\Exception $e) {
        \Log::error('Error fetching lessons', [
            'teacher_id' => $teacher->id,
            'branch_id' => $branchId,
            'course_id' => $courseId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'error' => 'Failed to load lessons',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Teacher marks recording - get students for a specific branch, course and lesson
Route::middleware('auth:teacher')->get('/api/teacher/branch/{branchId}/course/{courseId}/students', function($branchId, $courseId) {
    $teacher = auth('teacher')->user();
    
    try {
        // DEBUG: Log the API call
        \Log::info('Teacher students API called', [
            'teacher_id' => $teacher->id,
            'branch_id' => $branchId,
            'course_id' => $courseId
        ]);

        // Validate branch and course exist
        $branch = \App\Models\Branch::findOrFail($branchId);
        $course = \App\Models\Course::findOrFail($courseId);
        
        // Get students enrolled in this branch and course for the selected academic year
        $studentsQuery = \App\Models\Student::where('course_id', $courseId)
            ->where('branch_id', $branchId)
            ->where('status', '!=', 'inactive');

        if ($yearId = \App\Services\AcademicYearContext::selectedId()) {
            $studentsQuery->where('academic_year_id', $yearId);
        }

        $students = $studentsQuery
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'reg_number', 'email']);
        
        \Log::info('Students retrieved', [
            'count' => $students->count(),
            'branch_id' => $branchId,
            'course_id' => $courseId,
            'branch_name' => $branch->name,
            'course_name' => $course->course_name
        ]);
        
        return response()->json([
            'success' => true,
            'students' => $students,
            'branch' => ['id' => $branch->id, 'name' => $branch->name],
            'course' => ['id' => $course->id, 'name' => $course->course_name],
            'count' => $students->count()
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching students for marks', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'branch_id' => $branchId,
            'course_id' => $courseId
        ]);
        return response()->json([
            'success' => false,
            'error' => 'Failed to load students',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/dos/dailies', [App\Http\Controllers\DosDailyController::class, 'index'])->name('dos.dailies.index');
Route::get('/dos/dailies/{daily}', [App\Http\Controllers\DosDailyController::class, 'show'])->name('dos.dailies.show');
Route::post('/dos/dailies/{daily}/approve', [App\Http\Controllers\DosDailyController::class, 'approve'])->name('dos.dailies.approve');
Route::post('/dos/dailies/{daily}/attendance', [App\Http\Controllers\DosDailyController::class, 'updateAttendance'])->name('dos.dailies.attendance');
Route::post('/dos/dailies/{daily}/mark-attendance', [App\Http\Controllers\DosDailyController::class, 'markAttendance'])->name('dos.dailies.mark-attendance');

// DOS Marks Management Routes
Route::middleware(['auth:staff'])->prefix('dos/marks')->name('dos.marks.')->group(function () {
    Route::get('/', [App\Http\Controllers\DOS\MarksController::class, 'index'])->name('index');
    Route::get('/{mark}', [App\Http\Controllers\DOS\MarksController::class, 'show'])->name('show');
    Route::delete('/{mark}', [App\Http\Controllers\DOS\MarksController::class, 'destroy'])->name('destroy');
    Route::put('/{mark}', [App\Http\Controllers\DOS\MarksController::class, 'update'])->name('update');
    Route::post('/bulk-delete', [App\Http\Controllers\DOS\MarksController::class, 'bulkDelete'])->name('bulk-delete');
    Route::match(['get', 'post'], '/student-report', [App\Http\Controllers\DOS\MarksController::class, 'studentReport'])->name('student-report');
    Route::post('/class-report', [App\Http\Controllers\DOS\MarksController::class, 'classReport'])->name('class-report');
});

Route::middleware(['auth:staff'])->prefix('dos/report-cards')->name('dos.report-cards.')->group(function () {
    Route::get('/', [App\Http\Controllers\DOS\ReportCardReviewController::class, 'index'])->name('index');
    Route::post('/{reportCard}/approve', [App\Http\Controllers\DOS\ReportCardReviewController::class, 'approve'])->name('approve');
    Route::get('/{reportCard}/print', [App\Http\Controllers\DOS\ReportCardReviewController::class, 'print'])->name('print');
    Route::get('/{reportCard}/download', [App\Http\Controllers\DOS\ReportCardReviewController::class, 'download'])->name('download');
});

Route::get('/api/lessons/{lesson}/course', function($lessonId) {
    $lesson = \App\Models\Lesson::with('course')->find($lessonId);
    return response()->json(['course_id' => $lesson?->course?->id]);
});

// Marks resource route removed (duplicate - already defined in teacher group above)

Route::get('/teacher/dailies', [TeacherDailyController::class, 'index'])->name('teacher.dailies.index');

Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('resources/create', [\App\Http\Controllers\Teacher\ResourceController::class, 'create'])->name('resources.create');
    Route::post('resources', [\App\Http\Controllers\Teacher\ResourceController::class, 'store'])->name('resources.store');
    Route::get('resources/{resource}/view-document', [\App\Http\Controllers\Teacher\ResourceController::class, 'viewDocument'])->name('resources.viewDocument');
    Route::get('resources/{resource}/stream-video', [\App\Http\Controllers\Teacher\ResourceController::class, 'streamVideo'])->name('resources.streamVideo');
});

Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('messages/create', [\App\Http\Controllers\MessageController::class, 'create'])->name('messages.create');
    Route::post('messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/inbox', [\App\Http\Controllers\MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('messages/sent', [\App\Http\Controllers\MessageController::class, 'sent'])->name('messages.sent');
});

// Verification routes
Route::post('/students/send-verification-code', [StudentController::class, 'sendVerificationCode'])->name('students.sendVerificationCode');
Route::post('/students/verify-email-code', [StudentController::class, 'verifyEmailCode'])->name('students.verifyEmailCode');

Route::middleware('auth:student')->get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
// Student report-card routes are defined below in the grouped `student` prefix
// (using `StudentReportCardController`). The older standalone routes were
// duplicated and caused named route collisions during route:cache.
// Removed duplicate standalone routes to keep a single authoritative set.
Route::middleware('auth:student')->get('/student/payment-transcript', [StudentController::class, 'paymentTranscriptPrint'])->name('student.payment-transcript');
Route::middleware('auth:student')->get('/student/transcript/send', [StudentController::class, 'studentSendTranscript'])->name('student.transcript.send');
Route::post('/student/login/submit', [StudentController::class, 'loginWithPassword'])->name('student.login.submit');
Route::post('/student/login/validate-reg-number', [StudentController::class, 'validateRegNumber'])->name('student.login.validateRegNumber');
Route::post('/student/login/validate-id-value', [StudentController::class, 'validateIdValue'])->name('student.login.validateIdValue');
Route::get('/student/login/simple', [StudentController::class, 'showSimpleLoginForm'])->name('student.login.simple');

// Email test route
Route::get('/test-email', function() {
    try {
        \Illuminate\Support\Facades\Mail::raw('This is a test email from Excellent Servant Academy application.', function($message) {
            $message->to('excellentservantacademy@gmail.com')
                    ->subject('Test Email - ESA Application')
                    ->from('excellentservantacademy@gmail.com', 'Excellent Servant Academy');
        });
        
        return 'Test email sent successfully! Check your inbox.';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
})->name('test.email');

// Test verification route
Route::get('/test-verification', function() {
    return view('test_verification');
})->name('test.verification');
Route::post('/student/login/code', [StudentController::class, 'loginWithCode'])->name('student.login.code');

Route::post('/student/logout', function () {
    Auth::guard('student')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('student.logout');

Route::middleware('auth:student')->get('/student/fees/pay', function () {
    return view('students.fees.pay');
})->name('student.fees.pay');

Route::middleware(['auth:staff'])->get('/staff/course-fees', [\App\Http\Controllers\StaffController::class, 'courseFees'])->name('staff.course_fees');

Route::middleware(['auth:staff'])->get('/students', [\App\Http\Controllers\StudentController::class, 'index'])->name('students.index');
Route::middleware(['auth:staff'])->get('/students/{student}/transcript/send', [\App\Http\Controllers\StudentController::class, 'sendTranscript'])->name('students.transcript.send');
Route::middleware(['auth:staff'])->get('/students/{student}/payments', [\App\Http\Controllers\StudentController::class, 'payments'])->name('students.payments');
Route::middleware(['auth:staff'])->get('/students/{student}/pay', [\App\Http\Controllers\StudentController::class, 'pay'])->name('students.pay.show');
Route::middleware(['auth:staff'])->post('/students/{student}/pay', [\App\Http\Controllers\StudentController::class, 'pay'])->name('students.pay.store');

Route::get('/students/{student}/payments/download', [StudentController::class, 'downloadPayments'])->name('students.payments.download');
Route::get('/students/{student}/payments/ajax', [StudentController::class, 'ajaxPayments'])->name('students.payments.ajax');

Route::get('/students/create', [\App\Http\Controllers\StudentController::class, 'create'])->name('students.create');

Route::get('/courses/registration-fee', [StudentController::class, 'getRegistrationFee'])->name('courses.registration_fee');
Route::post('/students/flutterwave/initialize', [\App\Http\Controllers\FlutterwaveController::class, 'initializePayment'])->name('students.flutterwave.initialize');
Route::get('/payment/callback', [StudentController::class, 'paymentCallback'])->name('students.registration.callback');

Route::post('/admin/students/{student}/send-reset-link', [\App\Http\Controllers\StudentController::class, 'sendResetLink'])->name('admin.students.sendResetLink');
Route::get('/admin/force-logout', [\App\Http\Controllers\AdminLoginController::class, 'forceLogout'])->name('admin.forceLogout');
Route::get('/teacher/force-logout', [\App\Http\Controllers\TeacherAuthController::class, 'showForceLogout'])->name('teacher.force-logout');
Route::get('/admin/force-logout', [\App\Http\Controllers\AdminAuthController::class, 'showForceLogout'])->name('admin.force-logout');
Route::post('/admin/logout', [\App\Http\Controllers\AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/youtube/redirect', [YouTubeController::class, 'redirectToYouTube'])->name('youtube.redirect');
Route::get('/youtube/callback', [YouTubeController::class, 'handleYouTubeCallback'])->name('youtube.callback');
Route::get('/youtube/upload', [YouTubeController::class, 'showUploadForm'])->name('youtube.upload.form');
Route::post('/youtube/upload', [YouTubeController::class, 'upload'])->name('youtube.upload');

Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send.form');
Route::post('/student/fees/pay', [StudentController::class, 'payFees'])->name('student.fees.pay.process');
Route::post('/contact', [HomeController::class, 'handleContact'])->name('contact.send');
Route::get('/ai/prepare-exam', [\App\Http\Controllers\ExamController::class, 'prepareExamAI'])->name('ai.prepare-exam');

// API Routes for autocomplete
Route::get('/api/branches/search', [SearchController::class, 'branches'])->name('api.branches.search');
Route::get('/api/courses/search', [SearchController::class, 'courses'])->name('api.courses.search');
Route::get('/api/lessons/search', [SearchController::class, 'lessons'])->name('api.lessons.search');
Route::get('/api/teachers/search', [SearchController::class, 'teachers'])->name('api.teachers.search');

// API Routes for branch-courses relationship
Route::get('/api/branches/{branch}/courses', function($branchId) {
    $branch = \App\Models\Branch::findOrFail($branchId);
    $courses = $branch->courses()->get(['courses.id', 'course_name']);
    return response()->json($courses);
})->name('api.branches.courses');

// API Route to get teachers for branch-course combination
Route::get('/api/branches/{branch}/courses/{course}/teachers', function($branchId, $courseId) {
    $teachers = \App\Models\Teacher::whereHas('courses', function($query) use ($courseId) {
        $query->where('courses.id', $courseId);
    })->whereHas('branches', function($query) use ($branchId) {
        $query->where('branches.id', $branchId);
    })->get(['teachers.id', 'teacher_name']);
    
    return response()->json($teachers);
})->name('api.branches.courses.teachers');

// API Route to get lessons for branch-course combination
Route::get('/api/branches/{branch}/courses/{course}/lessons', function($branchId, $courseId) {
    $lessons = \App\Models\Lesson::whereHas('branchCourses', function($query) use ($branchId, $courseId) {
        $query->where('branch_id', $branchId)
              ->where('course_id', $courseId);
    })->get(['id', 'title', 'name', 'course_id']);
    
    return response()->json($lessons);
})->name('api.branches.courses.lessons');

// API Route to get all courses
Route::get('/api/courses/all', function() {
    $courses = \App\Models\Course::all(['id', 'course_name']);
    return response()->json($courses);
})->name('api.courses.all');

// Live Class Homepage API
Route::get('/api/live-classes/homepage', [LiveClassController::class, 'homepageLiveClasses'])->name('api.live-classes.homepage');
Route::get('/api/live-classes/{liveClass}/check-access', [LiveClassController::class, 'checkClassAccess'])->name('api.live-classes.check-access');
Route::get('/api/live-classes/{liveClass}/requests', [LiveClassController::class, 'apiGetRequests'])->name('api.live-classes.requests');

Route::get('/live-classes', [LiveClassController::class, 'index'])->name('live-classes.index');
Route::get('/live-classes/{liveClass}', [LiveClassController::class, 'show'])->name('live-classes.show');
Route::post('/live-classes/{liveClass}/join', [LiveClassController::class, 'join'])->name('live-classes.join');
Route::get('/live-classes/{liveClass}/room', [LiveClassController::class, 'room'])->name('live-classes.room');

// Public Access Routes
Route::get('/live-classes/{liveClass}/public-join', [LiveClassController::class, 'publicJoin'])->name('live-classes.public-join');
Route::get('/live-classes/{liveClass}/payment', [LiveClassController::class, 'showPaymentForm'])->name('live-classes.payment');
Route::post('/live-classes/{liveClass}/payment', [LiveClassController::class, 'processPayment'])->name('live-classes.payment.process');
Route::get('/live-classes/{liveClass}/payment/callback', [LiveClassController::class, 'handlePaymentCallback'])->name('live-classes.payment.callback');
Route::get('/live-classes/{liveClass}/payment/verification', [LiveClassController::class, 'paymentVerification'])->name('live-classes.payment-verification');
Route::post('/webhooks/flutterwave/live-classes', [LiveClassController::class, 'handleWebhook'])->name('webhooks.flutterwave.live-classes');

// Client Authentication Routes
Route::prefix('auth/client')->name('client.auth.')->group(function () {
    Route::get('/register', [App\Http\Controllers\Auth\Client\ClientAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\Client\ClientAuthController::class, 'register']);
    Route::get('/login', [App\Http\Controllers\Auth\Client\ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\Client\ClientAuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Auth\Client\ClientAuthController::class, 'logout'])->name('logout');

    // Google OAuth Routes
    Route::get('/google', [App\Http\Controllers\Auth\Client\ClientAuthController::class, 'redirectToGoogle'])->name('google');
    Route::get('/google/callback', [App\Http\Controllers\Auth\Client\ClientAuthController::class, 'handleGoogleCallback'])->name('google.callback');

    // Client password reset via email code
    Route::get('/forgot-password', [App\Http\Controllers\Auth\Client\ClientPasswordResetController::class, 'showRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\Client\ClientPasswordResetController::class, 'sendCode'])
        ->name('password.email');
    Route::get('/reset-password', [App\Http\Controllers\Auth\Client\ClientPasswordResetController::class, 'showResetForm'])
        ->name('password.reset.form');
    Route::post('/reset-password/code', [App\Http\Controllers\Auth\Client\ClientPasswordResetController::class, 'resetWithCode'])
        ->name('password.reset.code');
});

// Client Dashboard Routes
Route::middleware('auth:client')->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Auth\Client\ClientAuthController::class, 'showDashboard'])->name('dashboard');
    
    // Exams
    Route::get('/exams', [App\Http\Controllers\Client\ClientExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{exam}', [App\Http\Controllers\Client\ClientExamController::class, 'show'])->name('exams.show');
    Route::get('/exams/{exam}/take', [App\Http\Controllers\Client\ClientExamController::class, 'take'])->name('exams.take');
    Route::get('/exams/{exam}/take/{session}/{questionNumber?}', [App\Http\Controllers\Client\ClientExamController::class, 'takeQuestion'])->name('exams.take.question');
    Route::post('/exams/{exam}/save-answer/{session}/{questionNumber}', [App\Http\Controllers\Client\ClientExamController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('/exams/{exam}/submit/{session}', [App\Http\Controllers\Client\ClientExamController::class, 'submitExam'])->name('exams.submit');
    
    // Subscriptions
    Route::get('/subscriptions', [App\Http\Controllers\Client\ClientSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/purchase', [App\Http\Controllers\Client\ClientSubscriptionController::class, 'purchase'])->name('subscriptions.purchase');
    Route::post('/subscriptions/purchase/{subscription}', [App\Http\Controllers\Client\ClientSubscriptionController::class, 'processPurchase'])->name('subscriptions.process');
    
    // Tokens
    Route::get('/tokens', [App\Http\Controllers\Client\ClientTokenController::class, 'index'])->name('tokens.index');
    Route::get('/tokens/purchase', [App\Http\Controllers\Client\ClientTokenController::class, 'purchase'])->name('tokens.purchase');
    Route::post('/tokens/purchase', [App\Http\Controllers\Client\ClientTokenController::class, 'processPurchase'])->name('tokens.process');
    
    // Profile
    Route::get('/profile', [App\Http\Controllers\Client\ClientProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Client\ClientProfileController::class, 'update'])->name('profile.update');
    
    // Settings
    Route::get('/settings', [App\Http\Controllers\Client\ClientSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Client\ClientSettingsController::class, 'update'])->name('settings.update');
    
    // Logout
    Route::post('/logout', [App\Http\Controllers\Auth\Client\ClientAuthController::class, 'logout'])->name('logout');
});
Route::prefix('public-subscriptions')->name('public.subscriptions.')->group(function () {
    Route::get('/', [App\Http\Controllers\PublicSubscriptionController::class, 'index'])->name('index');
    Route::get('/register', [App\Http\Controllers\PublicSubscriptionController::class, 'showRegistration'])->name('register');
    Route::post('/register', [App\Http\Controllers\PublicSubscriptionController::class, 'register'])->name('register.submit');
    Route::get('/login', [App\Http\Controllers\PublicSubscriptionController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\PublicSubscriptionController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\PublicSubscriptionController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [App\Http\Controllers\PublicSubscriptionController::class, 'dashboard'])->name('dashboard')->middleware('auth:client');
    Route::get('/purchase/{subscription}', [App\Http\Controllers\PublicSubscriptionController::class, 'showPurchase'])->name('purchase')->middleware('auth:client');
    Route::post('/purchase/{subscription}/initialize', [App\Http\Controllers\PublicSubscriptionController::class, 'initializePayment'])->name('payment.initialize')->middleware('auth:client');
    Route::get('/payment/callback', [App\Http\Controllers\PublicSubscriptionController::class, 'handlePaymentCallback'])->name('payment.callback');
});

// Public Token Routes
Route::prefix('public-subscriptions/tokens')->name('public.tokens.')->group(function () {
    Route::get('/', [App\Http\Controllers\PublicTokenController::class, 'index'])->name('index')->middleware('auth:client');
    Route::get('/purchase', [App\Http\Controllers\PublicTokenController::class, 'purchase'])->name('purchase')->middleware('auth:client');
    Route::post('/purchase', [App\Http\Controllers\PublicTokenController::class, 'processPurchase'])->name('purchase.process')->middleware('auth:client');
    Route::get('/callback', [App\Http\Controllers\PublicTokenController::class, 'handleCallback'])->name('callback');
    Route::post('/logout', [App\Http\Controllers\PublicTokenController::class, 'logout'])->name('logout');
});

// Student Request Routes
Route::middleware('auth:student')->group(function () {
    Route::get('/live-classes/{liveClass}/request', [LiveClassController::class, 'requestAccess'])->name('live-classes.request');
    Route::post('/live-classes/{liveClass}/request', [LiveClassController::class, 'submitRequest'])->name('live-classes.request.submit');
});

// Student Live Class Routes
Route::middleware('auth:student')->prefix('student')->name('student.')->group(function () {
    Route::get('/live-classes', [LiveClassController::class, 'studentIndex'])->name('live-classes.index');
    Route::get('/live-classes/{liveClass}', [LiveClassController::class, 'studentShow'])->name('live-classes.show');
    Route::post('/live-classes/{liveClass}/join', [LiveClassController::class, 'join'])->name('live-classes.join');
    Route::get('/live-classes/{liveClass}/join', function (\App\Models\LiveClass $liveClass) {
        return redirect()->route('student.live-classes.room', $liveClass);
    });
    Route::get('/live-classes/{liveClass}/room', [LiveClassController::class, 'room'])->name('live-classes.room');
    Route::post('/live-classes/{liveClass}/leave', [LiveClassController::class, 'studentLeave'])->name('live-classes.leave');

    // Student Report Card Routes are defined later using ReportCardController
    // (Avoid duplicate route names by using the single authoritative set)
});


// Simple test route to debug web requests
Route::get('/test-web-request', function() {
    return response()->json([
        'message' => 'Web request works!',
        'timestamp' => now(),
        'database_connection' => \DB::connection()->getDatabaseName(),
        'debug' => 'This is a test'
    ]);
});

// Live Class Request Management (accessible from room)
Route::middleware(['auth:admin,staff,teacher'])->group(function () {
    Route::post('live-classes/{liveClass}/requests/{requestId}/approve', [LiveClassController::class, 'approveRequest'])->name('live-classes.requests.approve');
    Route::post('live-classes/{liveClass}/requests/{requestId}/reject', [LiveClassController::class, 'rejectRequest'])->name('live-classes.requests.reject');
});


// WebRTC Routes - NO AUTHENTICATION REQUIRED
// This allows live streaming to work without authentication issues

Route::get('/student/pay/', [StudentController::class, 'payFees'])->name('student.pay');

Route::get('/staff/one-time-login/{staff}', [StaffController::class, 'oneTimeLogin'])
    ->name('staff.one_time_login')
    ->middleware('signed');

Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
Route::post('/timetable/generate-ai', [TimetableController::class, 'generateWithAI'])->name('timetable.generate.ai');

Route::get('/staff/google-meet', function () {
    $branchCourses = \DB::table('branch_course')->get(['branch_id', 'course_id']);
    return view('staff.google_meet', compact('branchCourses'));
})->middleware('auth:staff');

Route::get('/google/redirect', [App\Http\Controllers\GoogleMeetController::class, 'redirectToGoogle'])->name('google.meet.redirect');
Route::get('/google/callback', [App\Http\Controllers\GoogleMeetController::class, 'handleGoogleCallback'])->name('google.meet.callback');
Route::post('/google/create-meet', [App\Http\Controllers\GoogleMeetController::class, 'createMeet'])->name('google.create-meet');

Route::get('/student/payment', [StudentController::class, 'showPaymentPage'])->name('student.payment');
Route::get('/student/payment-schedule', [StudentController::class, 'paymentSchedule'])->name('student.payment-schedule');
Route::post('/student/ai-exam', [StudentController::class, 'generateStudySummary'])->name('student.ai.exam');
Route::post('/student/pay-fee', [StudentController::class, 'payFees'])->name('student.pay.fee');
Route::get('/student/payments/create', [StudentController::class, 'showPaymentPage'])->name('student.payments.create');

Route::post('/exam/{exam}/submit', [App\Http\Controllers\ExamController::class, 'submit'])->name('exam.submit');
Route::middleware('auth:student')->get('/exam/{exam}/take', [App\Http\Controllers\ExamController::class, 'take'])->name('exam.take');
Route::get('/exam/{exam}/disqualified', [App\Http\Controllers\ExamController::class, 'disqualify'])->name('exam.disqualified');

// Test logo debug
Route::get('/test-logo', function() {
    $systemLogo = \Illuminate\Support\Facades\Cache::get('esa_system_logo');
    $storagePath = storage_path('app/public/' . $systemLogo);
    $publicPath = public_path('storage/' . $systemLogo);
    return response()->json([
        'cached_logo' => $systemLogo,
        'storage_exists' => $systemLogo ? file_exists($storagePath) : false,
        'public_exists' => $systemLogo ? file_exists($publicPath) : false,
        'storage_path' => $storagePath,
        'public_path' => $publicPath,
        'asset_url' => $systemLogo ? asset('storage/' . $systemLogo) : null,
    ]);
});

// Transcripts
Route::get('/students/{student}/transcript', [App\Http\Controllers\StudentController::class, 'transcript'])->name('students.transcript');
Route::get('/students/{student}/transcript/ajax', [App\Http\Controllers\StudentController::class, 'transcriptAjax']);

// API Routes for Transcript Management
Route::middleware(['auth:staff'])->prefix('api/transcripts')->name('api.transcripts.')->group(function () {
    Route::get('/all', [App\Http\Controllers\TranscriptController::class, 'getAllTranscripts'])->name('all');
    Route::get('/course/{courseId}', [App\Http\Controllers\TranscriptController::class, 'getCourseTranscripts'])->name('course');
    Route::get('/course/{courseId}/payment-stats', [App\Http\Controllers\TranscriptController::class, 'getPaymentStatistics'])->name('payment-stats');
    Route::get('/course/{courseId}/term-fees', [App\Http\Controllers\TranscriptController::class, 'getTermFeesBreakdown'])->name('term-fees');
});

// API Routes for Course Management
Route::middleware(['auth:staff'])->prefix('api/courses')->name('api.courses.')->group(function () {
    Route::get('/{courseId}/students', [App\Http\Controllers\CourseController::class, 'getCourseStudents'])->name('students');
});


// Teacher resource routes

Route::get('/teacher/course/{course}/students', [\App\Http\Controllers\TeacherDailyController::class, 'getStudents']);
Route::put('teacher/resources/{resource}', [\App\Http\Controllers\Teacher\ResourceController::class, 'update'])->name('teacher.resources.update');
Route::get('teacher/resources/{resource}/edit', [\App\Http\Controllers\Teacher\ResourceController::class, 'edit'])->name('teacher.resources.edit');
Route::delete('teacher/resources/{id}', [\App\Http\Controllers\Teacher\ResourceController::class, 'destroy'])->name('teacher.resources.destroy');
Route::get('teacher/resources/{resource}/view-document', [\App\Http\Controllers\Teacher\ResourceController::class, 'viewDocument'])->name('teacher.resources.viewDocument');

// -------------------------------------------------------
// 2. Public Blog Routes
//    FIND the public block (~line 1089) and replace with below:
// -------------------------------------------------------

// ✅ Fixed: renamed 'blogs.index' → 'blogs.public.index' to avoid conflict with admin.blogs.index
// If your public blog listing view uses route('blogs.index'), update it to route('blogs.public.index')
Route::get('/blogs',                   [BlogController::class, 'publicIndex'])->name('blogs.public.index');

// blogs.show stays the same — used in index blade as route('blogs.show', $blog->slug)
Route::get('/blogs/{blog:slug}',       [BlogController::class, 'show'])->name('blogs.show');
Route::post('/blogs/{blog}/view',      [BlogController::class, 'incrementViewCount'])->name('blogs.view-count');

// Public Exam Routes - REMOVED DUPLICATE (already defined above with public. prefix)

// Remove duplicate routes - they're already defined in DOS group below

// Enhanced Exam System Routes
Route::middleware(['web', 'auth:admin,staff,teacher'])->prefix('exams')->name('exams.')->group(function () {
    // Question Banks
    Route::resource('question-banks', QuestionBankController::class);
    Route::get('question-banks/{questionBank}/add-question', [QuestionBankController::class, 'addQuestion'])->name('question-banks.add-question');
    Route::post('question-banks/{questionBank}/questions', [QuestionBankController::class, 'storeQuestion'])->name('question-banks.store-question');
    Route::post('question-banks/{questionBank}/import', [QuestionBankController::class, 'importQuestions'])->name('question-banks.import');
    Route::get('question-banks/{questionBank}/export', [QuestionBankController::class, 'exportQuestions'])->name('question-banks.export');
    Route::post('question-banks/preview-questions', [ExamController::class, 'previewQuestions'])->name('question-banks.preview-questions');
    
    // Enhanced Exam Management
    Route::get('{exam}/results', [ExamResultsController::class, 'index'])->name('results');
    Route::get('{exam}/results/{attempt}', [ExamResultsController::class, 'studentResults'])->name('student-results');
    Route::get('{exam}/analytics', [ExamResultsController::class, 'analytics'])->name('analytics');
    Route::get('{exam}/report', [ExamResultsController::class, 'generateReport'])->name('report');
    Route::get('{exam}/export', [ExamResultsController::class, 'exportResults'])->name('export');
    Route::post('{exam}/release-results', [ExamResultsController::class, 'releaseResults'])->name('release-results');
    Route::post('{exam}/grade-essay', [ExamResultsController::class, 'gradeEssay'])->name('grade-essay');
    Route::post('{exam}/bulk-grade', [ExamResultsController::class, 'bulkGrade'])->name('bulk-grade');
    
    // Exam Questions Management
    Route::get('{exam}/questions', [\App\Http\Controllers\ExamQuestionController::class, 'index'])->name('questions');
    Route::get('{exam}/questions/create', [\App\Http\Controllers\ExamQuestionController::class, 'create'])->name('questions.create');
    Route::post('{exam}/questions', [\App\Http\Controllers\ExamQuestionController::class, 'store'])->name('questions.store');
    Route::post('{exam}/questions/import', [\App\Http\Controllers\ExamQuestionController::class, 'import'])->name('questions.import');
    Route::post('{exam}/import-from-banks', [\App\Http\Controllers\ExamController::class, 'importFromBanks'])->name('import-from-banks');
    
    });

// DOS-specific exam routes
Route::middleware(['auth:staff'])->prefix('dos/exams')->name('dos.exams.')->group(function () {
    Route::get('/', [ExamController::class, 'index'])->name('index');
    Route::get('/create', [ExamController::class, 'create'])->name('create');
    Route::post('/', [ExamController::class, 'store'])->name('store');
    Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
    Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
    Route::put('/{exam}', [ExamController::class, 'update'])->name('update');
    Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
    
    // Staff Question Management Routes
    Route::get('{exam}/questions', [\App\Http\Controllers\Staff\StaffQuestionController::class, 'index'])->name('questions');
    Route::get('{exam}/questions/create', [\App\Http\Controllers\Staff\StaffQuestionController::class, 'create'])->name('questions.create');
    Route::post('{exam}/questions', [\App\Http\Controllers\Staff\StaffQuestionController::class, 'store'])->name('questions.store');
    Route::post('{exam}/questions/import', [\App\Http\Controllers\Staff\StaffQuestionController::class, 'import'])->name('questions.import');
});

// DOS Academic Routes
Route::middleware(['auth:staff'])->prefix('dos')->name('dos.')->group(function () {
    // Courses Management
    Route::get('/courses', [\App\Http\Controllers\DosCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [\App\Http\Controllers\DosCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [\App\Http\Controllers\DosCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [\App\Http\Controllers\DosCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [\App\Http\Controllers\DosCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [\App\Http\Controllers\DosCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [\App\Http\Controllers\DosCourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/assign-lesson', [\App\Http\Controllers\DosCourseController::class, 'assignLesson'])->name('courses.assignLesson');

    // Lessons Management
    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::post('/lessons/assign', [LessonController::class, 'assignToBranches'])->name('lessons.assign');
    Route::get('/lessons/{lesson}/assignments', [LessonController::class, 'getAssignments'])->name('lessons.assignments');
    Route::delete('/lessons/{assignmentId}/delete-assignment', [LessonController::class, 'deleteAssignment'])->name('lessons.delete-assignment');

    // Live Classes Management
    Route::get('/live-classes', [\App\Http\Controllers\DOS\LiveClassController::class, 'index'])->name('live-classes.index');
    Route::get('/live-classes/create', [\App\Http\Controllers\DOS\LiveClassController::class, 'create'])->name('live-classes.create');
    Route::post('/live-classes', [\App\Http\Controllers\DOS\LiveClassController::class, 'store'])->name('live-classes.store');
    Route::get('/live-classes/{liveClass}', [\App\Http\Controllers\DOS\LiveClassController::class, 'show'])->name('live-classes.show');
    Route::get('/live-classes/{liveClass}/edit', [\App\Http\Controllers\DOS\LiveClassController::class, 'edit'])->name('live-classes.edit');
    Route::put('/live-classes/{liveClass}', [\App\Http\Controllers\DOS\LiveClassController::class, 'update'])->name('live-classes.update');
    Route::delete('/live-classes/{liveClass}', [\App\Http\Controllers\DOS\LiveClassController::class, 'destroy'])->name('live-classes.destroy');
    Route::post('/live-classes/{liveClass}/start', [\App\Http\Controllers\DOS\LiveClassController::class, 'start'])->name('live-classes.start');
    Route::post('/live-classes/{liveClass}/end', [\App\Http\Controllers\DOS\LiveClassController::class, 'end'])->name('live-classes.end');
    Route::get('/live-classes/{liveClass}/room', [\App\Http\Controllers\DOS\LiveClassController::class, 'room'])->name('live-classes.room');
    Route::get('/live-classes/{liveClass}/requests', [\App\Http\Controllers\DOS\LiveClassController::class, 'manageRequests'])->name('live-classes.requests');
    
    // Study Resources Management
    Route::get('/resources', [\App\Http\Controllers\DOS\ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/create', [\App\Http\Controllers\DOS\ResourceController::class, 'create'])->name('resources.create');
    Route::post('/resources', [\App\Http\Controllers\DOS\ResourceController::class, 'store'])->name('resources.store');
    Route::get('/resources/{resource}', [\App\Http\Controllers\DOS\ResourceController::class, 'show'])->name('resources.show');
    Route::get('/resources/{resource}/edit', [\App\Http\Controllers\DOS\ResourceController::class, 'edit'])->name('resources.edit');
    Route::put('/resources/{resource}', [\App\Http\Controllers\DOS\ResourceController::class, 'update'])->name('resources.update');
    Route::delete('/resources/{resource}', [\App\Http\Controllers\DOS\ResourceController::class, 'destroy'])->name('resources.destroy');
    Route::get('/resources/{resource}/download', [\App\Http\Controllers\DOS\ResourceController::class, 'downloadDocument'])->name('resources.download');
    Route::get('/resources/{resource}/preview', [\App\Http\Controllers\DOS\ResourceController::class, 'previewDocument'])->name('resources.preview');
    
    // Assignments Management
    Route::get('/assignments', [\App\Http\Controllers\Admin\ResourceController::class, 'assignmentsIndex'])->name('assignments.index');
    Route::get('/assignments/create', [\App\Http\Controllers\Admin\ResourceController::class, 'assignmentsCreate'])->name('assignments.create');
    Route::post('/assignments', [\App\Http\Controllers\Admin\ResourceController::class, 'assignmentsStore'])->name('assignments.store');
    Route::get('/assignments/{assignment}', [\App\Http\Controllers\Admin\ResourceController::class, 'assignmentsShow'])->name('assignments.show');
    Route::get('/assignments/{assignment}/edit', [\App\Http\Controllers\Admin\ResourceController::class, 'assignmentsEdit'])->name('assignments.edit');
    Route::put('/assignments/{assignment}', [\App\Http\Controllers\Admin\ResourceController::class, 'assignmentsUpdate'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [\App\Http\Controllers\Admin\ResourceController::class, 'assignmentsDestroy'])->name('assignments.destroy');

    // Timetable Management
    Route::get('/timetable', [\App\Http\Controllers\DosTimetableController::class, 'index'])->name('timetable.index');
    Route::get('/timetable/create', [\App\Http\Controllers\DosTimetableController::class, 'create'])->name('timetable.create');
    Route::get('/timetable/branch-courses', [\App\Http\Controllers\DosTimetableController::class, 'branchCourses'])->name('timetable.branch-courses');
    Route::post('/timetable', [\App\Http\Controllers\DosTimetableController::class, 'store'])->name('timetable.store');
    Route::get('/timetable/{timetable}/edit', [\App\Http\Controllers\DosTimetableController::class, 'edit'])->name('timetable.edit');
    Route::put('/timetable/{timetable}', [\App\Http\Controllers\DosTimetableController::class, 'update'])->name('timetable.update');
    Route::post('/timetable/{timetable}/publish', [\App\Http\Controllers\DosTimetableController::class, 'publish'])->name('timetable.publish');
    Route::delete('/timetable/{timetable}', [\App\Http\Controllers\DosTimetableController::class, 'destroy'])->name('timetable.destroy');
    Route::get('/timetable/{timetable}/validate', [\App\Http\Controllers\DosTimetableController::class, 'validateTimetable'])->name('timetable.validate');
    
    // Student Management Routes
    Route::get('/students', [\App\Http\Controllers\DOS\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/cards', [\App\Http\Controllers\DOS\StudentController::class, 'listStudentCards'])->name('students.StudentCards');
    Route::get('/students/report', [\App\Http\Controllers\DOS\StudentController::class, 'report'])->name('students.report');
    Route::get('/students/attendance', [\App\Http\Controllers\DOS\StudentController::class, 'attendance'])->name('students.attendance');
    Route::post('/students/mark-attendance', [\App\Http\Controllers\DOS\StudentController::class, 'markAttendance'])->name('students.mark-attendance');
    Route::get('/students/get-students-for-attendance', [\App\Http\Controllers\DOS\StudentController::class, 'getStudentsForAttendance'])->name('students.get-students-for-attendance');
    Route::get('/students/{student}/edit', [\App\Http\Controllers\DOS\StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [\App\Http\Controllers\DOS\StudentController::class, 'update'])->name('students.update');
    Route::post('/students/{student}/generate-card', [\App\Http\Controllers\DOS\StudentController::class, 'generateStudentCard'])->name('students.generate_card');
    Route::post('/students/generate-cards-bulk', [\App\Http\Controllers\DOS\StudentController::class, 'generateCardsBulk'])->name('students.generate_cards_bulk');
    Route::get('/students/qr-scan/{qrData}', [\App\Http\Controllers\DOS\StudentController::class, 'verifyQRCode'])->name('students.qr_scan');
});

// Admin-specific exam routes
Route::middleware(['auth:admin'])->prefix('admin/exams')->name('admin.exams.')->group(function () {
    Route::get('/', [ExamController::class, 'index'])->name('index');
    Route::get('/create', [ExamController::class, 'create'])->name('create');
    Route::post('/', [ExamController::class, 'store'])->name('store');
    Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
    Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
    Route::put('/{exam}', [ExamController::class, 'update'])->name('update');
    Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
    Route::post('/{exam}/make-public', [ExamController::class, 'makePublic'])->name('make-public');
    Route::post('/{exam}/make-private', [ExamController::class, 'makePrivate'])->name('make-private');
    
    // API endpoint for fetching courses and lessons by branch
    Route::get('/api/lessons-courses/{branchId}', [ExamController::class, 'getLessonsByCourse'])->name('api.lessons-courses');
    
    // Bulk Question Upload Routes
    Route::get('/bulk-upload', [ExamController::class, 'bulkUploadForm'])->name('bulk-upload');
    Route::post('/bulk-upload', [ExamController::class, 'bulkUpload'])->name('bulk-upload.process');
    Route::get('/download-template/{subject}', [ExamController::class, 'downloadTemplate'])->name('download-template');
    
    // Admin Question Management Routes
    Route::get('{exam}/questions', [\App\Http\Controllers\Admin\AdminQuestionController::class, 'index'])->name('questions');
    Route::get('{exam}/questions/create', [\App\Http\Controllers\Admin\AdminQuestionController::class, 'create'])->name('questions.create');
    Route::post('{exam}/questions', [\App\Http\Controllers\Admin\AdminQuestionController::class, 'store'])->name('questions.store');
    Route::post('{exam}/questions/import', [\App\Http\Controllers\Admin\AdminQuestionController::class, 'import'])->name('questions.import');
});

// Teacher-specific exam routes
Route::middleware(['auth:teacher'])->prefix('teacher/exams')->name('teacher.exams.')->group(function () {
    Route::get('/', [ExamController::class, 'index'])->name('index');
    Route::get('/create', [ExamController::class, 'create'])->name('create');
    Route::post('/', [ExamController::class, 'store'])->name('store');
    Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
    Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
    Route::put('/{exam}', [ExamController::class, 'update'])->name('update');
    Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
    
    // API endpoint for fetching courses and lessons by branch
    Route::get('/api/lessons-courses/{branchId}', [ExamController::class, 'getLessonsByCourse'])->name('api.lessons-courses');
    
    // Teacher Question Management Routes
    Route::get('{exam}/questions', [\App\Http\Controllers\Teacher\TeacherQuestionController::class, 'index'])->name('questions');
    Route::get('{exam}/questions/create', [\App\Http\Controllers\Teacher\TeacherQuestionController::class, 'create'])->name('questions.create');
    Route::post('{exam}/questions', [\App\Http\Controllers\Teacher\TeacherQuestionController::class, 'store'])->name('questions.store');
    Route::post('{exam}/questions/import', [\App\Http\Controllers\Teacher\TeacherQuestionController::class, 'import'])->name('questions.import');
});

// Student Exam Taking Routes
Route::middleware('auth:student')->prefix('exams')->name('exams.')->group(function () {
    Route::get('{exam}/start', [ExamTakingController::class, 'start'])->name('start');
    Route::get('{exam}/take/{session}/{questionNumber?}', [ExamTakingController::class, 'takeQuestion'])->name('take-question');
    Route::post('{exam}/save-answer/{session}/{questionNumber}', [ExamTakingController::class, 'saveAnswer'])->name('save-answer');
    Route::post('{exam}/submit/{session}', [ExamTakingController::class, 'submitExam'])->name('submit');
});

// Admin Exam Preview Routes
Route::middleware('auth:admin')->prefix('admin/exams')->name('admin.exams.')->group(function () {
    Route::get('{exam}/preview', [ExamController::class, 'preview'])->name('preview');
});

// Flutterwave Payment Routes
Route::middleware('auth:student')->prefix('flutterwave')->name('flutterwave.')->group(function () {
    Route::post('/payment/initialize-subscription', [App\Http\Controllers\FlutterwavePaymentController::class, 'initializeSubscriptionPayment'])->name('payment.initialize-subscription');
    Route::post('/payment/initialize-tokens', [App\Http\Controllers\FlutterwavePaymentController::class, 'initializeTokenPayment'])->name('payment.initialize-tokens');
    Route::post('/payment/initialize-reportcard-fees', [App\Http\Controllers\FlutterwavePaymentController::class, 'initializeReportCardFeePayment'])->name('payment.initialize-reportcard-fees');
    Route::get('/payment/callback', [App\Http\Controllers\FlutterwavePaymentController::class, 'handlePaymentCallback'])->name('payment.callback');
    Route::post('/webhook', [App\Http\Controllers\FlutterwavePaymentController::class, 'handleWebhook'])->name('webhook');
});

// Token Management Routes
Route::middleware('auth:student')->prefix('tokens')->name('tokens.')->group(function () {
    Route::get('/', [TokenController::class, 'index'])->name('index');
    Route::get('/dashboard', [TokenController::class, 'dashboard'])->name('dashboard');
    Route::post('/purchase', [TokenController::class, 'purchase'])->name('purchase');
    Route::post('/purchase-subscription', [TokenController::class, 'purchaseSubscription'])->name('purchase-subscription');
    Route::get('/payment/callback/{gateway}', [TokenController::class, 'paymentCallback'])->name('payment.callback');
    Route::get('/mobile-money/form/{subscriptionId}', [TokenController::class, 'showMobileMoneyForm'])->name('mobile-money.form');
    Route::post('/mobile-money/process/{subscriptionId}', [TokenController::class, 'processMobileMoney'])->name('mobile-money.process');
});

// Subscription Routes
Route::middleware('auth:student')->prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', [SubscriptionController::class, 'index'])->name('index');
    Route::post('/purchase', [SubscriptionController::class, 'purchase'])->name('purchase');
    Route::get('/payment/{studentSubscription}', [SubscriptionController::class, 'payment'])->name('payment');
    Route::post('/payment/complete/{studentSubscription}', [SubscriptionController::class, 'completePayment'])->name('payment.complete');
});

// Student Report Card Routes
Route::prefix('student/report-cards')->name('student.report-cards.')->group(function () {
    Route::get('/', [ReportCardController::class, 'studentIndex'])->name('index')->middleware('auth:student');
    Route::get('/{reportCard}', [ReportCardController::class, 'studentShow'])->name('show')->middleware('auth:student');
    Route::get('/{reportCard}/download', [ReportCardController::class, 'studentDownload'])->name('download')->middleware('auth:student');
});

// Public countdown endpoint (completely separate)
Route::get('/api/report-cards/countdown', [ReportCardController::class, 'getCountdown'])->name('student.report-cards.countdown');

// Secretary Token Management Routes
Route::middleware(['auth:admin,staff'])->prefix('tokens')->name('tokens.')->group(function () {
    Route::get('/secretary', [TokenController::class, 'secretaryIndex'])->name('secretary-index');
    Route::post('/add/{student}', [TokenController::class, 'secretaryAddTokens'])->name('add');
    Route::post('/deduct/{student}', [TokenController::class, 'secretaryDeductTokens'])->name('deduct');
    Route::get('/history/{student?}', [TokenController::class, 'transactionHistory'])->name('history');
});

// Admin Subscription Management Routes
Route::middleware(['auth:admin'])->prefix('admin/subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', [SubscriptionController::class, 'adminIndex'])->name('admin-index');
    Route::get('/create', [SubscriptionController::class, 'adminCreate'])->name('admin-create');
    Route::post('/', [SubscriptionController::class, 'adminStore'])->name('admin-store');
    Route::get('/{subscription}/edit', [SubscriptionController::class, 'adminEdit'])->name('admin-edit');
    Route::put('/{subscription}', [SubscriptionController::class, 'adminUpdate'])->name('admin-update');
    Route::post('/{subscription}/toggle', [SubscriptionController::class, 'adminToggle'])->name('admin-toggle');
    Route::delete('/{subscription}', [SubscriptionController::class, 'adminDestroy'])->name('admin-destroy');
    Route::get('/student-subscriptions', [SubscriptionController::class, 'studentSubscriptions'])->name('student-subscriptions');
    Route::post('/extend/{studentSubscription}', [SubscriptionController::class, 'extendSubscription'])->name('extend');
});

// To Whom Letter Routes - Accessible by students, accountants, and admins
Route::middleware(['auth:admin,staff,student'])->prefix('to-whom')->name('to-whom.')->group(function () {
    Route::get('/', [ToWhomLetterController::class, 'index'])->name('index');
    Route::get('/create', [ToWhomLetterController::class, 'create'])->name('create');
    Route::post('/', [ToWhomLetterController::class, 'store'])->name('store');
    Route::get('/search/students', [ToWhomLetterController::class, 'searchStudents'])->name('search-students');
    Route::get('/{letter}', [ToWhomLetterController::class, 'show'])->name('show');
    Route::get('/{letter}/download', [ToWhomLetterController::class, 'download'])->name('download');
    Route::delete('/{letter}', [ToWhomLetterController::class, 'destroy'])->name('destroy');
});

// Babyeyi Routes
// Admins can view/download existing documents only.
// Staff can create/edit/publish/generate documents.
Route::prefix('babyeyi')->name('babyeyi.')->group(function () {
    // IMPORTANT: Specific routes MUST come before generic {babyeyi} parameter route
    
    // Staff write actions (must be before generic routes)
    Route::middleware(['auth:staff'])->group(function () {
        Route::get('/create', [BabyeyiController::class, 'create'])->name('create');
        Route::post('/', [BabyeyiController::class, 'store'])->name('store');
        
        // Bulk operations routes (must be before generic /{babyeyi} route)
        Route::get('/bulk/create', [BabyeyiController::class, 'bulkCreate'])->name('bulk.create');
        Route::post('/bulk/store', [BabyeyiController::class, 'bulkStore'])->name('bulk.store');
        Route::get('/bulk/operations', [BabyeyiController::class, 'bulkOperations'])->name('bulk.operations');
        Route::post('/bulk/publish', [BabyeyiController::class, 'bulkPublish'])->name('bulk.publish');
        Route::post('/bulk/regenerate', [BabyeyiController::class, 'regenerateForTerm'])->name('bulk.regenerate');
        Route::post('/quick-generate/{termId}', [BabyeyiController::class, 'quickGenerate'])->name('quick-generate');
    });

    // Utility routes (must be before generic /{babyeyi} route)
    Route::middleware(['auth:admin,staff'])->group(function () {
        Route::get('/get-terms/{academicYearId}', [BabyeyiController::class, 'getTerms'])->name('get-terms');
        Route::get('/get-courses', [BabyeyiController::class, 'getCourses'])->name('get-courses');
    });

    // View-only routes including generic show (admin + staff)
    Route::middleware(['auth:admin,staff'])->group(function () {
        Route::get('/', [BabyeyiController::class, 'index'])->name('index');
    });

    Route::middleware(['auth:staff'])->group(function () {
        Route::get('/{babyeyi}/edit', [BabyeyiController::class, 'edit'])->name('edit');
        Route::put('/{babyeyi}', [BabyeyiController::class, 'update'])->name('update');
        Route::delete('/{babyeyi}', [BabyeyiController::class, 'destroy'])->name('destroy');
        Route::post('/{babyeyi}/publish', [BabyeyiController::class, 'publish'])->name('publish');
    });

    // Generic show routes (MUST be last - catches {babyeyi} parameter)
    Route::middleware(['auth:admin,staff'])->group(function () {
        Route::get('/{babyeyi}', [BabyeyiController::class, 'show'])->name('show');
        Route::get('/{babyeyi}/preview', [BabyeyiController::class, 'preview'])->name('preview');
        Route::get('/{babyeyi}/download', [BabyeyiController::class, 'download'])->name('download');
    });
});

// STUDENT: Babyeyi Routes - For authenticated students to view their course-specific babyeyis
Route::middleware(['auth:student'])->prefix('my-babyeyi')->name('student.babyeyi.')->group(function () {
    Route::get('/', [BabyeyiController::class, 'studentDashboard'])->name('dashboard');
    Route::get('/{babyeyi}', [BabyeyiController::class, 'studentShow'])->name('show');
    Route::get('/{babyeyi}/download', [BabyeyiController::class, 'studentDownload'])->name('download');
});

// Absolute simplest test route
Route::post('/simple-test', function() {
    return 'simple test works';
})->withoutMiddleware(['web']);

// Test route without middleware
Route::post('/no-middleware-test', function() {
    return response()->json(['message' => 'No middleware test works!']);
})->withoutMiddleware(['web']);

// Simple test signaling route
Route::post('/test-signaling/{liveClassId}', function(Request $request, $liveClassId) {
    \Log::info('Test signaling route called', [
        'liveClassId' => $liveClassId,
        'request_data' => $request->all(),
        'headers' => $request->headers->all()
    ]);
    
    return response()->json([
        'message' => 'Test signaling works!',
        'liveClassId' => $liveClassId,
        'request_data' => $request->all()
    ]);
});

// Live Class Signaling (polling-based WebRTC) - NO WEB MIDDLEWARE
Route::prefix('live-signaling')->name('live-signaling.')->group(function () {
    Route::post('/{liveClassId}/send', [\App\Http\Controllers\LiveClassSignalingController::class, 'send'])->name('send');
    Route::get('/{liveClassId}/poll', [\App\Http\Controllers\LiveClassSignalingController::class, 'poll'])->name('poll');
    Route::get('/{liveClassId}/participants', [\App\Http\Controllers\LiveClassSignalingController::class, 'participants'])->name('participants');
})->withoutMiddleware(['web']);
// Public Resource Routes
// Public Resources Routes
Route::prefix('resources')->name('public.resources.')->group(function () {
    Route::get('/', [App\Http\Controllers\PublicResourceController::class, 'index'])->name('index');
    Route::get('/{id}', [App\Http\Controllers\PublicResourceController::class, 'show'])->name('show');
    Route::get('/{id}/download', [App\Http\Controllers\PublicResourceController::class, 'downloadDocument'])->name('download');
    Route::get('/{id}/preview', [App\Http\Controllers\PublicResourceController::class, 'previewDocument'])->name('preview');
    Route::get('/{id}/payment', [App\Http\Controllers\PublicResourceController::class, 'showPayment'])->name('payment');
    Route::post('/{id}/payment', [App\Http\Controllers\PublicResourceController::class, 'processPayment'])->name('payment.process');
    Route::get('/{id}/payment/callback', [App\Http\Controllers\PublicResourceController::class, 'handlePaymentCallback'])->name('payment.callback');
    Route::get('/{id}/payment/success', [App\Http\Controllers\PublicResourceController::class, 'paymentSuccess'])->name('payment.success');
});

// Development: Populate lesson_branch_course table with sample data
Route::prefix('dev')->name('dev.')->group(function () {
    Route::get('/populate-lesson-branch-course', [PopulateLessonBranchCourseController::class, 'populate'])->name('populate-lbc');
    Route::get('/check-lesson-branch-course', [PopulateLessonBranchCourseController::class, 'check'])->name('check-lbc');
});

// Public Exam Access Routes
Route::prefix('exam')->name('exam.')->group(function () {
    Route::get('/{exam}/payment', function(Exam $exam) {
        if (!$exam->requiresPayment()) {
            return redirect()->route('exam.take', $exam);
        }
        return view('exam.payment', compact('exam'));
    })->name('payment');
    
    Route::post('/{exam}/payment', function(Request $request, Exam $exam) {
        // Process payment logic here
        return redirect()->route('exam.take', $exam)->with('success', 'Payment processed successfully');
    })->name('payment.process');
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});