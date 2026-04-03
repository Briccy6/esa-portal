<?php

namespace App\Services;

use App\Models\Babyeyi;
use App\Models\Course;
use App\Models\CourseTermFee;
use App\Models\Term;
use App\Models\Branch;
use App\Models\Student;
use App\Models\SchoolInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BabyeyiService
{
    /**
     * Generate babyeyis for all courses in all branches for a specific term.
     * 
     * @param int $termId
     * @param int|null $academicYearId
     * @param int|null $createdBy
     * @return array ['created' => count, 'skipped' => count, 'errors' => []]
     */
    public function generateBabyeyisForTerm($termId, $academicYearId = null, $createdBy = null)
    {
        $term = Term::findOrFail($termId);
        
        if (!$academicYearId) {
            $academicYearId = $term->academic_year_id;
        }

        if (!$createdBy) {
            $createdBy = Auth::id() ?? Auth::guard('admin')->id() ?? Auth::guard('staff')->id();
        }

        $result = [
            'created' => 0,
            'skipped' => 0,
            'errors' => [],
            'babyeyis' => []
        ];

        try {
            DB::beginTransaction();

            // Get all courses for this term that have course term fees
            $coursesWithFees = CourseTermFee::where('term_id', $termId)
                ->where('is_active', true)
                ->distinct('course_id')
                ->pluck('course_id');

            foreach ($coursesWithFees as $courseId) {
                try {
                    $babyeyi = $this->generateBabyeyiForCourse(
                        $courseId,
                        $termId,
                        $academicYearId,
                        $createdBy,
                        true // auto-publish
                    );

                    if ($babyeyi) {
                        $result['created']++;
                        $result['babyeyis'][] = $babyeyi;
                    } else {
                        $result['skipped']++;
                    }
                } catch (\Exception $e) {
                    $result['errors'][] = "Course ID {$courseId}: " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $result['errors'][] = "Transaction failed: " . $e->getMessage();
        }

        return $result;
    }

    /**
     * Generate babyeyi for a specific course.
     * 
     * @param int $courseId
     * @param int $termId
     * @param int $academicYearId
     * @param int $createdBy
     * @param bool $autoPublish
     * @return Babyeyi|null
     */
    public function generateBabyeyiForCourse(
        $courseId,
        $termId,
        $academicYearId,
        $createdBy,
        $autoPublish = false
    ) {
        $course = Course::findOrFail($courseId);
        $term = Term::findOrFail($termId);
        $academicYear = \App\Models\AcademicYear::findOrFail($academicYearId);

        // Check if babyeyi already exists for this course and term
        $existingBabyeyi = Babyeyi::where('course_id', $courseId)
            ->where('term_id', $termId)
            ->where('academic_year_id', $academicYearId)
            ->first();

        if ($existingBabyeyi) {
            return null; // Skip if already exists
        }

        // Get course term fees
        $courseTermFees = CourseTermFee::where('course_id', $courseId)
            ->where('term_id', $termId)
            ->where('is_active', true)
            ->first();

        if (!$courseTermFees) {
            return null; // Skip if no fees configured
        }

        // Generate content with fee table
        $content = $this->generateBabyeyiContent($course, $term, $academicYear, $courseTermFees);

        // Create the babyeyi
        $babyeyi = Babyeyi::create([
            'academic_year_id' => $academicYearId,
            'term_id' => $termId,
            'course_id' => $courseId,
            'course_type' => $course->course_type,
            'created_by' => $createdBy,
            'title' => $this->generateTitle($course, $term),
            'content' => $content,
            'status' => $autoPublish ? Babyeyi::STATUS_PUBLISHED : Babyeyi::STATUS_DRAFT,
            'reference_number' => Babyeyi::generateReferenceNumber(),
        ]);

        return $babyeyi;
    }

    /**
     * Generate babyeyi content with complete information and fee table.
     * 
     * @param Course $course
     * @param Term $term
     * @param mixed $academicYear
     * @param CourseTermFee $courseTermFees
     * @return string HTML content
     */
    private function generateBabyeyiContent($course, $term, $academicYear, $courseTermFees)
    {
        $termStart = $term->start_date->format('F j, Y');
        $termEnd = $term->end_date->format('F j, Y');

        $content = "<div style='font-family: Arial, sans-serif;'>";

        // Header
        $content .= "<h2 style='text-align: center; color: #1a1a1a; margin-bottom: 10px;'>BABYEYI - TERM REGISTRATION DOCUMENT</h2>";
        $content .= "<p style='text-align: center; color: #666; margin: 5px 0; font-size: 14px; border-bottom: 2px solid #333; padding-bottom: 15px;'>Term Registration and Fee Information</p>";

        // Academic Year and Term Info
        $content .= "<div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0; border-radius: 4px;'>";
        $content .= "<p style='margin: 5px 0;'><strong>Academic Year:</strong> {$academicYear->year}</p>";
        $content .= "<p style='margin: 5px 0;'><strong>Term:</strong> {$term->name}</p>";
        $content .= "<p style='margin: 5px 0;'><strong>Course:</strong> {$course->course_name}</p>";
        $content .= "<p style='margin: 5px 0;'><strong>Course Type:</strong> {$course->course_type}</p>";
        $content .= "<p style='margin: 5px 0;'><strong>Term Duration:</strong> {$termStart} to {$termEnd}</p>";
        $content .= "</div>";

        // Fee Structure Table
        $content .= $this->generateFeeTable($courseTermFees);

        // Class Entry Conditions
        $content .= "<h3 style='color: #1a1a1a; margin-top: 30px; margin-bottom: 10px;'>Conditions to Enter Class</h3>";
        $content .= "<ul style='margin-left: 20px; line-height: 1.8;'>";
        $content .= "<li>All fees must be paid before the start of the term (on or before {$termStart})</li>";
        $content .= "<li>Student must be registered in the system with valid student ID</li>";
        $content .= "<li>Student must have submitted all required identification documents</li>";
        $content .= "<li>Student ID card must be collected and signed for</li>";
        $content .= "<li>Late enrollment fees may apply for registrations after the term start date</li>";
        $content .= "</ul>";

        // Payment Information
        $content .= "<h3 style='color: #1a1a1a; margin-top: 30px; margin-bottom: 10px;'>Payment Information</h3>";
        $content .= "<div style='background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; border-radius: 4px;'>";
        $content .= "<p style='margin: 0; font-weight: bold; color: #856404;'>⚠️ Important Payment Notice:</p>";
        $content .= "<ul style='margin-left: 20px; line-height: 1.8; color: #856404;'>";
        $content .= "<li>All fees listed above must be paid in full by the start of the term</li>";
        $content .= "<li>Partial payments will not be accepted for course registration</li>";
        $content .= "<li>Students who have not paid by the term start date will not be allowed to attend classes</li>";
        $content .= "<li>Payment receipts must be presented at the time of attendance</li>";
        $content .= "</ul>";
        $content .= "</div>";

        // Additional Information
        $content .= "<h3 style='color: #1a1a1a; margin-top: 30px; margin-bottom: 10px;'>Additional Information</h3>";
        $content .= "<p style='line-height: 1.6;'>";
        $content .= "This document contains important information regarding your course registration for the {$term->name} of {$academicYear->year}. ";
        $content .= "Please read it carefully and ensure all fees are paid on time. ";
        $content .= "For any clarification regarding fees or payment procedures, please contact the school office.";
        $content .= "</p>";

        $content .= "</div>";

        return $content;
    }

    /**
     * Generate fee table HTML.
     * 
     * @param CourseTermFee $courseTermFees
     * @return string HTML table
     */
    private function generateFeeTable($courseTermFees)
    {
        $table = "<h3 style='color: #1a1a1a; margin: 30px 0 15px 0;'>Fee Structure for This Term</h3>";
        $table .= "<table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>";
        $table .= "<thead>";
        $table .= "<tr style='background-color: #003d82; color: white;'>";
        $table .= "<th style='padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;'>Fee Type</th>";
        $table .= "<th style='padding: 12px; text-align: right; border: 1px solid #ddd; font-weight: bold;'>Amount (RWF)</th>";
        $table .= "<th style='padding: 12px; text-align: center; border: 1px solid #ddd; font-weight: bold;'>Description</th>";
        $table .= "</tr>";
        $table .= "</thead>";
        $table .= "<tbody>";

        $fees = [
            'registration_fee' => ['name' => 'Registration Fee', 'desc' => 'One-time registration fee'],
            'tuition_fee' => ['name' => 'Tuition Fee', 'desc' => 'Term tuition and instruction costs'],
            'practical_fee' => ['name' => 'Practical Fee', 'desc' => 'Laboratory and practical work fees'],
            'trip_fee' => ['name' => 'Trip/Excursion Fee', 'desc' => 'Educational trips and excursions'],
            'student_card_fee' => ['name' => 'Student Card Fee', 'desc' => 'Student ID card issuance'],
        ];

        $total = 0;
        $rowColor = '#ffffff';

        foreach ($fees as $key => $feeInfo) {
            $amount = $courseTermFees->{$key};
            $total += $amount;

            if ($amount > 0) {
                $table .= "<tr style='background-color: {$rowColor}; border: 1px solid #ddd;'>";
                $table .= "<td style='padding: 10px; border: 1px solid #ddd;'><strong>{$feeInfo['name']}</strong></td>";
                $table .= "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>" . number_format($amount, 2) . "</td>";
                $table .= "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; font-size: 12px; color: #666;'>{$feeInfo['desc']}</td>";
                $table .= "</tr>";

                // Alternate row colors
                $rowColor = $rowColor === '#ffffff' ? '#f9f9f9' : '#ffffff';
            }
        }

        // Total Row
        $table .= "<tr style='background-color: #e8e8e8; font-weight: bold; border: 2px solid #003d82;'>";
        $table .= "<td style='padding: 12px; border: 2px solid #003d82; font-weight: bold;'>TOTAL FEES DUE</td>";
        $table .= "<td style='padding: 12px; border: 2px solid #003d82; text-align: right; font-weight: bold; font-size: 16px; color: #d32f2f;'>" . number_format($total, 2) . "</td>";
        $table .= "<td style='padding: 12px; border: 2px solid #003d82; text-align: center; font-weight: bold;'>Complete Payment Required</td>";
        $table .= "</tr>";

        $table .= "</tbody>";
        $table .= "</table>";

        return $table;
    }

    /**
     * Generate title for babyeyi.
     * 
     * @param Course $course
     * @param Term $term
     * @return string
     */
    private function generateTitle($course, $term)
    {
        return "{$course->course_name} - {$term->name} Registration";
    }

    /**
     * Publish all draft babyeyis for a specific term and course.
     * 
     * @param int $termId
     * @param int|null $courseId
     * @return int Number of published babyeyis
     */
    public function publishBabyeyisForTerm($termId, $courseId = null)
    {
        $query = Babyeyi::where('term_id', $termId)
            ->where('status', Babyeyi::STATUS_DRAFT);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->update(['status' => Babyeyi::STATUS_PUBLISHED]);
    }

    /**
     * Regenerate a single babyeyi (for updating content after fee changes).
     * 
     * @param Babyeyi $babyeyi
     * @return Babyeyi
     */
    public function regenerateBabyeyi(Babyeyi $babyeyi)
    {
        $course = $babyeyi->course;
        $term = $babyeyi->term;
        $academicYear = $babyeyi->academicYear;

        $courseTermFees = CourseTermFee::where('course_id', $course->id)
            ->where('term_id', $term->id)
            ->where('is_active', true)
            ->first();

        if ($courseTermFees) {
            $content = $this->generateBabyeyiContent($course, $term, $academicYear, $courseTermFees);
            $babyeyi->update([
                'content' => $content,
                'edited_by' => Auth::id() ?? Auth::guard('admin')->id() ?? Auth::guard('staff')->id(),
            ]);
        }

        return $babyeyi;
    }

    /**
     * Get babyeyi statistics for a term.
     * 
     * @param int $termId
     * @return array
     */
    public function getTermStatistics($termId)
    {
        return [
            'total' => Babyeyi::where('term_id', $termId)->count(),
            'published' => Babyeyi::where('term_id', $termId)->where('status', Babyeyi::STATUS_PUBLISHED)->count(),
            'draft' => Babyeyi::where('term_id', $termId)->where('status', Babyeyi::STATUS_DRAFT)->count(),
            'courses_with_babyeyi' => Babyeyi::where('term_id', $termId)->distinct('course_id')->count(),
            'courses_with_fees' => CourseTermFee::where('term_id', $termId)->where('is_active', true)->distinct('course_id')->count(),
        ];
    }
}
