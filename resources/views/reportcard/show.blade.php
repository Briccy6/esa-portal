@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 40px 20px;">
    <!-- Buttons Section -->
    <div style="margin-bottom: 30px; display: flex; gap: 10px; justify-content: center;">
        <a href="{{ route('student.report-cards.download', $reportCard->id) }}"
           style="background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
            <i class="fas fa-download"></i>Download PDF
        </a>
        <button onclick="window.print()"
           style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
            <i class="fas fa-print"></i>Print Report Card
        </button>
        <a href="/student/report-cards"
           style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
            <i class="fas fa-arrow-left"></i>Back to Report Cards
        </a>
    </div>

    <!-- Report Card Content -->
    <div style="background-color: white; padding: 40px; border: 1px solid #ddd; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 40px; border-bottom: 2px solid #333; padding-bottom: 30px;">
            <h2 style="margin: 0 0 10px 0; font-size: 22px; font-weight: bold;">{{ $schoolInfo->school_name ?? 'ESA School' }}</h2>
            <p style="margin: 5px 0; font-size: 12px;">{{ $schoolInfo->address ?? 'School Address' }}</p>
            <p style="margin: 5px 0; font-size: 12px;">{{ $schoolInfo->phone ?? 'Phone Number' }}</p>
            <p style="margin: 10px 0 0 0; font-size: 12px;"><strong>ACADEMIC REPORT CARD</strong></p>
        </div>

        <!-- Student Information -->
        <div style="margin-bottom: 35px;">
            <div style="display: flex; gap: 40px;">
                <!-- Left Column -->
                <div style="flex: 1;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: 1px solid #333; padding: 8px; background-color: #f0f0f0; font-weight: bold; width: 35%;">Student Name</td>
                            <td style="border: 1px solid #333; padding: 8px;">{{ $reportCard->student->full_name }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #333; padding: 8px; background-color: #f0f0f0; font-weight: bold;">Registration No.</td>
                            <td style="border: 1px solid #333; padding: 8px;">{{ $reportCard->student->registration_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #333; padding: 8px; background-color: #f0f0f0; font-weight: bold;">Class/Course</td>
                            <td style="border: 1px solid #333; padding: 8px;">{{ $reportCard->course->course_name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Right Column -->
                <div style="flex: 1;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: 1px solid #333; padding: 8px; background-color: #f0f0f0; font-weight: bold; width: 35%;">Academic Year</td>
                            <td style="border: 1px solid #333; padding: 8px;">{{ $reportCard->academicYear->year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #333; padding: 8px; background-color: #f0f0f0; font-weight: bold;">Term</td>
                            <td style="border: 1px solid #333; padding: 8px;">{{ $reportCard->term->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #333; padding: 8px; background-color: #f0f0f0; font-weight: bold;">Date Issued</td>
                            <td style="border: 1px solid #333; padding: 8px;">{{ $reportCard->updated_at ? $reportCard->updated_at->format('M j, Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Academic Performance -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 14px; font-weight: bold; margin: 0 0 15px 0; padding-bottom: 8px; border-bottom: 1px solid #333;">ACADEMIC PERFORMANCE</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #333; color: white;">
                        <th style="border: 1px solid #333; padding: 10px; text-align: center; font-size: 12px; font-weight: bold;">Subject</th>
                        <th style="border: 1px solid #333; padding: 10px; text-align: center; font-size: 12px; font-weight: bold;">Test Avg</th>
                        <th style="border: 1px solid #333; padding: 10px; text-align: center; font-size: 12px; font-weight: bold;">Exam</th>
                        <th style="border: 1px solid #333; padding: 10px; text-align: center; font-size: 12px; font-weight: bold;">Total</th>
                        <th style="border: 1px solid #333; padding: 10px; text-align: center; font-size: 12px; font-weight: bold;">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @if($reportCard->subjects && $reportCard->subjects->count() > 0)
                        @foreach($reportCard->subjects as $subject)
                            <tr>
                                <td style="border: 1px solid #333; padding: 8px; font-size: 12px; text-align: left;">{{ $subject->subject_name ?? 'N/A' }}</td>
                                <td style="border: 1px solid #333; padding: 8px; font-size: 12px; text-align: center;">{{ $subject->test_average ? number_format(min($subject->test_average, 100), 1) : 'N/A' }}</td>
                                <td style="border: 1px solid #333; padding: 8px; font-size: 12px; text-align: center;">{{ $subject->exam_mark ? number_format(min($subject->exam_mark, 100), 1) : 'N/A' }}</td>
                                <td style="border: 1px solid #333; padding: 8px; font-size: 12px; text-align: center; font-weight: bold;">{{ $subject->total_mark ? number_format(min($subject->total_mark, 100), 1) : 'N/A' }}</td>
                                <td style="border: 1px solid #333; padding: 8px; font-size: 12px; text-align: center;">
                                    <span style="padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 11px; color: white;
                                        @if(strtolower($subject->grade ?? 'n') === 'a') background-color: #28a745;
                                        @elseif(strtolower($subject->grade ?? 'n') === 'b') background-color: #17a2b8;
                                        @elseif(strtolower($subject->grade ?? 'n') === 'c') background-color: #ffc107; color: #000;
                                        @elseif(strtolower($subject->grade ?? 'n') === 'd') background-color: #fd7e14;
                                        @elseif(strtolower($subject->grade ?? 'n') === 'e') background-color: #dc3545;
                                        @else background-color: #6c757d;
                                        @endif
                                    ">{{ $subject->grade ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="border: 1px solid #333; padding: 20px; font-size: 12px; text-align: center; font-style: italic; color: #999;">
                                No subjects found for this report card
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; gap: 20px;">
            <div style="flex: 1; text-align: center; padding: 15px; border: 1px solid #333; background-color: #f9f9f9;">
                <div style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">{{ $reportCard->total_marks ? number_format($reportCard->total_marks, 1) : 'N/A' }}</div>
                <div style="font-size: 11px; text-transform: uppercase; color: #666;">Total Marks</div>
            </div>
            <div style="flex: 1; text-align: center; padding: 15px; border: 1px solid #333; background-color: #f9f9f9;">
                <div style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">{{ $reportCard->average_marks ? number_format($reportCard->average_marks, 1) : 'N/A' }}%</div>
                <div style="font-size: 11px; text-transform: uppercase; color: #666;">Average</div>
            </div>
            <div style="flex: 1; text-align: center; padding: 15px; border: 1px solid #333; background-color: #f9f9f9;">
                <div style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">{{ $reportCard->position_display ?? 'N/A' }}</div>
                <div style="font-size: 11px; text-transform: uppercase; color: #666;">Position</div>
            </div>
        </div>

        <!-- Remarks -->
        @if($reportCard->remarks)
        <div style="margin-bottom: 30px; padding: 15px; background-color: #f9f9f9; border-left: 3px solid #333; font-style: italic; font-size: 12px;">
            <strong>Remarks:</strong> {{ $reportCard->remarks }}
        </div>
        @endif

        <!-- Footer -->
        <div style="margin-top: 60px; padding-top: 30px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: flex-end;">
            <div style="font-size: 11px;">
                <p style="margin: 0 0 5px 0;"><strong>Status:</strong> {{ ucfirst($reportCard->status) }}</p>
                @if($reportCard->approved_by)
                    <p style="margin: 5px 0;"><strong>Approved by:</strong> {{ $reportCard->approvedBy->name ?? 'Administrator' }}</p>
                    <p style="margin: 5px 0;"><strong>Approved on:</strong> {{ $reportCard->approved_at ? $reportCard->approved_at->format('M j, Y g:i A') : 'N/A' }}</p>
                @endif
            </div>
            <div style="text-align: center; min-width: 180px;">
                <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 60px;"></div>
                <div style="font-size: 10px; color: #666;">School Administrator Signature</div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            margin: 0;
            padding: 0;
        }
        div[style*="margin-bottom: 30px; display: flex"] {
            display: none !important;
        }
        div[style*="max-width: 900px"] {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
        div[style*="background-color: white"] {
            box-shadow: none;
            page-break-inside: avoid;
        }
    }
</style>
@endsection
