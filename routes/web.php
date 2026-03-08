<?php

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
use App\Http\Controllers\WebRTCController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\ExamTakingController;
use App\Http\Controllers\ExamResultsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SecretaryAuthController;
use App\Http\Controllers\ReportCardController;

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
    $systemName = Cache::get('esa_system_name', 'ESA-Parnel');
    $systemLogo = Cache::get('esa_system_logo');
    $publicAnnouncements = Announcement::where('audience', 'public')->latest()->take(10)->get();

    return view('welcome', compact('systemName', 'systemLogo', 'publicAnnouncements'));
})->name('welcome');

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

// Student registration
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
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

// Admin auth routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});


if (! Route::has('admin.teachers.index')) {
    Route::middleware('auth:admin')->get('/admin/teachers', [TeacherController::class, 'index'])->name('admin.teachers.index');
}
// -----------------------------------------------------------------------------

// Admin protected routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminAuthController::class, 'dashboard'])->name('dashboard');

    Route::resource('students', StudentController::class);
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

    // Teacher assignment
    Route::get('teachers/{teacher}/assign-course', [TeacherController::class, 'assignCourse'])->name('teachers.assignCourse');
    Route::post('teachers/{teacher}/assign-course', [TeacherController::class, 'storeCourseAssignment'])->name('teachers.storeCourseAssignment');

    // Student fees
    Route::get('students/{student}/fees', [StudentController::class, 'showStudentFees'])->name('students.fees');
    Route::patch('/students/{student}/discount', [StudentController::class, 'updateDiscount'])->name('students.updateDiscount');

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

    // Live Classes
    Route::resource('live-classes', LiveClassController::class);
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

    // NESA Registration Management (disabled)
    // routes removed per request

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
        Route::get('/accounting/export/cashbook', [\App\Http\Controllers\AccountingController::class, 'exportCashbook'])->name('accounting.export.cashbook');
        Route::get('/accounting/export/balance-sheet', [\App\Http\Controllers\AccountingController::class, 'exportBalanceSheet'])->name('accounting.export.balance-sheet');
        Route::get('/accounting/export/trial-balance', [\App\Http\Controllers\AccountingController::class, 'exportTrialBalance'])->name('accounting.export.trial-balance');
        Route::get('/accounting/export/cash-flow-statement', [\App\Http\Controllers\AccountingController::class, 'exportCashFlowStatement'])->name('accounting.export.cash-flow-statement');
    });

    // Document Management
    Route::get('documents', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'index'])->name('documents.index');
    Route::get('documents/{id}/edit', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'edit'])->name('documents.edit');
    Route::put('documents/{id}', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'update'])->name('documents.update');
    Route::post('documents/bulk-update', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'bulkUpdate'])->name('documents.bulk.update');
    Route::get('documents/{id}/download', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'downloadDocument'])->name('documents.download');
    Route::get('documents/{id}/preview', [\App\Http\Controllers\Admin\DocumentManagementController::class, 'previewDocument'])->name('documents.preview');

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

    // Report Cards
    Route::get('report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
    Route::get('report-cards/create', [ReportCardController::class, 'create'])->name('report-cards.create');
    Route::post('report-cards', [ReportCardController::class, 'store'])->name('report-cards.store');
    Route::get('report-cards/{reportCard}', [ReportCardController::class, 'show'])->name('report-cards.show');
    Route::get('report-cards/{reportCard}/edit', [ReportCardController::class, 'edit'])->name('report-cards.edit');
    Route::put('report-cards/{reportCard}', [ReportCardController::class, 'update'])->name('report-cards.update');
    Route::delete('report-cards/{reportCard}', [ReportCardController::class, 'destroy'])->name('report-cards.destroy');
    Route::get('report-cards/{reportCard}/print', [ReportCardController::class, 'printReport'])->name('report-cards.print');
    Route::post('report-cards/generate-class', [ReportCardController::class, 'generateForClass'])->name('report-cards.generate-class');
    Route::get('report-cards/print-class', [ReportCardController::class, 'printClassReports'])->name('report-cards.print-class');

    // School Information
    Route::get('school-information', [\App\Http\Controllers\SchoolInformationController::class, 'edit'])->name('school-information.edit');
    Route::put('school-information', [\App\Http\Controllers\SchoolInformationController::class, 'update'])->name('school-information.update');

    Route::get('chat-test', function() {
        return view('admin.profile.test');
    })->name('chat.test');
});

