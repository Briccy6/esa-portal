<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentRegistrationController extends Controller
{
    public function index()
    {
        // Get students with incomplete registration
        $students = Student::with(['branch', 'course'])
            ->where(function($query) {
                $query->whereNull('id_photo')
                      ->orWhereNull('passport_photo')
                      ->orWhereNull('id_number')
                      ->orWhereNull('identification_type');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('secretary.student-registration.index', compact('students'));
    }

    public function edit(Student $student)
    {
        return view('secretary.student-registration.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'id_number' => 'required|string|max:50',
            'identification_type' => 'required|in:passport,national_id',
            'id_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'senior6_document' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:5120',
            'senior3_document' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:5120',
            'other_supporting_document' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:5120',
        ]);

        // Update text fields
        $student->id_number = $validated['id_number'];
        $student->identification_type = $validated['identification_type'];

        // Handle ID photo upload
        if ($request->hasFile('id_photo')) {
            // Delete old photo if exists
            if ($student->id_photo) {
                Storage::disk('public')->delete($student->id_photo);
            }
            
            $idPhoto = $request->file('id_photo');
            $idPhotoPath = $idPhoto->store('students/id-photos', 'public');
            $student->id_photo = $idPhotoPath;
        }

        // Handle passport photo upload
        if ($request->hasFile('passport_photo')) {
            // Delete old photo if exists
            if ($student->passport_photo) {
                Storage::disk('public')->delete($student->passport_photo);
            }
            
            $passportPhoto = $request->file('passport_photo');
            $passportPhotoPath = $passportPhoto->store('students/passport-photos', 'public');
            $student->passport_photo = $passportPhotoPath;
        }

        // Handle supporting documents
        if ($request->hasFile('senior6_document')) {
            if ($student->senior6_document) {
                Storage::disk('public')->delete($student->senior6_document);
            }
            
            $doc = $request->file('senior6_document');
            $docPath = $doc->store('students/senior6-documents', 'public');
            $student->senior6_document = $docPath;
        }

        if ($request->hasFile('senior3_document')) {
            if ($student->senior3_document) {
                Storage::disk('public')->delete($student->senior3_document);
            }
            
            $doc = $request->file('senior3_document');
            $docPath = $doc->store('students/senior3-documents', 'public');
            $student->senior3_document = $docPath;
        }

        if ($request->hasFile('other_supporting_document')) {
            if ($student->other_supporting_document) {
                Storage::disk('public')->delete($student->other_supporting_document);
            }
            
            $doc = $request->file('other_supporting_document');
            $docPath = $doc->store('students/supporting-documents', 'public');
            $student->other_supporting_document = $docPath;
        }

        $student->save();

        return redirect()
            ->route('secretary.student-registration.index')
            ->with('success', 'Student registration completed successfully!');
    }

    public function show(Student $student)
    {
        return view('secretary.student-registration.show', compact('student'));
    }

    public function completeRegistration(Student $student)
    {
        // Check if all required fields are present
        $requiredFields = ['id_number', 'identification_type', 'id_photo', 'passport_photo'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (is_null($student->$field)) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return redirect()
                ->route('secretary.student-registration.edit', $student)
                ->with('error', 'Please complete all required fields: ' . implode(', ', $missingFields));
        }

        // Mark registration as complete (you might want to add a flag in the students table)
        // For now, we'll just redirect with success message
        return redirect()
            ->route('secretary.student-registration.show', $student)
            ->with('success', 'Student registration completed successfully!');
    }
}
