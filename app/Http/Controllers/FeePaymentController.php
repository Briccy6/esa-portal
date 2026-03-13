<?php

namespace App\Http\Controllers;

use App\Models\SchoolFee;
use App\Models\FeeSegment;
use App\Models\Payment;
use App\Models\SchoolFeeSegment;
use App\Models\Student;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
    public function show($id) {
    $student = Student::find($id);
    return view('fees.index', compact('student'));
}

    // Show segments for a school fee with payment status for a student
    public function showSegments($studentId, $schoolFeeId)
    {
        $student = Student::findOrFail($studentId);
        $schoolFee = SchoolFee::findOrFail($schoolFeeId);
        $segments = SchoolFeeSegment::where('school_fee_id', $schoolFeeId)->get();

        // Load payments for the student and these segments
        $payments = Payment::where('student_id', $studentId)
            ->whereIn('fee_segment_id', $segments->pluck('id'))
            ->get()
            ->keyBy('fee_segment_id');

        return view('fees.segments', compact('student', 'schoolFee', 'segments', 'payments'));
    }

    // Show form to record payment
    public function createPayment($studentId, $segmentId)
    {
        $student = Student::findOrFail($studentId);
        $segment = SchoolFeeSegment::findOrFail($segmentId);

        return view('fees.payments.create', compact('student', 'segment'));
    }

    // Store payment
    public function storePayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_segment_id' => 'required|exists:fee_segments,id',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $segment = SchoolFeeSegment::findOrFail($request->fee_segment_id);

        if ($request->amount_paid > $segment->amount) {
            return back()->withErrors(['amount_paid' => 'Amount paid cannot exceed segment amount']);
        }

        // Save payment
        Payment::create([
            'student_id' => $request->student_id,
            'fee_segment_id' => $request->fee_segment_id,
            'amount_paid' => $request->amount_paid,
            'status' => $request->amount_paid == $segment->amount ? 'paid' : 'pending',
        ]);

        return redirect()->route('fees.segments', ['studentId' => $request->student_id, 'schoolFeeId' => $segment->school_fee_id])
            ->with('success', 'Payment recorded successfully.');
    }
}