// Secretary protected routes
Route::middleware(['auth:staff'])->prefix('secretary')->name('secretary.')->group(function () {
    Route::get('/login', [SecretaryAuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [SecretaryAuthController::class, 'login'])->name('login');
    Route::post('/logout', [SecretaryAuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [SecretaryAuthController::class, 'dashboard'])->name('dashboard');
    
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
    $exams = \App\Models\Exam::where('is_active', true)
        ->whereHas('courses', function($query) use ($courseId) {
            $query->where('courses.id', $courseId);
        })
        ->orderBy('title')
        ->get(['id', 'title']);
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

Route::get('/dos/timetable', [\App\Http\Controllers\DosTimetableController::class, 'index'])->name('dos.timetable.index');
Route::get('/dos/timetable/{id}/edit', [\App\Http\Controllers\DosTimetableController::class, 'edit'])->name('dos.timetable.edit');
Route::post('/dos/timetable/{id}/update', [\App\Http\Controllers\DosTimetableController::class, 'update'])->name('dos.timetable.update');
Route::get('/dos/timetable/create', [\App\Http\Controllers\DosTimetableController::class, 'create'])->name('dos.timetable.create');
Route::post('/dos/timetable/store', [\App\Http\Controllers\DosTimetableController::class, 'store'])->name('dos.timetable.store');

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
         // ===== PAYMENT ROUTES - ADD THESE HERE =====
        Route::post('/students/{student}/pay-cash', [StudentController::class, 'payCash'])->name('students.pay-cash');
        Route::get('/students/{student}/payments/ajax', [StudentController::class, 'ajaxPayments'])->name('students.payments.ajax');
        Route::get('/students/{student}/transcript/ajax', [StudentController::class, 'transcriptAjax'])->name('students.transcript.ajax');
        Route::get('/students/{student}/transcript/send', [StudentController::class, 'sendTranscript'])->name('students.transcript.send');
        // ============================================


    Route::middleware('auth:staff')->group(function () {
        Route::get('dashboard', [StaffAuthController::class, 'dashboard'])->name('dashboard');
        
        // Transaction Management Routes (FIXED)
        Route::get('/transactions/{transaction}/edit', [StudentController::class, 'editTransaction'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [StudentController::class, 'updateTransaction'])->name('transactions.update');
        Route::delete('/transactions/{transaction}', [StudentController::class, 'deleteTransaction'])->name('transactions.destroy');
            // Payment Routes - ADD THESE



        // Student Management Routes
        Route::get('/students/{student}/edit', [StudentController::class, 'editByStaff'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'updateByStaff'])->name('students.update');
        Route::put('/students/{student}/nesa', [StudentController::class, 'registerNesa'])->name('students.nesa');
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
        Route::get('/lesson-units', [LessonDetailsController::class, 'index'])->name('lesson-units.index');
        Route::get('/lessons/{lesson}/units/create', [LessonDetailsController::class, 'create'])->name('lesson-units.create');
        Route::post('/lessons/{lesson}/units', [LessonDetailsController::class, 'store'])->name('lesson-units.store');
        Route::get('/lesson-units/{unit}', [LessonDetailsController::class, 'show'])->name('lesson-units.show');
        Route::get('/lesson-units/{unit}/edit', [LessonDetailsController::class, 'edit'])->name('lesson-units.edit');
        Route::put('/lesson-units/{unit}', [LessonDetailsController::class, 'update'])->name('lesson-units.update');
        Route::delete('/lesson-units/{unit}', [LessonDetailsController::class, 'destroy'])->name('lesson-units.destroy');
        Route::get('/lessons/{lesson}/details/create', [LessonDetailsController::class, 'create'])->name('lesson.details.create');
        Route::post('/lessons/{lesson}/details', [LessonDetailsController::class, 'store'])->name('lesson.details.store');

        // Subscription Management Routes for Staff
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [SubscriptionController::class, 'staffIndex'])->name('index');
            Route::get('/create', [SubscriptionController::class, 'staffCreate'])->name('create');
            Route::post('/', [SubscriptionController::class, 'staffStore'])->name('store');
            Route::get('/{studentSubscription}', [SubscriptionController::class, 'staffShow'])->name('show');
            Route::get('/reports', [SubscriptionController::class, 'staffReports'])->name('reports');
        });
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
| Special routes for authorized user for editing/deleting transactions
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:admin,staff'])->group(function () {
    Route::get('/admin/transactions/{transaction}/edit', [StudentController::class, 'editTransaction'])->name('admin.transactions.edit');
    Route::put('/admin/transactions/{transaction}', [StudentController::class, 'editTransaction'])->name('admin.transactions.update');
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
    Route::middleware('auth:teacher')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\TeacherAuthController::class, 'dashboard'])->name('dashboard');
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

// Exams
Route::resource('exams', \App\Http\Controllers\ExamController::class);
Route::get('/api/courses/{course}/lessons', function($courseId) {
    $lessons = \App\Models\Lesson::where('course_id', $courseId)->get(['id', 'title']);
    return response()->json(['lessons' => $lessons]);
});
Route::get('/api/lessons/{lesson}/marks', function($lessonId) {
    $lesson = \App\Models\Lesson::find($lessonId);
    return response()->json(['total_marks' => $lesson ? $lesson->total_marks : 100]);
});
Route::get('/exams/{exam}/questions', [\App\Http\Controllers\ExamQuestionController::class, 'index'])->name('exams.questions');
Route::get('/exams/{exam}/questions/create', [\App\Http\Controllers\ExamQuestionController::class, 'create'])->name('exams.questions.create');
Route::post('/exams/{exam}/questions', [\App\Http\Controllers\ExamQuestionController::class, 'store'])->name('exams.questions.store');
Route::post('/exams/{exam}/questions/import', [\App\Http\Controllers\ExamQuestionController::class, 'import'])->name('exams.questions.import');

Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::resource('marks', \App\Http\Controllers\MarksController::class);
});

