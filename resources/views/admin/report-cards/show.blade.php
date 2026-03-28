@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report Card Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.report-cards.print-view', $reportCard) }}" class="btn btn-success btn-sm" target="_blank">
                            <i class="fas fa-eye"></i> Quick View
                        </a>
                        <a href="{{ route('admin.report-cards.print', $reportCard) }}" class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-print"></i> Print PDF
                        </a>
                        <a href="{{ route('admin.report-cards.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- School Information -->
                    @php $schoolInfo = App\Models\SchoolInformation::getActive(); @endphp
                    @if($schoolInfo)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            @if($schoolInfo->logo_url)
                                                <img src="{{ $schoolInfo->logo_url }}" alt="School Logo" style="width: 60px; height: 60px; object-fit: contain;">
                                            @endif
                                        </div>
                                        <div class="col">
                                            <h5 class="mb-1">{{ $schoolInfo->school_name }}</h5>
                                            <p class="mb-0">
                                                <small>
                                                    {{ $schoolInfo->address }} | 
                                                    Tel: {{ $schoolInfo->phone }} | 
                                                    Email: {{ $schoolInfo->email }}
                                                    @if($schoolInfo->motto) | "{{ $schoolInfo->motto }}" @endif
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Student Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Student Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $reportCard->student->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Registration Number:</strong></td>
                                    <td>{{ $reportCard->student->reg_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sex:</strong></td>
                                    <td>{{ ucfirst($reportCard->student->sex ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $reportCard->course->course_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Passport Photo:</strong></td>
                                    <td>
                                        @if($reportCard->student->passport_photo)
                                            <img src="data:image/jpeg;base64,{{ base64_encode($reportCard->student->passport_photo) }}" alt="Student Photo" class="img-thumbnail" style="max-width: 150px;">
                                        @else
                                            <div class="alert alert-warning text-center">
                                                <i class="fas fa-user-circle fa-3x"></i>
                                                <p class="mb-0 mt-2">No Photo Available</p>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Academic Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Academic Year:</strong></td>
                                    <td>{{ $reportCard->academicYear->year }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Term:</strong></td>
                                    <td>Term {{ $reportCard->term->term }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Class Position:</strong></td>
                                    <td>{{ $reportCard->position_display }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $reportCard->status == 'approved' ? 'success' : ($reportCard->status == 'printed' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($reportCard->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Subject Marks -->
                    <h5>Subject Performance</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Subject</th>
                                    <th>Exam (60%)</th>
                                    <th>Test Avg (40%)</th>
                                    <th>Total</th>
                                    <th>Grade</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportCard->subjects as $subject)
                                    <tr>
                                        <td>{{ $subject->subject_name }}</td>
                                        <td>{{ number_format($subject->exam_mark, 2) }}</td>
                                        <td>{{ number_format($subject->test_average, 2) }}</td>
                                        <td><strong>{{ number_format($subject->total_mark, 2) }}</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $subject->grade == 'A' || $subject->grade == 'A+' ? 'success' : ($subject->grade == 'B' || $subject->grade == 'B+' ? 'info' : ($subject->grade == 'C' ? 'warning' : 'danger')) }}">
                                                {{ $subject->grade }}
                                            </span>
                                        </td>
                                        <td>{{ $subject->remarks ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <td>TOTAL/AVERAGE</td>
                                    <td>{{ number_format($reportCard->subjects->avg('exam_mark'), 2) }}</td>
                                    <td>{{ number_format($reportCard->subjects->avg('test_average'), 2) }}</td>
                                    <td>{{ number_format($reportCard->average_marks, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $reportCard->grade == 'A' || $reportCard->grade == 'A+' ? 'success' : ($reportCard->grade == 'B' || $reportCard->grade == 'B+' ? 'info' : ($reportCard->grade == 'C' ? 'warning' : 'danger')) }}">
                                            {{ $reportCard->grade }}
                                        </span>
                                    </td>
                                    <td>-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Test Details -->
                    @if($reportCard->subjects->isNotEmpty() && $reportCard->subjects->first()->testMarks->isNotEmpty())
                        <h5 class="mt-4">Test Breakdown</h5>
                        @foreach($reportCard->subjects as $subject)
                            @if($subject->testMarks->isNotEmpty())
                                <div class="mb-3">
                                    <h6>{{ $subject->subject_name }} - Test Details</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Test Name</th>
                                                    <th>Score</th>
                                                    <th>Max Mark</th>
                                                    <th>Percentage</th>
                                                    <th>Normalized (100%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subject->testMarks as $testMark)
                                                    <tr>
                                                        <td>{{ $testMark->test_name }}</td>
                                                        <td>{{ $testMark->original_mark }}</td>
                                                        <td>{{ $testMark->max_mark }}</td>
                                                        <td>{{ number_format($testMark->percentage, 2) }}%</td>
                                                        <td>{{ number_format($testMark->normalized_mark, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    <!-- Summary Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Performance Summary</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Total Subjects:</td>
                                            <td><strong>{{ $reportCard->subjects->count() }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Average Mark:</td>
                                            <td><strong>{{ number_format($reportCard->average_marks, 2) }}%</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Total Marks:</td>
                                            <td><strong>{{ number_format($reportCard->total_marks, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Final Grade:</td>
                                            <td>
                                                <strong class="badge badge-{{ $reportCard->grade == 'A' || $reportCard->grade == 'A+' ? 'success' : ($reportCard->grade == 'B' || $reportCard->grade == 'B+' ? 'info' : ($reportCard->grade == 'C' ? 'warning' : 'danger')) }}">
                                                    {{ $reportCard->grade }}
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Class Standing</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Position:</td>
                                            <td><strong>{{ $reportCard->class_position ?? 'N/A' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Total Students:</td>
                                            <td><strong>{{ $reportCard->total_students }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Academic Year:</td>
                                            <td><strong>{{ $reportCard->academicYear->year }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Term:</td>
                                            <td><strong>{{ $reportCard->term->term }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks Section -->
                    <div class="mt-4">
                        <h5>Remarks</h5>
                        <div class="card">
                            <div class="card-body">
                                @if($reportCard->remarks)
                                    <p>{{ $reportCard->remarks }}</p>
                                @else
                                    <p class="text-muted">No remarks added.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4">
                        @if($reportCard->status !== 'approved')
                            <a href="{{ route('admin.report-cards.edit', $reportCard) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Report Card
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('admin.report-cards.update', $reportCard) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Approve this report card?')">
                                <i class="fas fa-check"></i> Approve Report Card
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.report-cards.print', $reportCard) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-print"></i> Print Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
