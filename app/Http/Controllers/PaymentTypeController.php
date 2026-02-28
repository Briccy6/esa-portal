<?php

namespace App\Http\Controllers;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $payments = PaymentType::all();
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        return view('admin.payments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'applies_to' => 'required|in:student,teacher,staff',
            'is_required' => 'required|boolean',
            'default_amount' => 'required|numeric|min:0',
            'default_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        PaymentType::create($request->all());

        return redirect()->route('admin.payments.index')->with('success', 'Payment type created successfully.');
    }

    public function edit(PaymentType $paymentType)
    {
        return view('admin.payments.edit', compact('paymentType'));
    }

    public function update(Request $request, PaymentType $paymentType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'applies_to' => 'required|in:student,teacher,staff',
            'is_required' => 'required|boolean',
            'default_amount' => 'required|numeric|min:0',
            'default_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $paymentType->update($request->all());

        return redirect()->route('admin.payments.index')->with('success', 'Payment type updated successfully.');
    }

    public function destroy(PaymentType $paymentType)
    {
        $paymentType->delete();
        return redirect()->route('admin.payments.index')->with('success', 'Payment type deleted successfully.');
    }
}