Route::get('/api/courses/{course}/students', function($courseId) {
    $course = \App\Models\Course::with('students')->find($courseId);
    $students = $course ? $course->students->map(function($s) {
        return [
            'id' => $s->id,
            'name' => $s->name,
            'student_id' => $s->student_id ?? null,
        ];
    }) : [];
    return response()->json(['students' => $students]);
});

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

Route::get('/dos/dailies', [App\Http\Controllers\DosDailyController::class, 'index'])->name('dos.dailies.index');
Route::get('/dos/dailies/{daily}', [App\Http\Controllers\DosDailyController::class, 'show'])->name('dos.dailies.show');
Route::post('/dos/dailies/{daily}/approve', [App\Http\Controllers\DosDailyController::class, 'approve'])->name('dos.dailies.approve');
Route::post('/dos/dailies/{daily}/attendance', [App\Http\Controllers\DosDailyController::class, 'updateAttendance'])->name('dos.dailies.attendance');

Route::get('/api/lessons/{lesson}/course', function($lessonId) {
    $lesson = \App\Models\Lesson::with('course')->find($lessonId);
    return response()->json(['course_id' => $lesson?->course?->id]);
});

Route::middleware(['auth:admin,staff,teacher'])->group(function () {
    Route::resource('marks', \App\Http\Controllers\MarksController::class);
});

Route::get('/teacher/dailies', [TeacherDailyController::class, 'index'])->name('teacher.dailies.index');

Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('resources/create', [\App\Http\Controllers\Teacher\ResourceController::class, 'create'])->name('resources.create');
    Route::post('resources', [\App\Http\Controllers\Teacher\ResourceController::class, 'store'])->name('resources.store');
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
Route::post('/students', [\App\Http\Controllers\StudentController::class, 'store'])->name('students.store');

Route::get('/courses/registration-fee', [StudentController::class, 'getRegistrationFee'])->name('courses.registration_fee');
Route::post('/students/flutterwave/initialize', [\App\Http\Controllers\FlutterwaveController::class, 'initializePayment'])->name('students.flutterwave.initialize');
Route::get('/payment/callback', [StudentController::class, 'paymentCallback'])->name('students.payment.callback');

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
});

// Teacher Live Class Routes
Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/live-classes', [LiveClassController::class, 'index'])->name('live-classes.index');
    Route::get('/live-classes/{liveClass}/room', [LiveClassController::class, 'room'])->name('live-classes.room');
    Route::post('/live-classes/{liveClass}/start', [LiveClassController::class, 'start'])->name('live-classes.start');
    Route::post('/live-classes/{liveClass}/end', [LiveClassController::class, 'end'])->name('live-classes.end');
});

// Live Class Request Management (accessible from room)
Route::middleware(['auth:admin,staff,teacher'])->group(function () {
    Route::post('live-classes/{liveClass}/requests/{requestId}/approve', [LiveClassController::class, 'approveRequest'])->name('live-classes.requests.approve');
    Route::post('live-classes/{liveClass}/requests/{requestId}/reject', [LiveClassController::class, 'rejectRequest'])->name('live-classes.requests.reject');
});

// WebRTC Routes (Protected)
Route::middleware(['auth:admin,staff,teacher,student'])->prefix('webrtc')->name('webrtc.')->group(function () {
    Route::post('/signaling/{roomId}', [WebRTCController::class, 'signaling'])->name('signaling');
    Route::post('/join/{roomId}', [WebRTCController::class, 'joinRoom'])->name('join');
    Route::post('/leave/{roomId}', [WebRTCController::class, 'leaveRoom'])->name('leave');
    Route::post('/chat/{roomId}', [WebRTCController::class, 'chatMessage'])->name('chat');
});

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

