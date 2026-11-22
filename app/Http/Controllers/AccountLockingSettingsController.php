<?php

namespace App\Http\Controllers;

use App\Models\AccountLockingSetting;
use App\Models\AccountLockStatus;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountLockingSettingsController extends Controller
{
    /**
     * Display account locking settings
     */
    public function index()
    {
        $branches = Branch::all();
        $settings = AccountLockingSetting::with('branch')
            ->orderBy('role')
            ->orderBy('branch_id')
            ->get()
            ->groupBy('role');

        $lockedAccounts = AccountLockStatus::locked()
            ->with('unlockedBy')
            ->orderBy('locked_at', 'desc')
            ->get();

        return view('staff.account_locking_settings', compact(
            'branches',
            'settings',
            'lockedAccounts'
        ));
    }

    /**
     * Store a new setting
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:accountant,student,dos,teacher,client',
            'setting_key' => 'required|string',
            'setting_value' => 'required',
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'nullable|string',
        ]);

        $value = $request->setting_value;
        if (is_numeric($value)) {
            $value = ['value' => (int) $value];
        } elseif (is_string($value)) {
            $value = ['value' => $value];
        }

        AccountLockingSetting::setValue(
            $request->role,
            $request->setting_key,
            $value,
            $request->branch_id,
            $request->description
        );

        return back()->with('success', 'Setting saved successfully.');
    }

    /**
     * Update a setting
     */
    public function update(Request $request, $id)
    {
        $setting = AccountLockingSetting::findOrFail($id);

        $request->validate([
            'setting_value' => 'required',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $value = $request->setting_value;
        if (is_numeric($value)) {
            $value = ['value' => (int) $value];
        } elseif (is_string($value)) {
            $value = ['value' => $value];
        }

        $setting->update([
            'setting_value' => $value,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return back()->with('success', 'Setting updated successfully.');
    }

    /**
     * Delete a setting
     */
    public function destroy($id)
    {
        $setting = AccountLockingSetting::findOrFail($id);
        $setting->delete();

        return back()->with('success', 'Setting deleted successfully.');
    }

    /**
     * Unlock a user account
     */
    public function unlockAccount(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:staff,student,admin,teacher',
            'user_id' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $staff = Auth::guard('staff')->user();

        AccountLockStatus::unlockUser(
            $request->user_type,
            $request->user_id,
            $staff->id,
            $request->notes
        );

        return back()->with('success', 'Account unlocked successfully.');
    }

    /**
     * Unlock a user account by lock status ID
     */
    public function unlockById($id)
    {
        $lockStatus = AccountLockStatus::findOrFail($id);
        $staff = Auth::guard('staff')->user();

        AccountLockStatus::unlockUser(
            $lockStatus->user_type,
            $lockStatus->user_id,
            $staff->id,
            'Manually unlocked by IT staff'
        );

        return back()->with('success', 'Account unlocked successfully.');
    }

    /**
     * Get settings for a role
     */
    public function getRoleSettings($role)
    {
        $settings = AccountLockingSetting::where('role', $role)
            ->active()
            ->with('branch')
            ->get();

        return response()->json(['settings' => $settings]);
    }
}
