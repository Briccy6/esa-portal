<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Mail\NesaRegistrationStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class NesaRegistrationController extends Controller
{
    public function index()
    {
        $students = Student::with(['branch', 'course', 'academicYear'])
            ->orderBy('full_name')
            ->paginate(20);
            
        return view('admin.nesa.index', compact('students'));
    }

    public function registered()
    {
        $students = Student::with(['branch', 'course', 'academicYear'])
            ->nesaRegistered()
            ->orderBy('nesa_registration_date', 'desc')
            ->paginate(20);
            
        return view('admin.nesa.registered', compact('students'));
    }

    public function pending()
    {
        $students = Student::with(['branch', 'course', 'academicYear'])
            ->notNesaRegistered()
            ->orderBy('full_name')
            ->paginate(20);
            
        return view('admin.nesa.pending', compact('students'));
    }

    public function edit(Student $student)
    {
        $student->load(['branch', 'course', 'academicYear']);
        return view('admin.nesa.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'nesa_sdms_code' => 'required|string|max:50',
            'nesa_reg_number' => 'required|string|max:50',
            'nesa_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'nesa_remarks' => 'nullable|string|max:500',
            'nesa_registered' => 'boolean',
            'send_email' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        $data = $request->except('nesa_document');
        $oldStatus = $student->nesa_registered;
        $newStatus = $request->nesa_registered;

        // Handle file upload
        if ($request->hasFile('nesa_document')) {
            // Delete old document if exists
            if ($student->nesa_document) {
                Storage::disk('public')->delete($student->nesa_document);
            }

            $file = $request->file('nesa_document');
            $filename = 'nesa_document_' . $student->reg_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('nesa_documents', $filename, 'public');
            $data['nesa_document'] = $path;
        }

        // Set registration date if marking as registered
        if ($request->nesa_registered && !$student->nesa_registered) {
            $data['nesa_registration_date'] = now();
        }

        $student->update($data);

        // Send email notification if status changed and email sending is requested
        if ($request->send_email && $oldStatus != $newStatus && $student->email) {
            try {
                $status = $newStatus ? 'approved' : 'rejected';
                $emailContent = $status === 'rejected' ? ($request->nesa_remarks ?? 'Your registration could not be processed at this time.') : null;
                $documentPath = $status === 'approved' && $student->nesa_document ? storage_path('app/public/' . $student->nesa_document) : null;
                
                Mail::to($student->email)->send(new NesaRegistrationStatusMail(
                    $student,
                    $status,
                    $emailContent,
                    $documentPath
                ));
                
                return redirect()
                    ->route('admin.students.index')
                    ->with('success', 'NESA registration updated and email notification sent to ' . $student->full_name);
            } catch (\Exception $e) {
                return redirect()
                    ->route('admin.students.index')
                    ->with('warning', 'NESA registration updated for ' . $student->full_name . ' but email notification failed: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'NESA registration updated successfully for ' . $student->full_name);
    }

    public function markAsRegistered(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'nesa_sdms_code' => 'required|string|max:50',
            'nesa_reg_number' => 'required|string|max:50',
            'nesa_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'nesa_remarks' => 'nullable|string|max:500',
            'send_email' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        $documentPath = null;
        
        // Handle file upload
        if ($request->hasFile('nesa_document')) {
            $file = $request->file('nesa_document');
            $filename = 'nesa_document_' . $student->reg_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $documentPath = $file->storeAs('nesa_documents', $filename, 'public');
        }

        $student->markAsNesaRegistered(
            $request->nesa_sdms_code,
            $request->nesa_reg_number,
            $documentPath,
            $request->nesa_remarks
        );

        // Send email notification if requested
        if ($request->send_email && $student->email) {
            try {
                $fullDocumentPath = $documentPath ? storage_path('app/public/' . $documentPath) : null;
                
                Mail::to($student->email)->send(new NesaRegistrationStatusMail(
                    $student,
                    'approved',
                    null,
                    $fullDocumentPath
                ));
                
                return redirect()
                    ->route('admin.students.index')
                    ->with('success', $student->full_name . ' has been successfully registered for NESA examination and email notification sent.');
            } catch (\Exception $e) {
                return redirect()
                    ->route('admin.students.index')
                    ->with('warning', $student->full_name . ' has been successfully registered for NESA examination but email notification failed: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('admin.students.index')
            ->with('success', $student->full_name . ' has been successfully registered for NESA examination.');
    }

    public function bulkRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'bulk_sdms_code' => 'required|string|max:50',
            'bulk_reg_number_prefix' => 'required|string|max:20',
            'bulk_remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        $students = Student::whereIn('id', $request->student_ids)->get();
        $registeredCount = 0;

        foreach ($students as $index => $student) {
            $regNumber = $request->bulk_reg_number_prefix . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            
            $student->markAsNesaRegistered(
                $request->bulk_sdms_code,
                $regNumber,
                null,
                $request->bulk_remarks
            );
            
            $registeredCount++;
        }

        return redirect()
            ->route('admin.students.index')
            ->with('success', $registeredCount . ' students have been successfully registered for NESA examination.');
    }

    public function downloadDocument(Student $student)
    {
        if (!$student->nesa_document) {
            return back()->with('error', 'No NESA document found for this student.');
        }

        $filePath = storage_path('app/public/' . $student->nesa_document);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'NESA document file not found.');
        }

        return response()->download($filePath, 'NESA_Document_' . $student->reg_number . '.pdf');
    }

    public function exportRegistered()
    {
        $students = Student::with(['branch', 'course', 'academicYear'])
            ->nesaRegistered()
            ->orderBy('nesa_registration_date', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['Reg Number', 'Full Name', 'Email', 'Phone', 'Branch', 'Course', 'Academic Year', 'NESA SDMS Code', 'NESA Reg Number', 'Registration Date', 'Remarks'];

        foreach ($students as $student) {
            $csvData[] = [
                $student->reg_number,
                $student->full_name,
                $student->email,
                $student->phone,
                $student->branch->name ?? 'N/A',
                $student->course->course_name ?? 'N/A',
                $student->academicYear->year ?? 'N/A',
                $student->nesa_sdms_code,
                $student->nesa_reg_number,
                $student->nesa_registration_date->format('Y-m-d'),
                $student->nesa_remarks ?? ''
            ];
        }

        $filename = 'nesa_registered_students_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $filter = $request->get('filter', 'all');

        $students = Student::with(['branch', 'course', 'academicYear'])
            ->where(function($q) use ($query) {
                $q->where('full_name', 'like', "%{$query}%")
                  ->orWhere('reg_number', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            });

        if ($filter === 'registered') {
            $students->nesaRegistered();
        } elseif ($filter === 'pending') {
            $students->notNesaRegistered();
        }

        $students = $students->orderBy('full_name')->paginate(20);

        return view('admin.nesa.search', compact('students', 'query', 'filter'));
    }
}