Route::get('/google/redirect', [App\Http\Controllers\GoogleMeetController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [App\Http\Controllers\GoogleMeetController::class, 'handleGoogleCallback'])->name('google.callback');
Route::post('/google/create-meet', [App\Http\Controllers\GoogleMeetController::class, 'createMeet'])->name('google.create-meet');

Route::get('/student/payment', [StudentController::class, 'showPaymentPage'])->name('student.payment');
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

Route::get('resources/{resource}/view-document', [ResourceController::class, 'viewDocument'])->name('resources.viewDocument');
Route::get('resources/{resource}/stream-video', [ResourceController::class, 'streamVideo'])->name('resources.streamVideo');

// Teacher resource routes

Route::get('/teacher/course/{course}/students', [\App\Http\Controllers\TeacherDailyController::class, 'getStudents']);
Route::put('teacher/resources/{resource}', [\App\Http\Controllers\Teacher\ResourceController::class, 'update'])->name('teacher.resources.update');
Route::get('teacher/resources/{resource}/edit', [\App\Http\Controllers\Teacher\ResourceController::class, 'edit'])->name('teacher.resources.edit');
Route::delete('teacher/resources/{id}', [\App\Http\Controllers\Teacher\ResourceController::class, 'destroy'])->name('teacher.resources.destroy');
Route::get('teacher/resources/{resource}/view-document', [\App\Http\Controllers\Teacher\ResourceController::class, 'viewDocument'])->name('teacher.resources.viewDocument');

// Blog Routes
Route::get('/blogs', [BlogController::class, 'publicIndex'])->name('blogs.index');
Route::get('/blogs/{blog:slug}', [BlogController::class, 'show'])->name('blogs.show');

// Admin Blog Routes
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/create', [BlogController::class, 'create'])->name('blogs.create');
    Route::post('/blogs', [BlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{blog}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{blog}', [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy');
    Route::post('/blogs/{blog}/toggle-featured', [BlogController::class, 'toggleFeatured'])->name('blogs.toggle-featured');
    Route::delete('/blogs/images/{image}', [BlogController::class, 'deleteImage'])->name('blogs.delete-image');
    Route::delete('/blogs/documents/{document}', [BlogController::class, 'deleteDocument'])->name('blogs.delete-document');
});

Route::get('dos/courses', [\App\Http\Controllers\DosCourseController::class, 'index'])->name('dos.courses.index');
Route::post('dos/courses/assign-lesson', [\App\Http\Controllers\DosCourseController::class, 'assignLesson'])->name('dos.courses.assignLesson');

// Enhanced Exam System Routes
Route::middleware(['auth:admin,staff,teacher'])->prefix('exams')->name('exams.')->group(function () {
    // Question Banks
    Route::resource('question-banks', QuestionBankController::class);
    Route::get('question-banks/{questionBank}/add-question', [QuestionBankController::class, 'addQuestion'])->name('question-banks.add-question');
    Route::post('question-banks/{questionBank}/questions', [QuestionBankController::class, 'storeQuestion'])->name('question-banks.store-question');
    Route::post('question-banks/{questionBank}/import', [QuestionBankController::class, 'importQuestions'])->name('question-banks.import');
    Route::get('question-banks/{questionBank}/export', [QuestionBankController::class, 'exportQuestions'])->name('question-banks.export');
    
    // Enhanced Exam Management
    Route::get('{exam}/results', [ExamResultsController::class, 'index'])->name('results');
    Route::get('{exam}/results/{attempt}', [ExamResultsController::class, 'studentResults'])->name('student-results');
    Route::get('{exam}/analytics', [ExamResultsController::class, 'analytics'])->name('analytics');
    Route::get('{exam}/report', [ExamResultsController::class, 'generateReport'])->name('report');
    Route::get('{exam}/export', [ExamResultsController::class, 'exportResults'])->name('export');
    Route::post('{exam}/release-results', [ExamResultsController::class, 'releaseResults'])->name('release-results');
    Route::post('{exam}/grade-essay', [ExamResultsController::class, 'gradeEssay'])->name('grade-essay');
    Route::post('{exam}/bulk-grade', [ExamResultsController::class, 'bulkGrade'])->name('bulk-grade');
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
});

// Student Exam Taking Routes
Route::middleware('auth:student')->prefix('exams')->name('exams.')->group(function () {
    Route::get('{exam}/start', [ExamTakingController::class, 'start'])->name('start');
    Route::get('{exam}/take/{session}/{questionNumber?}', [ExamTakingController::class, 'takeQuestion'])->name('take-question');
    Route::post('{exam}/save-answer/{session}/{questionNumber}', [ExamTakingController::class, 'saveAnswer'])->name('save-answer');
    Route::post('{exam}/submit/{session}', [ExamTakingController::class, 'submitExam'])->name('submit');
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
});

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