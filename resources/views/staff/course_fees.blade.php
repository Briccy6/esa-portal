@extends('staff.partials.staff_layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Course Fees & Transcript Management</h2>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#transcriptModal">
            <i class="fas fa-file-invoice"></i> View All Transcripts
        </button>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Print Dashboard
        </button>
    </div>
</div>
<div class="row g-4">
    @php
        $systemLogo = \Illuminate\Support\Facades\Cache::get('esa_system_logo');
        $logoPath = $systemLogo ? asset('storage/' . $systemLogo) : asset('images/logo.png');
    @endphp
    @forelse($courses as $course)
        @php
            $totalFee = ($course->tuition_fee ?? 0) + ($course->registration_fee ?? 0) + ($course->trip_fee ?? 0) - ($course->discount_fee ?? 0);
            $studentCount = $course->students->count();
            $totalPaid = 0; // Transcripts table doesn't exist yet
            $totalDue = ($studentCount * $totalFee) - $totalPaid;
        @endphp
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <img src="{{ $logoPath }}" class="card-img-top p-4" alt="Course Image" style="height: 180px; object-fit: contain;">
                <div class="card-body">
                    <h5 class="card-title">{{ $course->name }}</h5>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Class:
                            <span class="fw-bold">{{ ($course->course_name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tuition Fee
                            <span class="fw-bold">{{ number_format($course->tuition_fee, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Registration Fee
                            <span class="fw-bold">{{ number_format($course->registration_fee, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Trip Fee
                            <span class="fw-bold">{{ number_format($course->trip_fee, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Discount Fee
                            <span class="fw-bold">{{ number_format($course->discount_fee, 2) }}</span>
                        </li>
                    </ul>
                    <!-- Details Button -->
                    <button type="button" class="btn btn-outline-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#courseDetailsModal{{ $course->id }}">
                        <i class="fas fa-info-circle"></i> Details
                    </button>
                    <!-- View Transcript Button -->
                    <button type="button" class="btn btn-outline-success w-100 mb-2" onclick="loadCourseTranscripts({{ $course->id }})">
                        <i class="fas fa-file-invoice"></i> View Transcripts
                    </button>
                    <!-- Print Student Transcript Button -->
                    <button type="button" class="btn btn-outline-primary w-100" onclick="printStudentTranscript({{ $course->id }})">
                        <i class="fas fa-print"></i> Print Student Transcript
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="courseDetailsModal{{ $course->id }}" tabindex="-1" aria-labelledby="courseDetailsModalLabel{{ $course->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="courseDetailsModalLabel{{ $course->id }}">Course Details: {{ $course->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Branch Locations:</strong>
                            @if($course->branches && $course->branches->count())
                                {{ $course->branches->pluck('name')->join(', ') }}
                            @else
                                <span class="text-muted">No branches assigned</span>
                            @endif
                        </p>
                        <p><strong>Teachers Assigned:</strong>
                            @if($course->teachers && $course->teachers->count())
                                {{ $course->teachers->pluck('name')->unique()->join(', ') }}
                            @else
                                <span class="text-muted">No teachers assigned</span>
                            @endif
                        </p>
                        <p><strong>Total Students:</strong>
                            {{ $studentCount }}
                        </p>
                        <p><strong>Total Paid Amount:</strong>
                            {{ number_format($totalPaid, 2) }}
                        </p>
                        <p><strong>Rest to be Paid:</strong>
                            {{ number_format($totalDue, 2) }}
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center">No courses found.</div>
        </div>
    @endforelse
</div>

<!-- Transcript Modal -->
<div class="modal fade" id="transcriptModal" tabindex="-1" aria-labelledby="transcriptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transcriptModalLabel">
                    <i class="fas fa-file-invoice"></i> Student Payment Transcripts
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <select class="form-select" id="courseFilter" onchange="filterTranscripts()">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" id="termFilter" onchange="filterTranscripts()">
                            <option value="">All Terms</option>
                            @php
                                $terms = \App\Models\Term::with('academicYear')->orderBy('start_date', 'desc')->get();
                            @endphp
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }} - {{ $term->academicYear->year ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="transcriptContent">
                    <div class="text-center py-4">
                        <span class="spinner-border"></span> Loading transcripts...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printTranscripts()">
                    <i class="fas fa-print"></i> Print Transcripts
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Course Transcripts Modal -->
<div class="modal fade" id="courseTranscriptsModal" tabindex="-1" aria-labelledby="courseTranscriptsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseTranscriptsModalLabel">
                    <i class="fas fa-file-invoice"></i> Course Transcripts
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="courseTranscriptContent">
                    <div class="text-center py-4">
                        <span class="spinner-border"></span> Loading course transcripts...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printCourseTranscripts()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Student Selection Modal -->
<div class="modal fade" id="studentSelectionModal" tabindex="-1" aria-labelledby="studentSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentSelectionModalLabel">
                    <i class="fas fa-users"></i> Select Student for Transcript
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="studentSelectionContent">
                    <div class="text-center py-4">
                        <span class="spinner-border"></span> Loading students...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let allTranscripts = [];

    // Load all transcripts when modal opens
    document.getElementById('transcriptModal').addEventListener('show.bs.modal', function () {
        loadAllTranscripts();
    });

    function loadAllTranscripts() {
        fetch('/api/transcripts/all')
            .then(response => response.json())
            .then(data => {
                allTranscripts = data;
                displayTranscripts(data);
            })
            .catch(error => {
                console.error('Error loading transcripts:', error);
                document.getElementById('transcriptContent').innerHTML = 
                    '<div class="alert alert-danger">Error loading transcripts. Please try again.</div>';
            });
    }

    function loadCourseTranscripts(courseId) {
        const modal = new bootstrap.Modal(document.getElementById('courseTranscriptsModal'));
        const courseName = {{ json_encode($courses->pluck('name', 'id')->toArray()) }}[courseId];
        document.getElementById('courseTranscriptsModalLabel').innerHTML = 
            '<i class="fas fa-file-invoice"></i> Transcripts: ' + courseName;
        
        modal.show();
        
        fetch('/api/transcripts/course/' + courseId)
            .then(response => response.json())
            .then(data => {
                displayCourseTranscripts(data);
            })
            .catch(error => {
                console.error('Error loading course transcripts:', error);
                document.getElementById('courseTranscriptContent').innerHTML = 
                    '<div class="alert alert-danger">Error loading transcripts. Please try again.</div>';
            });
    }

    function displayTranscripts(transcripts) {
        const content = document.getElementById('transcriptContent');
        
        if (transcripts.length === 0) {
            content.innerHTML = '<div class="text-center py-4 text-muted">No transcripts found.</div>';
            return;
        }

        let html = '<div class="table-responsive">';
        html += '<table class="table table-striped table-hover">';
        html += '<thead><tr><th>Student</th><th>Reg Number</th><th>Course</th><th>Term</th><th>Total Fees</th><th>Paid</th><th>Outstanding</th><th>Actions</th></tr></thead>';
        html += '<tbody>';
        
        transcripts.forEach(transcript => {
            const outstanding = transcript.total_fees - transcript.total_paid;
            html += '<tr>';
            html += '<td>' + transcript.student_name + '</td>';
            html += '<td>' + transcript.reg_number + '</td>';
            html += '<td>' + transcript.course_name + '</td>';
            html += '<td><span class="badge bg-info">' + (transcript.term_name || 'N/A') + '</span></td>';
            html += '<td><span class="badge bg-primary">' + formatCurrency(transcript.total_fees) + '</span></td>';
            html += '<td><span class="badge bg-success">' + formatCurrency(transcript.total_paid) + '</span></td>';
            html += '<td><span class="badge bg-warning text-dark">' + formatCurrency(outstanding) + '</span></td>';
            html += '<td>';
            html += '<button class="btn btn-sm btn-outline-primary me-1" onclick="viewStudentTranscript(' + transcript.student_id + ')">';
            html += '<i class="fas fa-eye"></i> View';
            html += '</button>';
            html += '<button class="btn btn-sm btn-outline-success" onclick="printStudentTranscript(' + transcript.student_id + ')">';
            html += '<i class="fas fa-print"></i> Print';
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        content.innerHTML = html;
    }

    function displayCourseTranscripts(transcripts) {
        const content = document.getElementById('courseTranscriptContent');
        
        if (transcripts.length === 0) {
            content.innerHTML = '<div class="text-center py-4 text-muted">No transcripts found for this course.</div>';
            return;
        }

        let html = '<div class="table-responsive">';
        html += '<table class="table table-striped table-hover">';
        html += '<thead><tr><th>Student</th><th>Reg Number</th><th>Term</th><th>Total Fees</th><th>Paid</th><th>Outstanding</th><th>Actions</th></tr></thead>';
        html += '<tbody>';
        
        transcripts.forEach(transcript => {
            const outstanding = transcript.total_fees - transcript.total_paid;
            html += '<tr>';
            html += '<td>' + transcript.student_name + '</td>';
            html += '<td>' + transcript.reg_number + '</td>';
            html += '<td><span class="badge bg-info">' + (transcript.term_name || 'N/A') + '</span></td>';
            html += '<td><span class="badge bg-primary">' + formatCurrency(transcript.total_fees) + '</span></td>';
            html += '<td><span class="badge bg-success">' + formatCurrency(transcript.total_paid) + '</span></td>';
            html += '<td><span class="badge bg-warning text-dark">' + formatCurrency(outstanding) + '</span></td>';
            html += '<td>';
            html += '<button class="btn btn-sm btn-outline-primary me-1" onclick="viewStudentTranscript(' + transcript.student_id + ')">';
            html += '<i class="fas fa-eye"></i> View';
            html += '</button>';
            html += '<button class="btn btn-sm btn-outline-success" onclick="printStudentTranscript(' + transcript.student_id + ')">';
            html += '<i class="fas fa-print"></i> Print';
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        content.innerHTML = html;
    }

    function filterTranscripts() {
        const courseId = document.getElementById('courseFilter').value;
        const termId = document.getElementById('termFilter').value;
        
        let filtered = allTranscripts;
        
        if (courseId) {
            filtered = filtered.filter(t => t.course_id == courseId);
        }
        
        if (termId) {
            filtered = filtered.filter(t => t.term_id == termId);
        }
        
        displayTranscripts(filtered);
    }

    function viewStudentTranscript(studentId) {
        window.open('/students/' + studentId + '/transcript', '_blank');
    }

    function printStudentTranscript(courseId) {
        // Show student selection modal
        const modal = new bootstrap.Modal(document.getElementById('studentSelectionModal'));
        document.getElementById('studentSelectionModalLabel').innerHTML = 
            '<i class="fas fa-users"></i> Select Student for Transcript';
        
        modal.show();
        
        // Load students for this course
        fetch('/api/courses/' + courseId + '/students')
            .then(response => response.json())
            .then(data => {
                displayStudentSelection(data, courseId);
            })
            .catch(error => {
                console.error('Error loading students:', error);
                document.getElementById('studentSelectionContent').innerHTML = 
                    '<div class="alert alert-danger">Error loading students. Please try again.</div>';
            });
    }

    function displayStudentSelection(students, courseId) {
        const content = document.getElementById('studentSelectionContent');
        
        if (students.length === 0) {
            content.innerHTML = '<div class="text-center py-4 text-muted">No students found for this course.</div>';
            return;
        }

        let html = '<div class="table-responsive">';
        html += '<table class="table table-striped table-hover">';
        html += '<thead><tr><th>Student Name</th><th>Reg Number</th><th>Class</th><th>Actions</th></tr></thead>';
        html += '<tbody>';
        
        students.forEach(student => {
            html += '<tr>';
            html += '<td>' + student.full_name + '</td>';
            html += '<td>' + student.reg_number + '</td>';
            html += '<td>' + (student.class || 'N/A') + '</td>';
            html += '<td>';
            html += '<button class="btn btn-sm btn-primary" onclick="generateStudentTranscript(' + student.id + ')">';
            html += '<i class="fas fa-print"></i> Print Transcript';
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        content.innerHTML = html;
    }

    function generateStudentTranscript(studentId) {
        // Open transcript in new window for printing
        const printWindow = window.open('/students/' + studentId + '/transcript?print=1', '_blank');
        
        // Show success message
        alert('Transcript opened in new window for printing. You can close this modal.');
        
        // Close the student selection modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('studentSelectionModal'));
        if (modal) modal.hide();
    }

    function printTranscripts() {
        const printContent = document.getElementById('transcriptContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Payment Transcripts</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    @media print { body { padding: 10px; } }
                </style>
            </head>
            <body>
                <h3>Student Payment Transcripts</h3>
                <p>Generated on: ${new Date().toLocaleString()}</p>
                ${printContent}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

    function printCourseTranscripts() {
        const printContent = document.getElementById('courseTranscriptContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Course Transcripts</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    @media print { body { padding: 10px; } }
                </style>
            </head>
            <body>
                <h3>Course Payment Transcripts</h3>
                <p>Generated on: ${new Date().toLocaleString()}</p>
                ${printContent}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('rw-RW', {
            style: 'currency',
            currency: 'RWF',
            minimumFractionDigits: 2
        }).format(amount);
    }
</script>
@endpush
@endsection