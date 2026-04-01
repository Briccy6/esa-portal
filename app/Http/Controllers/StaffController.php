<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::all();
        
        // Get all students for NESA registration modal (not paginated)
        $allStudents = Student::with(['branch', 'course'])
            ->orderBy('full_name', 'asc')
            ->get();
            
        return view('admin.staff.index', compact('staffs', 'allStudents'));
    }

    public function create()
    {
        $roles = ['DOS', 'Accountant', 'Secretary'];
        $branches = \App\Models\Branch::all();
        return view('admin.staff.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:DOS,Accountant,Secretary,Teacher',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|unique:staff,phone',
            'branches' => 'required|array|min:1',
            'branches.*' => 'exists:branches,id',
        ]);

        $generatedPassword = Str::random(8);

        // Store photo as base64 string
        $photoBase64 = null;
        if ($request->hasFile('photo')) {
            $photoBase64 = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        }

        $staff = Staff::create([
            'name' => $request->name,
            'role' => $request->role,
            'photo' => $photoBase64,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($generatedPassword),
            'qr_code' => '',
        ]);

        // Assign branches
        $staff->branches()->attach($request->branches);

        // Generate a signed URL that expires in 1 hour
        $loginUrl = URL::temporarySignedRoute(
            'staff.one_time_login',
            now()->addHour(),
            ['staff' => $staff->id]
        );

        // Send welcome email
        Mail::to($staff->email)->send(new \App\Mail\StaffWelcomeMail($staff, $generatedPassword, $loginUrl));

        // Redirect with the generated password (only once)
        // Instead of ->with('password', $generatedPassword), do this:

        return redirect()->route('admin.staff.show', ['staff' => $staff->id, 'temp_password' => $generatedPassword])
            ->with('success', 'Staff member created successfully. See temporary password below.');
    }

    public function edit(Staff $staff)
    {
        $roles = ['DOS', 'Accountant', 'Secretary', 'Teacher'];
        $branches = \App\Models\Branch::all(); // Add this line
        return view('admin.staff.edit', compact('staff', 'roles', 'branches')); // Add 'branches'
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:DOS,Accountant,Secretary,Teacher',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $staff->name = $request->name;
        $staff->role = $request->role;

        if ($request->hasFile('photo')) {
            $staff->photo = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        }

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        // Sync branches
        $staff->branches()->sync($request->input('branches', []));

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        try {
            \DB::beginTransaction();
            
            // Log the deletion attempt
            \Log::info('Attempting to delete staff member', ['staff_id' => $staff->id, 'staff_name' => $staff->name]);
            
            // Comprehensive dependency checking
            $dependencies = $this->checkStaffDependencies($staff);
            
            if (!empty($dependencies)) {
                \DB::rollBack();
                \Log::warning('Cannot delete staff member - has dependencies', [
                    'staff_id' => $staff->id,
                    'dependencies' => $dependencies
                ]);
                
                $dependencyMessage = 'Cannot delete staff member. They have the following dependencies:\n';
                foreach ($dependencies as $table => $count) {
                    $dependencyMessage .= "- {$table}: {$count} record(s)\n";
                }
                $dependencyMessage .= 'Please reassign or delete these records first.';
                
                return redirect()->route('admin.staff.index')
                    ->with('error', $dependencyMessage)
                    ->with('dependency_details', $dependencies);
            }
            
            // Handle the many-to-many branch relationships
            $staff->branches()->detach();
            
            // Delete the staff member
            $deleted = $staff->delete();
            
            if ($deleted) {
                \Log::info('Staff member deleted successfully', ['staff_id' => $staff->id, 'staff_name' => $staff->name]);
                \DB::commit();
                
                return redirect()->route('admin.staff.index')
                    ->with('success', 'Staff member "' . $staff->name . '" deleted successfully.');
            } else {
                throw new \Exception('Failed to delete staff member');
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            \DB::rollBack();
            \Log::error('Database error while deleting staff member', [
                'staff_id' => $staff->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            
            // Check if it's a foreign key constraint error
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('admin.staff.index')
                    ->with('error', 'Cannot delete staff member due to database constraints. They may be referenced by other records. Please check dependencies first.')
                    ->with('technical_error', $e->getMessage());
            }
            
            return redirect()->route('admin.staff.index')
                ->with('error', 'Database error occurred while deleting staff member: ' . $e->getMessage())
                ->with('technical_error', $e->getMessage());
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \DB::rollBack();
            \Log::error('Staff member not found for deletion', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.staff.index')
                ->with('error', 'Staff member not found. They may have already been deleted.')
                ->with('technical_error', $e->getMessage());
                
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('General error while deleting staff member', [
                'staff_id' => $staff->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.staff.index')
                ->with('error', 'Error occurred while deleting staff member: ' . $e->getMessage())
                ->with('technical_error', $e->getMessage());
        }
    }
    
    /**
     * Check for all possible dependencies on a staff member
     */
    private function checkStaffDependencies(Staff $staff): array
    {
        $dependencies = [];
        
        // Check branch assignments
        $branchCount = \DB::table('staff_branch')
            ->where('staff_id', $staff->id)
            ->count();
        if ($branchCount > 0) {
            $dependencies['Branch Assignments'] = $branchCount;
        }
        
        // Check for any records that might reference this staff member
        // as a user_id in various tables
        $tablesToCheck = [
            'journal_entries' => 'created_by',
            'live_class_participants' => 'user_id',
            'live_class_requests' => 'user_id',
            'sessions' => 'user_id',
            'course_user' => 'user_id',
        ];
        
        foreach ($tablesToCheck as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                $count = \DB::table($table)
                    ->where($column, $staff->id)
                    ->count();
                if ($count > 0) {
                    $dependencies[ucfirst(str_replace('_', ' ', $table))] = $count;
                }
            }
        }
        
        // Check live_class_conditions table for staff user_type
        if (Schema::hasTable('live_class_conditions')) {
            $liveClassCount = \DB::table('live_class_conditions')
                ->where('user_type', 'staff')
                ->where(function($query) use ($staff) {
                    $query->where('user_id', $staff->id)
                          ->orWhere('email', $staff->email)
                          ->orWhere('name', $staff->name);
                })
                ->count();
            if ($liveClassCount > 0) {
                $dependencies['Live Class Conditions'] = $liveClassCount;
            }
        }
        
        return $dependencies;
    }

    public function show(Staff $staff, Request $request)
    {
        // Get temp_password from query string, or null if not present
        $temp_password = $request->query('temp_password');

        return view('admin.staff.show', compact('staff', 'temp_password'));
    }

    public function courseFees()
    {
        $courses = \App\Models\Course::with(['branches', 'teachers', 'students'])->get();
        return view('staff.course_fees', compact('courses'));
    }

    // Staff login methods
    public function showLoginForm()
    {
        return view('staff.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();
            
            return redirect()->route('staff.dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        auth()->guard('staff')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('welcome')->with('success', 'You have been logged out successfully.');
    }

    public function dashboard()
    {
        $staff = auth()->guard('staff')->user();
        
        // Get dashboard data based on staff role
        if ($staff->role === 'DOS') {
            return redirect()->route('dos.dashboard');
        } elseif ($staff->role === 'Secretary') {
            return redirect()->route('secretary.dashboard');
        } elseif ($staff->role === 'Accountant') {
            return redirect()->route('accountant.dashboard');
        }
        
        // Default staff dashboard
        return view('staff.dashboard', compact('staff'));
    }

    // One-time login method
    public function oneTimeLogin(Request $request, Staff $staff)
    {
        // Log in the staff (you may need to use the correct guard)
        auth()->login($staff);

        // Optionally, invalidate the link after use (e.g., set a flag in DB or use a short expiry)
        // Redirect to dashboard or force password change
        return redirect()->route('staff.dashboard')->with('success', 'Welcome! Please change your password.');
    }

    public function updateProfile(Request $request)
    {
        $staff = auth()->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|max:2048',
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updatedFields = [];

        if ($request->filled('name') && $request->name !== $staff->name) {
            $staff->name = $request->name;
            $updatedFields[] = 'Name';
        }

        if ($request->filled('email') && $request->email !== $staff->email) {
            $staff->email = $request->email;
            $updatedFields[] = 'Email';
        }

        if ($request->hasFile('photo')) {
            $staff->photo = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
            $updatedFields[] = 'Photo';
        }

        if ($request->filled('password')) {
            if (\Hash::check($request->current_password, $staff->password)) {
                $staff->password = \Hash::make($request->password);
                $updatedFields[] = 'Password';
            } else {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }

        if (count($updatedFields)) {
            $staff->save();
            return back()->with('success', 'Profile updated successfully.')
                         ->with('updated_fields', $updatedFields);
        } else {
            return back()->with('success', 'No changes were made.');
        }
    }

    public function resetPassword(Staff $staff)
    {
        $newPassword = \Illuminate\Support\Str::random(8);
        $staff->password = \Illuminate\Support\Facades\Hash::make($newPassword);
        $staff->save();

        // Send email to staff
        \Illuminate\Support\Facades\Mail::to($staff->email)->send(
            new \App\Mail\StaffPasswordResetMail($staff, $newPassword)
        );

        return redirect()->route('admin.staff.index')->with('success', 'Password reset and sent to staff email.');
    }
}
