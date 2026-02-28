<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'expense_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'paid_to' => 'required|string|max:255',
            'paid_to_contact' => 'required|string|max:50',
            'payment_method' => 'required|string|max:50',
        ]);

        $expense = \App\Models\Expense::create([
            'expense_date' => $request->expense_date,
            'expense_type' => $request->expense_type,
            'amount' => $request->amount,
            'description' => $request->description,
           'paid_by' => auth()->guard('staff')->id() ?? null,


            'paid_to' => $request->paid_to,
            'paid_to_contact' => $request->paid_to_contact,
            'payment_method' => $request->payment_method,
        ]);

        // Send email to admin
        \Mail::to(config('mail.admin_address', 'excellentservantacademy@gmail.com'))
            ->send(new \App\Mail\ExpenseCreatedAdminMail($expense));

        return back()->with('success', 'Expense recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
