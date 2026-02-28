<?php
namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Student;

class PaymentController extends Controller
{
    public function totalPaidByStudents()
{
    $students = Student::with(['schoolFees.payments'])->get();

    $result = $students->map(function ($student) {
        $totalPaid = $student->schoolFees->sum(function ($fee) {
            return $fee->payments->sum('payed');
        });

        return [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'total_paid' => $totalPaid,
        ];
    });

    return response()->json($result);
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_fee_id' => 'required|exists:school_fees,id',
            'payment_type' => 'required|string|max:50',
            'discount' => 'nullable|numeric|min:0',
            'payed' => 'required|numeric|min:0',
        ]);

        $payment = Payment::create($validated);

        return response()->json([
            'message' => 'Payment recorded successfully.',
            'data' => $payment
        ], 201);
    }

    public function index()
    {
        $payments = Payment::with('schoolFee')->latest()->get();

        return response()->json([
            'payments' => $payments
        ]);
    }

    public function show($id)
    {
        $payment = Payment::with('schoolFee')->findOrFail($id);

        return response()->json([
            'payment' => $payment
        ]);
    }
}
