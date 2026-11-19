<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Expense::with(['paid_by_staff', 'term', 'branch']);

        // Filter by staff if not admin
        if (!auth()->guard('admin')->check()) {
            $query->where('paid_by', auth()->guard('staff')->id());
        }

        // Filter by term
        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('expense_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('expense_date', '<=', $request->end_date);
        }

        // Filter by type
        if ($request->filled('expense_type')) {
            $query->where('expense_type', 'like', '%' . $request->expense_type . '%');
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);
        $terms = \App\Models\Term::with('academicYear')->orderBy('start_date', 'desc')->get();
        $branches = \App\Models\Branch::orderBy('name')->get();

        return view('accountant.expenses.index', compact('expenses', 'terms', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $terms = \App\Models\Term::with('academicYear')->orderBy('start_date', 'desc')->get();
        $branches = \App\Models\Branch::orderBy('name')->get();
        return view('accountant.expenses.create', compact('terms', 'branches'));
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
            'term_id' => 'nullable|exists:terms,id',
            'branch_id' => 'nullable|exists:branches,id',
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
            'term_id' => $request->term_id,
            'branch_id' => $request->branch_id,
        ]);

        // Create journal entry for expense
        try {
            \App\Services\AccountingService::recordExpense($expense);
        } catch (\Exception $e) {
            // Log error but don't prevent expense creation
            \Log::error('Failed to create journal entry for expense: ' . $e->getMessage());
        }

        // Send email to admin
        try {
            \Mail::to(config('mail.admin_address', 'excellentservantacademy@gmail.com'))
                ->send(new \App\Mail\ExpenseCreatedAdminMail($expense));
        } catch (\Exception $e) {
            // Log email error but don't prevent expense creation
            \Log::error('Failed to send expense email: ' . $e->getMessage());
        }

        return back()->with('success', 'Expense recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = \App\Models\Expense::with(['paid_by_staff', 'term', 'branch'])->findOrFail($id);

        // Check access
        if (!auth()->guard('admin')->check() && $expense->paid_by !== auth()->guard('staff')->id()) {
            abort(403);
        }

        return view('accountant.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $expense = \App\Models\Expense::findOrFail($id);

        // Check access
        if (!auth()->guard('admin')->check() && $expense->paid_by !== auth()->guard('staff')->id()) {
            abort(403);
        }

        $terms = \App\Models\Term::with('academicYear')->orderBy('start_date', 'desc')->get();
        $branches = \App\Models\Branch::orderBy('name')->get();
        return view('accountant.expenses.edit', compact('expense', 'terms', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expense = \App\Models\Expense::findOrFail($id);
        
        // Check access
        if (!auth()->guard('admin')->check() && $expense->paid_by !== auth()->guard('staff')->id()) {
            abort(403);
        }

        $request->validate([
            'expense_date' => 'required|date',
            'expense_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'paid_to' => 'required|string|max:255',
            'paid_to_contact' => 'required|string|max:50',
            'payment_method' => 'required|string|max:50',
            'term_id' => 'nullable|exists:terms,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $expense->update([
            'expense_date' => $request->expense_date,
            'expense_type' => $request->expense_type,
            'amount' => $request->amount,
            'description' => $request->description,
            'paid_to' => $request->paid_to,
            'paid_to_contact' => $request->paid_to_contact,
            'payment_method' => $request->payment_method,
            'term_id' => $request->term_id,
            'branch_id' => $request->branch_id,
        ]);

        return redirect()->route('staff.accountant.expenses.index')->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = \App\Models\Expense::findOrFail($id);
        
        // Check access
        if (!auth()->guard('admin')->check() && $expense->paid_by !== auth()->guard('staff')->id()) {
            abort(403);
        }

        $expense->delete();

        return redirect()->route('staff.accountant.expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
