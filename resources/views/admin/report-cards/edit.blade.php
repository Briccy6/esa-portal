@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Report Card</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.report-cards.update', $reportCard) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Student Information (Read-only) -->
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
                                        <td><strong>Course:</strong></td>
                                        <td>{{ $reportCard->course->course_name }}</td>
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
                                </table>
                            </div>
                        </div>

                        <!-- Subject Marks Edit -->
                        <h5>Subject Performance</h5>
                        <div class="table-responsive mb-4">
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
                                    @foreach($reportCard->subjects as $index => $subject)
                                        <tr>
                                            <td>{{ $subject->subject_name }}</td>
                                            <td>
                                                <input type="number" name="subjects[{{ $index }}][exam_mark]" 
                                                       value="{{ number_format($subject->exam_mark, 2) }}" 
                                                       class="form-control form-control-sm" 
                                                       step="0.01" min="0" max="100">
                                            </td>
                                            <td>
                                                <input type="number" name="subjects[{{ $index }}][test_average]" 
                                                       value="{{ number_format($subject->test_average, 2) }}" 
                                                       class="form-control form-control-sm" 
                                                       step="0.01" min="0" max="100">
                                            </td>
                                            <td><strong>{{ number_format($subject->total_mark, 2) }}</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $subject->grade == 'A' || $subject->grade == 'A+' ? 'success' : ($subject->grade == 'B' || $subject->grade == 'B+' ? 'info' : ($subject->grade == 'C' ? 'warning' : 'danger')) }}">
                                                    {{ $subject->grade }}
                                                </span>
                                            </td>
                                            <td>
                                                <input type="text" name="subjects[{{ $index }}][remarks]" 
                                                       value="{{ $subject->remarks }}" 
                                                       class="form-control form-control-sm">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Report Card Remarks -->
                        <div class="form-group mb-4">
                            <label for="remarks">Overall Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="4">{{ $reportCard->remarks }}</textarea>
                        </div>

                        <!-- Status Update -->
                        <div class="form-group mb-4">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" {{ $reportCard->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $reportCard->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="printed" {{ $reportCard->status == 'printed' ? 'selected' : '' }}>Printed</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('admin.report-cards.show', $reportCard) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
