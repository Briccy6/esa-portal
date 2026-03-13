<?php
namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExpensesExport implements FromCollection
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    { 
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        return Expense::with(['paid_by_staff', 'paid_by_staff.branches'])
            ->whereBetween('expense_date', [$this->from, $this->to])
            ->get()
            ->map(function ($expense) {
                return [
                    'Date' => $expense->expense_date,
                    'Description' => $expense->description,
                    'Amount' => $expense->amount,
                    'Paid By' => $expense->paid_by_staff->full_name ?? 'N/A',
                    'Paid To' => $expense->paid_to ?? 'N/A', // Use the field directly
                    'Branch' => $expense->paid_by_staff && $expense->paid_by_staff->branches->count()
                        ? $expense->paid_by_staff->branches->first()->name
                        : 'N/A',
                ];
            });
    }
}