<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\StudentSubscription;
use App\Models\Student;
use App\Models\StudentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::active()->orderBy('price')->get();
        $student = Auth::user();
        $activeSubscription = $student->activeSubscription;

        return view('subscriptions.index', compact('subscriptions', 'student', 'activeSubscription'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        $student = Auth::user();
        $subscription = Subscription::findOrFail($request->subscription_id);

        // Check if student already has active subscription
        if ($student->activeSubscription) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'You already have an active subscription');
        }

        $studentSubscription = $student->purchaseSubscription($subscription->id);

        return redirect()->route('subscriptions.payment', $studentSubscription->id)
            ->with('info', 'Please complete the payment to activate your subscription');
    }

    public function payment(StudentSubscription $studentSubscription)
    {
        // Ensure user owns this subscription
        if ($studentSubscription->student_id !== Auth::id()) {
            abort(403);
        }

        return view('subscriptions.payment', compact('studentSubscription'));
    }

    public function completePayment(Request $request, StudentSubscription $studentSubscription)
    {
        // Ensure user owns this subscription
        if ($studentSubscription->student_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'payment_reference' => 'required|string|max:255',
        ]);

        $studentSubscription->markAsPaid($request->payment_reference);

        return redirect()->route('dashboard')
            ->with('success', 'Subscription activated successfully! Tokens have been added to your account.');
    }

    public function adminIndex()
    {
        $subscriptions = Subscription::withCount('studentSubscriptions')->get();
        return view('subscriptions.admin-index', compact('subscriptions'));
    }

    public function adminCreate()
    {
        return view('subscriptions.admin-create');
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tokens_included' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'tier' => 'required|in:basic,standard,premium',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_recommended' => 'boolean',
            'features' => 'nullable|string',
        ]);

        // Convert features string to array
        $features = null;
        if ($request->filled('features')) {
            $features = array_filter(array_map('trim', explode("\n", $request->features)));
        }

        $subscription = Subscription::create([
            'name' => $request->name,
            'description' => $request->description,
            'tokens_included' => $request->tokens_included,
            'price' => $request->price,
            'tier' => $request->tier,
            'duration_days' => $request->duration_days,
            'is_active' => $request->boolean('is_active', true),
            'is_recommended' => $request->boolean('is_recommended', false),
            'features' => $features,
        ]);

        return redirect()->route('subscriptions.admin-index')
            ->with('success', 'Subscription created successfully');
    }

    public function adminEdit(Subscription $subscription)
    {
        return view('subscriptions.admin-edit', compact('subscription'));
    }

    public function adminUpdate(Request $request, Subscription $subscription)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tokens_included' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'tier' => 'required|in:basic,standard,premium',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_recommended' => 'boolean',
            'features' => 'nullable|string',
        ]);

        // Convert features string to array
        $features = null;
        if ($request->filled('features')) {
            $features = array_filter(array_map('trim', explode("\n", $request->features)));
        }

        $subscription->update([
            'name' => $request->name,
            'description' => $request->description,
            'tokens_included' => $request->tokens_included,
            'price' => $request->price,
            'tier' => $request->tier,
            'duration_days' => $request->duration_days,
            'is_active' => $request->boolean('is_active', true),
            'is_recommended' => $request->boolean('is_recommended', false),
            'features' => $features,
        ]);

        return redirect()->route('subscriptions.admin-index')
            ->with('success', 'Subscription updated successfully');
    }

    public function adminToggle(Subscription $subscription)
    {
        $subscription->update(['is_active' => !$subscription->is_active]);

        return redirect()->route('subscriptions.admin-index')
            ->with('success', 'Subscription status updated');
    }

    public function studentSubscriptions()
    {
        $subscriptions = StudentSubscription::with(['student', 'subscription'])
            ->latest()
            ->paginate(20);

        return view('subscriptions.student-subscriptions', compact('subscriptions'));
    }

    public function extendSubscription(Request $request, StudentSubscription $studentSubscription)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $studentSubscription->update([
            'ends_at' => $studentSubscription->ends_at->addDays($request->days),
        ]);

        return redirect()->back()
            ->with('success', 'Subscription extended successfully');
    }

    public function adminDestroy(Subscription $subscription)
    {
        // Check if subscription has any active student subscriptions
        if ($subscription->studentSubscriptions()->count() > 0) {
            return redirect()->route('subscriptions.admin-index')
                ->with('error', 'Cannot delete subscription with active student subscriptions');
        }

        $subscription->delete();

        return redirect()->route('subscriptions.admin-index')
            ->with('success', 'Subscription deleted successfully');
    }

    // Staff methods for recording subscription payments
    public function staffIndex()
    {
        $staff = auth('staff')->user();
        $subscriptions = StudentSubscription::with(['student', 'subscription'])
            ->whereHas('student', function ($query) use ($staff) {
                $branchIds = $staff->branches()->pluck('branches.id')->toArray();
                $query->whereIn('branch_id', $branchIds);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('staff.subscriptions.index', compact('subscriptions'));
    }

    public function staffCreate()
    {
        $staff = auth('staff')->user();
        $subscriptions = Subscription::active()->get();
        
        $branchIds = $staff->branches()->pluck('branches.id')->toArray();
        $students = Student::whereIn('branch_id', $branchIds)
            ->orderBy('full_name')
            ->get();

        return view('staff.subscriptions.create', compact('subscriptions', 'students'));
    }

    public function staffStore(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subscription_id' => 'required|exists:subscriptions,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,other',
            'payment_reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
        ]);

        $staff = auth('staff')->user();
        $student = Student::findOrFail($request->student_id);
        $subscription = Subscription::findOrFail($request->subscription_id);

        // Verify staff has access to this student
        $branchIds = $staff->branches()->pluck('branches.id')->toArray();
        if (!in_array($student->branch_id, $branchIds)) {
            return back()->with('error', 'You do not have access to this student.');
        }

        DB::beginTransaction();
        try {
            // Create student subscription
            $studentSubscription = StudentSubscription::create([
                'student_id' => $student->id,
                'subscription_id' => $subscription->id,
                'paid_amount' => $request->amount_paid,
                'payment_reference' => $request->payment_reference,
                'payment_status' => 'paid',
                'starts_at' => now(),
                'ends_at' => now()->addDays($subscription->duration_days),
                'is_active' => true,
            ]);

            // Create payment transaction record
            StudentTransaction::create([
                'student_id' => $student->id,
                'tx_ref' => 'SUB-' . $student->id . '-' . time() . '-' . uniqid(),
                'type' => 'subscription',
                'amount' => $request->amount_paid,
                'currency' => 'RWF',
                'status' => 'successful',
                'meta' => json_encode([
                    'method' => $request->payment_method,
                    'subscription_id' => $subscription->id,
                    'subscription_name' => $subscription->name,
                    'tokens_included' => $subscription->tokens_included,
                    'recorded_by' => $staff->name,
                    'payment_reference' => $request->payment_reference,
                ]),
                'paid_at' => $request->payment_date,
            ]);

            // Initialize tokens for student if not already done
            $student->initializeTokens();
            
            // Add tokens from subscription
            $student->token->addTokens(
                $subscription->tokens_included,
                "Tokens from {$subscription->name} subscription",
                'subscription'
            );

            DB::commit();

            Log::info('Subscription payment recorded', [
                'student_id' => $student->id,
                'subscription_id' => $subscription->id,
                'amount' => $request->amount_paid,
                'tokens_added' => $subscription->tokens_included,
                'staff_id' => $staff->id,
            ]);

            return redirect()->route('staff.subscriptions.index')
                ->with('success', "Subscription payment recorded successfully! {$subscription->tokens_included} tokens added to {$student->full_name}'s account.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Subscription payment recording failed', [
                'error' => $e->getMessage(),
                'student_id' => $student->id,
                'subscription_id' => $subscription->id,
            ]);

            return back()->with('error', 'Failed to record subscription payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function staffShow(StudentSubscription $studentSubscription)
    {
        $staff = auth('staff')->user();
        
        // Verify staff has access to this student
        $branchIds = $staff->branches()->pluck('branches.id')->toArray();
        if (!in_array($studentSubscription->student->branch_id, $branchIds)) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        $studentSubscription->load(['student', 'subscription']);
        
        return view('staff.subscriptions.show', compact('studentSubscription'));
    }

    public function staffReports()
    {
        $staff = auth('staff')->user();
        
        $subscriptions = StudentSubscription::with(['student', 'subscription'])
            ->whereHas('student', function ($query) use ($staff) {
                $branchIds = $staff->branches()->pluck('branches.id')->toArray();
                $query->whereIn('branch_id', $branchIds);
            })
            ->where('payment_status', 'paid')
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = $subscriptions->sum('paid_amount');
        $totalTokens = $subscriptions->sum(function ($sub) {
            return $sub->subscription->tokens_included;
        });
        $totalSubscriptions = $subscriptions->count();

        return view('staff.subscriptions.reports', compact(
            'subscriptions',
            'totalRevenue',
            'totalTokens',
            'totalSubscriptions'
        ));
    }
}
