@extends('students.app')

@section('title', 'Report Card - ' . $reportCard->student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title">
                        <i class="bi bi-file-earmark-text me-2"></i>Report Card
                    </h1>
                    <p class="page-subtitle">
                        {{ $reportCard->student->full_name }} - 
                        {{ $reportCard->academicYear->year ?? 'N/A' }} - 
                        {{ $reportCard->term->name ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('student.report-cards.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Report Cards
                    </a>
                    <a href="{{ route('student.report-cards.download', $reportCard) }}" class="btn btn-success ms-2">
                        <i class="bi bi-download me-2"></i>Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-primary ms-2">
                        <i class="bi bi-printer me-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="report-card-container">
                <!-- School Header -->
                <div class="report-card-header text-center mb-4">
                    <h2>{{ $schoolInfo->school_name ?? 'ESA School' }}</h2>
                    <p class="text-muted">{{ $schoolInfo->address ?? 'School Address' }}</p>
                    <p class="text-muted">{{ $schoolInfo->phone ?? 'Phone Number' }}</p>
                </div>

                <!-- Student Information -->
                <div class="student-info-section mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Student Name:</strong></td>
                                    <td>{{ $reportCard->student->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Registration Number:</strong></td>
                                    <td>{{ $reportCard->student->registration_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Class/Course:</strong></td>
                                    <td>{{ $reportCard->course->course_name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Academic Year:</strong></td>
                                    <td>{{ $reportCard->academicYear->year ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Term:</strong></td>
                                    <td>{{ $reportCard->term->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date Issued:</strong></td>
                                    <td>{{ $reportCard->updated_at->format('M j, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Subjects and Marks -->
                <div class="marks-section mb-4">
                    <h4 class="section-title">Academic Performance</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered marks-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Subject</th>
                                    <th>Test Average</th>
                                    <th>Exam Mark</th>
                                    <th>Total Mark</th>
                                    <th>Grade</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($reportCard->subjects && $reportCard->subjects->count() > 0)
                                    @foreach($reportCard->subjects as $subject)
                                        <tr>
                                            <td>{{ $subject->subject_name ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                {{ $subject->test_average ? number_format($subject->test_average, 1) : 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $subject->exam_mark ? number_format($subject->exam_mark, 1) : 'N/A' }}
                                            </td>
                                            <td class="text-center fw-bold">
                                                {{ $subject->total_mark ? number_format($subject->total_mark, 1) : 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                <span class="grade-badge grade-{{ strtolower($subject->grade ?? 'n') }}">
                                                    {{ $subject->grade ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                {{ ($subject->grade === 'A' || $subject->grade === 'B') ? 'C' : 'NYC' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No subjects found for this report card
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="summary-section mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="summary-card">
                                <div class="summary-value">
                                    {{ $reportCard->total_marks ? number_format($reportCard->total_marks, 1) : 'N/A' }}
                                </div>
                                <div class="summary-label">Total Marks</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card">
                                <div class="summary-value">
                                    {{ $reportCard->average_marks ? number_format($reportCard->average_marks, 1) : 'N/A' }}%
                                </div>
                                <div class="summary-label">Average Marks</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card">
                                <div class="summary-value">
                                    {{ $reportCard->position_display ?? 'N/A' }}
                                </div>
                                <div class="summary-label">Class Position</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                @if($reportCard->remarks)
                <div class="remarks-section mb-4">
                    <h4 class="section-title">Remarks</h4>
                    <div class="remarks-content">
                        {{ $reportCard->remarks }}
                    </div>
                </div>
                @endif

                <!-- Approval Information -->
                <div class="approval-section">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $reportCard->status === 'approved' ? 'success' : ($reportCard->status === 'printed' ? 'info' : 'warning') }}">
                                    {{ ucfirst($reportCard->status) }}
                                </span>
                            </p>
                            @if($reportCard->approved_by)
                                <p class="mb-1">
                                    <strong>Approved by:</strong> {{ $reportCard->approvedBy->name ?? 'Administrator' }}
                                </p>
                                <p class="mb-1">
                                    <strong>Approved on:</strong> {{ $reportCard->approved_at->format('M j, Y g:i A') }}
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="signature-area">
                                <p class="mb-1">_________________________</p>
                                <p class="text-muted">School Administrator Signature</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.report-card-container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.report-card-header h2 {
    color: #333;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.student-info-section .table {
    margin-bottom: 0;
}

.student-info-section .table td {
    padding: 0.75rem;
    border-color: #dee2e6;
}

.student-info-section .table td:first-child {
    width: 40%;
    background-color: #f8f9fa;
    font-weight: 500;
}

.section-title {
    color: #333;
    font-weight: bold;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #667eea;
}

.marks-table th {
    background-color: #343a40;
    border-color: #343a40;
    color: white;
    font-weight: 500;
}

.marks-table td {
    vertical-align: middle;
    border-color: #dee2e6;
}

.grade-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-weight: bold;
    font-size: 0.875rem;
}

.grade-a { background-color: #28a745; color: white; }
.grade-b { background-color: #17a2b8; color: white; }
.grade-c { background-color: #ffc107; color: #212529; }
.grade-d { background-color: #fd7e14; color: white; }
.grade-e { background-color: #dc3545; color: white; }
.grade-n { background-color: #6c757d; color: white; }

.summary-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.summary-value {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.summary-label {
    font-size: 0.875rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.remarks-content {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
    font-style: italic;
}

.approval-section {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #dee2e6;
}

.signature-area p {
    margin-bottom: 0.25rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 0;
}

@media print {
    .report-card-container {
        box-shadow: none;
        border: 1px solid #333;
    }
    
    .btn, .d-flex.justify-content-between {
        display: none !important;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
    
    .container-fluid {
        margin: 0;
        padding: 0;
    }
    
    .row, .col-12 {
        margin: 0;
        padding: 0;
    }
    
    .report-card-container {
        border-radius: 0;
        padding: 1rem;
        margin: 0;
    }
    
    .summary-card {
        background: #f8f9fa;
        color: #333;
        border: 1px solid #333;
    }
    
    .summary-value {
        color: #333;
    }
    
    page-break-inside: avoid;
}
</style>
@endsection
