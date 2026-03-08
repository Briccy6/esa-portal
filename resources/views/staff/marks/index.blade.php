@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Marks Management & Reports
                    </h2>
                    <p class="text-muted mb-0">View student marks, generate reports, and monitor academic performance</p>
                </div>
                <div class="d-flex gap-3">
                    <div class="badge bg-light text-dark fs-6">
                        <i class="fas fa-clipboard-check me-1 text-success"></i>
                        {{ $stats['total_marks'] }} Total Records
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Quick Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-list-alt fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['total_marks'] }}</h5>
                            <small>Total Marks</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['exam_marks'] }}</h5>
                            <small>Exams</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-pencil-alt fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['test_marks'] }}</h5>
                            <small>Tests</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-bar fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ number_format($stats['average_score'], 1) }}</h5>
                            <small>Average Score</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-up fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['highest_score'] }}</h5>
                            <small>Highest</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-danger text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-down fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['lowest_score'] }}</h5>
                            <small>Lowest</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('dos.marks.index') }}">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <select name="course_id" class="form-select" id="courseFilter">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="branch_id" class="form-select" id="branchFilter">
                                    <option value="">All Branches</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->branch_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="assessment_type" class="form-select" id="assessmentFilter">
                                    <option value="">All Types</option>
                                    <option value="exam" {{ request('assessment_type') == 'exam' ? 'selected' : '' }}>Exams</option>
                                    <option value="test" {{ request('assessment_type') == 'test' ? 'selected' : '' }}>Tests</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="exam_id" class="form-select" id="examFilter">
                                    <option value="">All Exams</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->exam_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" 
                                       name="student_search" 
                                       class="form-control" 
                                       placeholder="Search student..."
                                       value="{{ request('student_search') }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter me-1"></i> Apply Filters
                                </button>
                                <a href="{{ route('dos.marks.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-dark fw-semibold mb-3">
                                <i class="fas fa-tools me-2"></i>Reports & Tools
                            </h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentReportModal">
                                    <i class="fas fa-user-graduate me-1"></i> Student Report
                                </button>
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#classReportModal">
                                    <i class="fas fa-users me-1"></i> Class Report
                                </button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#reportCardModal">
                                    <i class="fas fa-id-card me-1"></i> Report Card
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-table me-2"></i>
                        Marks Records
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="marksTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-user text-primary me-2"></i>Student
                                    </th>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-book text-info me-2"></i>Course
                                    </th>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-code-branch text-success me-2"></i>Branch
                                    </th>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-clipboard text-warning me-2"></i>Type
                                    </th>
                                    <th class="border-0 fw-semibold py-3 text-center">
                                        <i class="fas fa-star text-warning me-2"></i>Score
                                    </th>
                                    <th class="border-0 fw-semibold py-3">
                                        <i class="fas fa-medal text-secondary me-2"></i>Grade
                                    </th>
                                    <th class="border-0 fw-semibold py-3 text-center">
                                        <i class="fas fa-cogs text-dark me-2"></i>Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @foreach($marks as $mark)
                                    <tr class="mark-row">
                                        <!-- Student Column -->
                                        <td class="align-middle py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-semibold student-name">{{ $mark->student->name ?? '-' }}</span>
                                                    <br><small class="text-muted">ID: {{ $mark->student_id }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Course Column -->
                                        <td class="align-middle py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                                    <i class="fas fa-graduation-cap text-info"></i>
                                                </div>
                                                <span class="course-name">{{ $mark->course->course_name ?? '-' }}</span>
                                            </div>
                                        </td>

                                        <!-- Branch Column -->
                                        <td class="align-middle py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                                    <i class="fas fa-map-marker-alt text-success"></i>
                                                </div>
                                                <span class="branch-name">{{ $mark->branch->branch_name ?? '-' }}</span>
                                            </div>
                                        </td>

                                        <!-- Type Column -->
                                        <td class="align-middle py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                                    <i class="fas fa-tag text-warning"></i>
                                                </div>
                                                <div>
                                                    <span class="badge bg-{{ $mark->assessment_type == 'exam' ? 'primary' : 'secondary' }} text-white px-2 py-1">
                                                        {{ ucfirst($mark->assessment_type) }}
                                                    </span>
                                                    @if($mark->exam)
                                                        <br><small class="text-muted">{{ $mark->exam->exam_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Score Column -->
                                        <td class="align-middle py-3 text-center">
                                            <div class="score-container">
                                                <span class="fw-bold score-value {{ $mark->value >= ($mark->marks_out_of * 0.5) ? 'text-success' : 'text-danger' }}">
                                                    {{ $mark->value ?? '-' }}
                                                </span>
                                                @if($mark->marks_out_of)
                                                    <br><small class="text-muted">/ {{ $mark->marks_out_of }}</small>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Grade Column -->
                                        <td class="align-middle py-3">
                                            <div class="grade-container">
                                                @if($mark->grade)
                                                    <span class="badge bg-light text-dark px-2 py-1">
                                                        {{ $mark->grade->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Action Column -->
                                        <td class="align-middle py-3 text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dos.marks.show', $mark->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        onclick="generateStudentReport({{ $mark->student_id }})"
                                                        title="Student Report">
                                                    <i class="fas fa-user-graduate"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- No Results Message -->
                    @if($marks->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-search fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No marks found</h5>
                            <p class="text-muted mb-0">Try adjusting your search terms or filters</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pagination -->
            @if($marks->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $marks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Student Report Modal -->
<div class="modal fade" id="studentReportModal" tabindex="-1" aria-labelledby="studentReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="studentReportModalLabel">
                    <i class="fas fa-user-graduate me-2"></i>Generate Student Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('dos.marks.student-report') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Student *</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Select Student</option>
                                @foreach(\App\Models\Student::orderBy('name')->get() as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} (ID: {{ $student->id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course (Optional)</label>
                            <select name="course_id" class="form-select">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Term (Optional)</label>
                            <input type="text" name="term" class="form-control" placeholder="e.g., Term 1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-alt me-1"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Class Report Modal -->
<div class="modal fade" id="classReportModal" tabindex="-1" aria-labelledby="classReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="classReportModalLabel">
                    <i class="fas fa-users me-2"></i>Generate Class Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('dos.marks.class-report') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Course *</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch *</label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Exam (Optional)</label>
                            <select name="exam_id" class="form-select">
                                <option value="">All Exams</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-chart-bar me-1"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Report Card Modal -->
<div class="modal fade" id="reportCardModal" tabindex="-1" aria-labelledby="reportCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="reportCardModalLabel">
                    <i class="fas fa-id-card me-2"></i>Generate Report Card
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('dos.marks.report-card') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Student *</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Select Student</option>
                                @foreach(\App\Models\Student::orderBy('name')->get() as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} (ID: {{ $student->id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Term (Optional)</label>
                            <input type="text" name="term" class="form-control" placeholder="e.g., Term 1">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Academic Year (Optional)</label>
                            <input type="text" name="academic_year" class="form-control" placeholder="e.g., 2025-2026">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-alt me-1"></i> Generate Report Card
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #117a8b);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
    }
    
    .mark-row {
        transition: all 0.2s ease;
    }
    
    .mark-row:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .score-value {
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .badge {
            font-size: 0.75rem !important;
            padding: 0.25rem 0.5rem !important;
        }
    }
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-close modals after form submission
    const forms = document.querySelectorAll('#studentReportModal form, #classReportModal form, #reportCardModal form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const modal = this.closest('.modal');
            const modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
        });
    });
});

function generateStudentReport(studentId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("dos.marks.student-report") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const studentInput = document.createElement('input');
    studentInput.type = 'hidden';
    studentInput.name = 'student_id';
    studentInput.value = studentId;
    
    form.appendChild(csrfToken);
    form.appendChild(studentInput);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
