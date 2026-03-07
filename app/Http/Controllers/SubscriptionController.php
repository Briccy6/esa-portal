<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\StudentSubscription;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
