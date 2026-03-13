<?php
// app/Http/Controllers/TariffController.php
namespace App\Http\Controllers;

use App\Models\Tariff;
use Illuminate\Http\Request;


class TariffController extends Controller
{
    public function index()
    {
        $tariffs = Tariff::latest()->paginate(10);
        return view('admin.tariffs.index', compact('tariffs'));
    }

    public function create()
    {
        return view('admin.tariffs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'activity' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'has_discount' => 'nullable|boolean',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'deadline_type' => 'required|in:date,hours',
            'deadline_date' => 'required_if:deadline_type,date|nullable|date',
            'deadline_hours' => 'required_if:deadline_type,hours|nullable|integer|min:1',
        ]);

        $data = $request->all();
        $data['has_discount'] = $request->has('has_discount');

        Tariff::create($data);

        return redirect()->route('admin.tariffs.index')->with('success', 'Tariff created successfully.');
    }

    public function edit(Tariff $tariff)
    {
        return view('admin.tariffs.edit', compact('tariff'));
    }

    public function update(Request $request, Tariff $tariff)
    {
        $request->validate([
            'activity' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'has_discount' => 'nullable|boolean',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'deadline_type' => 'required|in:date,hours',
            'deadline_date' => 'required_if:deadline_type,date|nullable|date',
            'deadline_hours' => 'required_if:deadline_type,hours|nullable|integer|min:1',
        ]);

        $data = $request->all();
        $data['has_discount'] = $request->has('has_discount');

        $tariff->update($data);

        return redirect()->route('admin.tariffs.index')->with('success', 'Tariff updated successfully.');
    }

    public function destroy(Tariff $tariff)
    {
        $tariff->delete();
        return redirect()->route('admin.tariffs.index')->with('success', 'Tariff deleted successfully.');
    }
}
