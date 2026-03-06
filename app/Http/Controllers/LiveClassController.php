<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LiveClassController extends Controller
{
    public function index()
    {
        $liveClasses = LiveClass::with(['branches', 'courses', 'lessons', 'teacher'])
            ->orderBy('start_time', 'desc')
            ->paginate(10);
        
        return view('admin.live-classes.index', compact('liveClasses'));
    }

    /**
     * Student-specific live classes index - only shows enrolled courses
     */
    public function studentIndex()
    {
        $student = Auth::guard('student')->user();
        
        // Get only live classes for student's enrolled courses
        $liveClasses = LiveClass::whereHas('courses', function($query) use ($student) {
            $query->where('course_id', $student->course_id);
        })
        ->with(['branches', 'courses', 'lessons', 'teacher'])
        ->orderBy('start_time', 'desc')
        ->paginate(10);

        return view('students.live-classes.index', compact('liveClasses'));
    }

    public function homepageLiveClasses()
    {
        // Get active live classes that are currently running or scheduled
        $query = LiveClass::with(['branches', 'courses', 'lessons', 'teacher'])
            ->whereIn('status', ['scheduled', 'active'])
            ->where('end_time', '>=', now())  // Only check that it hasn't ended
            ->orderBy('start_time', 'asc');
        
        // If student is logged in, only show classes for their courses
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $query->whereHas('courses', function($q) use ($student) {
                $q->where('courses.id', $student->course_id);
            });
        }
        
        $liveClasses = $query->get();
            
        // Debug logging
        \Log::info('Homepage Live Classes Query', [
            'current_time' => now(),
            'student_logged_in' => Auth::guard('student')->check(),
            'student_course_id' => Auth::guard('student')->check() ? Auth::guard('student')->user()->course_id : null,
            'count' => $liveClasses->count(),
            'classes' => $liveClasses->map(function($class) {
                return [
                    'id' => $class->id,
                    'title' => $class->title,
                    'status' => $class->status,
                    'start_time' => $class->start_time,
                    'end_time' => $class->end_time,
                ];
            })
        ]);
            
        return response()->json([
            'liveClasses' => $liveClasses,
            'count' => $liveClasses->count()
        ]);
    }

    public function checkClassAccess(LiveClass $liveClass)
    {
        $user = Auth::user();
        $response = [
            'canJoin' => false,
            'requiresPayment' => false,
            'paymentAmount' => 0,
            'currency' => 'USD',
            'reason' => '',
            'userType' => 'guest'
        ];

        // Check if user is authenticated
        if (!$user) {
            $response['requiresPayment'] = $liveClass->access_type === 'public' || $liveClass->access_type === 'both';
            $response['paymentAmount'] = $liveClass->public_fee;
            $response['currency'] = $liveClass->currency;
            $response['reason'] = $response['requiresPayment'] ? 'Payment required for public access' : 'Please login to access this class';
            return response()->json($response);
        }

        // Check student access
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $response['userType'] = 'student';
            
            // Check if student is enrolled in any of the class courses
            if (!$liveClass->courses->contains('id', $student->course_id)) {
                $response['reason'] = 'This is not your course';
                return response()->json($response);
            }
            
            // Student is enrolled - check if payment is required
            if ($liveClass->access_type === 'public' || $liveClass->access_type === 'both') {
                // Check if student has already paid
                $payment = \App\Models\LiveClassPayment::where('live_class_id', $liveClass->id)
                    ->where('user_id', $student->id)
                    ->where('user_type', 'student')
                    ->where('status', 'completed')
                    ->first();
                
                if (!$payment && $liveClass->public_fee > 0) {
                    $response['requiresPayment'] = true;
                    $response['paymentAmount'] = $liveClass->public_fee;
                    $response['currency'] = $liveClass->currency;
                    $response['reason'] = 'Payment required to join this class';
                    return response()->json($response);
                }
            }
            
            // Student can join
            $response['canJoin'] = true;
            $response['reason'] = 'Access granted';
            return response()->json($response);
        }

        // Check public access
        if ($liveClass->access_type === 'public' || $liveClass->access_type === 'both') {
            $response['userType'] = 'public';
            $response['requiresPayment'] = $liveClass->public_fee > 0;
            $response['paymentAmount'] = $liveClass->public_fee;
            $response['currency'] = $liveClass->currency;
            
            if ($response['requiresPayment']) {
                // Check if already paid
                $hasPaid = \App\Models\LiveClassPayment::where('live_class_id', $liveClass->id)
                    ->where('email', $user->email)
                    ->where('status', 'completed')
                    ->exists();
                    
                if ($hasPaid) {
                    $response['canJoin'] = true;
                    $response['requiresPayment'] = false;
                } else {
                    $response['reason'] = 'Payment required to join this class';
                }
            } else {
                $response['canJoin'] = true;
            }
        } else {
            $response['reason'] = 'This class is for students only';
        }

        return response()->json($response);
    }

    public function join(LiveClass $liveClass)
    {
        $user = Auth::user();
        
        // Check device restriction
        if ($liveClass->device_restriction) {
            $existingSession = \App\Models\LiveClassParticipant::where('live_class_id', $liveClass->id)
                ->where('email', $user->email)
                ->where('is_active', true)
                ->where('left_at', null)
                ->first();
                
            if ($existingSession) {
                return redirect()->back()->with('error', 'You already have an active session for this class. Please close the existing session first.');
            }
        }

        // Create participant record
        $participant = \App\Models\LiveClassParticipant::create([
            'live_class_id' => $liveClass->id,
            'user_id' => $user->id,
            'user_type' => Auth::guard('student')->check() ? 'student' : (Auth::guard('teacher')->check() ? 'teacher' : 'admin'),
            'email' => $user->email,
            'name' => $user->name ?? $user->first_name . ' ' . $user->last_name,
            'joined_at' => now(),
            'is_active' => true,
            'ip_address' => request()->ip(),
            'browser_info' => request()->userAgent(),
        ]);

        return redirect()->route('live-classes.room', $liveClass);
    }

    public function processPayment(Request $request, LiveClass $liveClass)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'payment_method' => 'required|string',
        ]);

        // Create payment record
        $payment = \App\Models\LiveClassPayment::create([
            'live_class_id' => $liveClass->id,
            'user_type' => 'public',
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
            'amount' => $liveClass->public_fee,
            'currency' => $liveClass->currency,
            'payment_method' => $request->payment_method,
            'transaction_id' => 'LC_' . Str::random(20),
            'status' => 'pending',
        ]);

        // Here you would integrate with your payment gateway (Flutterwave, etc.)
        // For now, we'll simulate successful payment
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'gateway_response' => ['simulated' => true],
        ]);

        return redirect()->route('live-classes.room', $liveClass)
            ->with('success', 'Payment successful! You can now join the live class.');
    }

    public function showPaymentForm(LiveClass $liveClass)
    {
        return view('live-classes.public-payment', compact('liveClass'));
    }

    /**
     * Show the form for creating a new live class
     */
    public function create()
    {
        $branches = Branch::all();
        $courses = Course::all();
        $lessons = Lesson::all();
        $teachers = Teacher::all();
        
        return view('admin.live-classes.create', compact('branches', 'courses', 'lessons', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branches' => 'required|array|min:1',
            'branches.*' => 'exists:branches,id',
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:courses,id',
            'lessons' => 'nullable|array',
            'lessons.*' => 'exists:lessons,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'access_type' => 'required|in:students_only,public,both',
            'public_fee' => 'nullable|numeric|min:0',
            'currency' => 'required_with:public_fee|string|size:3',
            'required_payment_percentage' => 'nullable|integer|min:0|max:100',
            'allow_public_requests' => 'sometimes|boolean',
            'max_participants' => 'nullable|integer|min:1',
            'auto_approve_online_students' => 'sometimes|boolean',
            'require_student_approval' => 'sometimes|boolean',
            'device_restriction' => 'sometimes|boolean',
            'recording_enabled' => 'sometimes|boolean',
            'chat_enabled' => 'sometimes|boolean',
            'screen_share_enabled' => 'sometimes|boolean',
        ]);

        $liveClass = LiveClass::create([
            'title' => $request->title,
            'summary' => $request->title, // Use title as summary for now
            'description' => $request->description,
            'meet_link' => '', // Empty meet_link for now
            'teacher_id' => $request->teacher_id ?: null,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'scheduled',
            'meeting_id' => 'meeting_' . Str::random(32),
            'password' => Str::random(8),
            'access_type' => $request->access_type,
            'public_fee' => $request->public_fee ?? 0,
            'currency' => $request->currency ?? 'USD',
            'required_payment_percentage' => $request->required_payment_percentage ?? 0,
            'allow_public_requests' => $request->has('allow_public_requests'),
            'max_participants' => $request->max_participants ?: null,
            'auto_approve_online_students' => $request->has('auto_approve_online_students'),
            'require_student_approval' => $request->has('require_student_approval'),
            'device_restriction' => $request->has('device_restriction'),
            'recording_enabled' => $request->has('recording_enabled'),
            'chat_enabled' => $request->has('chat_enabled'),
            'screen_share_enabled' => $request->has('screen_share_enabled'),
        ]);

        // Attach relationships
        $liveClass->branches()->attach($request->branches);
        $liveClass->courses()->attach($request->courses);
        if ($request->has('lessons') && is_array($request->lessons) && count($request->lessons) > 0) {
            $liveClass->lessons()->attach($request->lessons);
        }

        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class scheduled successfully!');
    }

    public function show(LiveClass $liveClass)
    {
        $user = Auth::user();
        
        // Check permissions
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            if (!$liveClass->courses->contains('id', $student->course_id)) {
                abort(403, 'You are not authorized to join this class');
            }
        }

        $liveClass->load(['branches', 'courses', 'lessons', 'teacher']);
        
        return view('admin.live-classes.show', compact('liveClass'));
    }

    /**
     * Student-specific live class show - only for enrolled courses
     */
    public function studentShow(LiveClass $liveClass)
    {
        $student = Auth::guard('student')->user();
        
        // Verify student is enrolled in this live class's course
        if (!$liveClass->courses->contains('id', $student->course_id)) {
            abort(403, 'You are not authorized to access this live class');
        }

        $liveClass->load(['branches', 'courses', 'lessons', 'teacher']);
        
        return view('students.live-classes.show', compact('liveClass'));
    }

    public function edit(LiveClass $liveClass)
    {
        $branches = Branch::all();
        $courses = Course::all();
        $lessons = Lesson::all();
        $teachers = Teacher::all();
        
        return view('admin.live-classes.edit', compact('liveClass', 'branches', 'courses', 'lessons', 'teachers'));
    }

    public function update(Request $request, LiveClass $liveClass)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branches' => 'required|array|min:1',
            'branches.*' => 'exists:branches,id',
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:courses,id',
            'lessons' => 'nullable|array',
            'lessons.*' => 'exists:lessons,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $liveClass->update([
            'title' => $request->title,
            'description' => $request->description,
            'teacher_id' => $request->teacher_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        // Update relationships
        $liveClass->branches()->sync($request->branches);
        $liveClass->courses()->sync($request->courses);
        $liveClass->lessons()->sync($request->lessons ?? []);

        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class updated successfully!');
    }

    public function destroy(LiveClass $liveClass)
    {
        $liveClass->delete();
        
        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class deleted successfully!');
    }

    public function start(LiveClass $liveClass)
    {
        $user = Auth::user();
        
        // Only teachers or admins can start classes
        if (!Auth::guard('admin')->check() && 
            !Auth::guard('staff')->check() && 
            !(Auth::guard('teacher')->check() && Auth::guard('teacher')->user()->id == $liveClass->teacher_id)) {
            abort(403, 'Only teachers can start live classes');
        }

        $liveClass->update([
            'status' => 'active',
            'start_time' => now(),
        ]);

        return redirect()->route('live-classes.room', $liveClass);
    }

    public function requestAccess(LiveClass $liveClass)
    {
        $user = Auth::user();
        
        if (!Auth::guard('student')->check()) {
            return back()->with('error', 'Only students can request access to classes');
        }

        $student = Auth::guard('student')->user();
        $student->load(['branch', 'course']);
        $liveClass->load(['courses', 'teacher']);
        
        // Check if already requested
        $existingRequest = $liveClass->requests()
            ->where('user_id', $student->id)
            ->where('user_type', 'student')
            ->first();
        
        if ($existingRequest) {
            if ($existingRequest->status === 'pending') {
                return back()->with('info', 'Your request is already pending approval');
            } elseif ($existingRequest->status === 'approved') {
                return redirect()->route('live-classes.join', $liveClass)
                    ->with('success', 'Your request has been approved! You can now join the class.');
            } else {
                return back()->with('error', 'Your request was rejected: ' . $existingRequest->rejected_reason);
            }
        }

        return view('live-classes.request', compact('liveClass', 'student'));
    }

    public function submitRequest(Request $request, LiveClass $liveClass)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $student = Auth::guard('student')->user();
        
        // Create request
        $liveClass->requests()->create([
            'user_id' => $student->id,
            'user_type' => 'student',
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()->route('student.live-classes.index')
            ->with('success', 'Your request has been submitted and is pending approval.');
    }

    public function publicJoin(Request $request, LiveClass $liveClass)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20'
        ]);

        $canJoin = $liveClass->canPublicUserJoin($request->email);
        
        if (!$canJoin['allowed']) {
            if (isset($canJoin['requires_payment'])) {
                return redirect()->route('live-classes.payment', $liveClass)
                    ->with(['email' => $request->email, 'name' => $request->name, 'phone' => $request->phone]);
            }
            return back()->with('error', $canJoin['reason']);
        }

        // Record participant
        $this->recordPublicParticipant($liveClass, $request->email, $request->name, $request->phone);

        return redirect()->route('live-classes.room', $liveClass);
    }

    public function manageRequests(LiveClass $liveClass)
    {
        $requests = $liveClass->requests()->with('user')->latest()->get();
        
        return view('admin.live-classes.requests', compact('liveClass', 'requests'));
    }

    public function approveRequest(Request $request, LiveClass $liveClass, $requestId)
    {
        $classRequest = $liveClass->requests()->findOrFail($requestId);
        $admin = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('staff')->user();
        
        $classRequest->approve($admin->id ?? null);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Request approved successfully']);
        }

        return back()->with('success', 'Request approved successfully');
    }

    public function rejectRequest(Request $request, LiveClass $liveClass, $requestId)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $data = json_decode($request->getContent(), true) ?? [];
            $reason = $data['reason'] ?? null;
        } else {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);
            $reason = $request->reason;
        }

        $classRequest = $liveClass->requests()->findOrFail($requestId);
        $admin = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('staff')->user();
        
        $classRequest->reject($reason, $admin->id ?? null);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Request rejected']);
        }

        return back()->with('success', 'Request rejected');
    }

    private function recordParticipant($liveClass, $user, $userType)
    {
        $participant = $liveClass->participants()->create([
            'user_id' => $user->id,
            'user_type' => $userType,
            'joined_at' => now(),
            'is_active' => true,
            'ip_address' => request()->ip(),
            'device_info' => [
                'user_agent' => request()->userAgent(),
                'device' => $this->getDeviceInfo()
            ]
        ]);

        return $participant;
    }

    private function recordPublicParticipant($liveClass, $email, $name, $phone)
    {
        $participant = $liveClass->participants()->create([
            'email' => $email,
            'name' => $name,
            'user_type' => 'public',
            'joined_at' => now(),
            'is_active' => true,
            'ip_address' => request()->ip(),
            'device_info' => [
                'user_agent' => request()->userAgent(),
                'device' => $this->getDeviceInfo()
            ]
        ]);

        return $participant;
    }

    private function getUserType($user)
    {
        if (Auth::guard('admin')->check()) return 'admin';
        if (Auth::guard('staff')->check()) return 'staff';
        if (Auth::guard('teacher')->check()) return 'teacher';
        return 'unknown';
    }

    private function getDeviceInfo()
    {
        $userAgent = request()->userAgent();
        
        // Simple device detection
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet/', $userAgent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    public function room(LiveClass $liveClass)
    {
        $user = Auth::user();
        
        // Check permissions and class status
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            if (!$liveClass->courses->contains('id', $student->course_id) || $liveClass->status !== 'active') {
                abort(403, 'You cannot join this class');
            }
            $isTeacher = false;
        } else {
            // Admin, staff, or teacher
            $isTeacher = Auth::guard('teacher')->check() && 
                        Auth::guard('teacher')->user()->id == $liveClass->teacher_id;
        }

        $liveClass->load(['branches', 'courses', 'lessons', 'teacher']);
        
        // Load student requests for teachers/admins
        $studentRequests = [];
        if (Auth::guard('admin')->check() || Auth::guard('teacher')->check() || Auth::guard('staff')->check()) {
            $studentRequests = $liveClass->requests()
                ->with('user')
                ->where('user_type', 'student')
                ->where('status', 'pending')
                ->latest()
                ->get();
        }
        
        return view('live-classes.room', compact('liveClass', 'isTeacher', 'studentRequests'));
    }

    public function end(LiveClass $liveClass)
    {
        $user = Auth::user();
        
        // Only teachers or admins can end classes
        if (!Auth::guard('admin')->check() && 
            !Auth::guard('staff')->check() && 
            !(Auth::guard('teacher')->check() && Auth::guard('teacher')->user()->id == $liveClass->teacher_id)) {
            abort(403, 'Only teachers can end live classes');
        }

        $liveClass->update([
            'status' => 'ended',
            'end_time' => now(),
        ]);

        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class ended successfully!');
    }
}
