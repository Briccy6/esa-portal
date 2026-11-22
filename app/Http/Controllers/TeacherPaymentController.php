<?php

namespace App\Http\Controllers;

use App\Services\TeacherPaymentService;
use App\Models\TeacherDailyPayment;
use App\Models\TeacherDaily;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(TeacherPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Get pending payments for accountant
     */
    public function index()
    {
        $accountant = Auth::guard('staff')->user();
        $payments = $this->paymentService->getPendingPaymentsForAccountant($accountant);

        return view('accountant.teacher-payments.index', compact('payments'));
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $payment = TeacherDailyPayment::with(['teacher', 'teacherDaily'])->findOrFail($id);
        return view('accountant.teacher-payments.show', compact('payment'));
    }

    /**
     * Record payment
     */
    public function recordPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $payment = TeacherDailyPayment::findOrFail($id);
        $accountant = Auth::guard('staff')->user();

        $this->paymentService->recordPayment(
            $payment,
            $request->amount,
            $accountant,
            $request->notes
        );

        return back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Get teacher's pending payments (for teacher dashboard)
     */
    public function getTeacherPayments()
    {
        $teacher = Auth::guard('teacher')->user();
        $payments = $this->paymentService->getTeacherPendingPayments($teacher);

        return response()->json($payments);
    }

    /**
     * Get unreviewed teacher dailies (for DOS dashboard)
     */
    public function getUnreviewedDailies()
    {
        $dailies = $this->paymentService->getUnreviewedTeacherDailies();

        return response()->json($dailies);
    }
}
