@extends('admin.layout')

@section('title', 'Student Fees Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-users me-2"></i>Student Fees Management</h2>
            <p class="text-muted">View and manage student fees by current and upcoming terms</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fas fa-print me-1"></i> Print List
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('accounting.student-fees') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="course_id" class="form-label">Course</label>
                        <select name="course_id" id="course_id" class="form-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select name="branch_id" id="branch_id" class="form-select">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="academic_year_id" class="form-label">Academic Year</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select">
                            <option value="">All Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('accounting.student-fees') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students List -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list me-2"></i>Students ({{ $students->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Student Name</th>
                            <th>Reg Number</th>
                            <th>Course</th>
                            <th>Branch</th>
                            <th>Academic Year</th>
                            <th>Current Term Fees</th>
                            <th>Upcoming Term Fees</th>
                            <th>Total Outstanding</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $currentDate = now();
                                $allTermFees = $student->course->courseTermFees ?? collect([]);
                                
                                $currentFees = $allTermFees->filter(function($termFee) use ($currentDate) {
                                    return $currentDate->between($termFee->term->start_date, $termFee->term->end_date);
                                });
                                
                                $upcomingFees = $allTermFees->filter(function($termFee) use ($currentDate) {
                                    return $currentDate->lt($termFee->term->start_date);
                                });
                                
                                $currentTotal = $currentFees->sum('total_fees');
                                $upcomingTotal = $upcomingFees->sum('total_fees');
                                
                                // Calculate outstanding for current fees
                                $currentPaid = \App\Models\StudentTransaction::where('student_id', $student->id)
                                    ->whereIn('term_id', $currentFees->pluck('term_id'))
                                    ->where('status', 'successful')
                                    ->sum('amount');
                                $currentOutstanding = max(0, $currentTotal - $currentPaid);
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('accounting.student-fee-details', $student->id) }}" class="text-decoration-none">
                                        {{ $student->full_name }}
                                    </a>
                                </td>
                                <td>{{ $student->reg_number }}</td>
                                <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                <td>{{ $student->academicYear->year ?? 'N/A' }}</td>
                                <td>
                                    @if($currentTotal > 0)
                                        <span class="badge bg-info">{{ number_format($currentTotal, 0) }} RWF</span>
                                        @if($currentOutstanding > 0)
                                            <br><small class="text-danger">Outstanding: {{ number_format($currentOutstanding, 0) }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">No current fees</span>
                                    @endif
                                </td>
                                <td>
                                    @if($upcomingTotal > 0)
                                        <span class="badge bg-warning">{{ number_format($upcomingTotal, 0) }} RWF</span>
                                        <br><small class="text-muted">{{ $upcomingFees->count() }} term(s)</small>
                                    @else
                                        <span class="text-muted">No upcoming fees</span>
                                    @endif
                                </td>
                                <td>
                                    @if($currentOutstanding > 0)
                                        <span class="badge bg-danger">{{ number_format($currentOutstanding, 0) }} RWF</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('accounting.student-fee-details', $student->id) }}" class="btn btn-sm btn-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('accounting.payment-transcript', $student->id) }}" class="btn btn-sm btn-info" title="Payment Transcript">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No students found matching the criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($students->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                        </small>
                    </div>
                    <div>
                        {{ $students->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 15px 15px 0 0 !important;
}

.table th {
    border: none;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
}

.btn-sm {
    border-radius: 8px;
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

@media print {
    .btn, .card-header h5 {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
    
    .table-responsive {
        overflow: visible !important;
    }
}
</style>
@endsection
