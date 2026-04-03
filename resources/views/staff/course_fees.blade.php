@extends('staff.partials.sidebar_sec')

@php
    $schoolInfo = \App\Models\SchoolInformation::first();
    $hdrSchoolName = optional($schoolInfo)->school_name ?: 'School Name';
    $hdrAddress = optional($schoolInfo)->address ?: '';
    $hdrPhone = optional($schoolInfo)->phone ?: '';
    $hdrEmail = optional($schoolInfo)->email ?: '';
    
    // Function to convert binary image data to data URL
    $getImageDataUrl = function($binaryData, $mimeType = 'image/jpeg') {
        if (empty($binaryData)) return null;
        return 'data:' . $mimeType . ';base64,' . base64_encode($binaryData);
    };
    
    // Extract image data URLs
    $logoUrl = $getImageDataUrl(optional($schoolInfo)->logo);
    $principalSignatureUrl = $getImageDataUrl(optional($schoolInfo)->principal_signature_data);
    $stampSignatureUrl = $getImageDataUrl(optional($schoolInfo)->stamp_signature_data);
    $principalName = optional($schoolInfo)->principal_name ?: 'School Principal';
@endphp

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
        $logoPath = $systemLogo ? asset('storage/' . $systemLogo) : asset('images/esa.png');
    @endphp
    @forelse($courses as $course)
        @php
            $totalFee = ($course->tuition_fee ?? 0) + ($course->registration_fee ?? 0) + ($course->trip_fee ?? 0) - ($course->discount_fee ?? 0);
            $studentCount = $course->students->count();
            $totalPaid = 0; // Will be loaded via API
            $totalDue = ($studentCount * $totalFee) - $totalPaid;
        @endphp
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <img src="{{ $logoPath }}" class="card-img-top p-4" alt="Course Image" style="height: 180px; object-fit: contain;">
                <div class="card-body">
                    <h5 class="card-title">{{ $course->course_name }}</h5>
                    
                    <!-- Payment Status Section -->
                    <div class="alert alert-light border mb-3" id="paymentStatus{{ $course->id }}">
                        <small class="text-muted">Loading payment statistics...</small>
                    </div>
                    
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Class:
                            <span class="fw-bold">{{ ($course->course_name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Students
                            <span class="badge bg-info">{{ $studentCount }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Fees (Per Student)
                            <span class="fw-bold">{{ number_format($totalFee, 2) }}</span>
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
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#courseDetailsModal{{ $course->id }}">
                            <i class="fas fa-info-circle"></i> Course Details
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="showPaymentSchedule({{ $course->id }})">
                            <i class="fas fa-calendar"></i> Payment Schedule by Term
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="loadCourseTranscripts({{ $course->id }})">
                            <i class="fas fa-file-invoice"></i> View Transcripts
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="printStudentTranscript({{ $course->id }})">
                            <i class="fas fa-print"></i> Print Student Transcript
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="courseDetailsModal{{ $course->id }}" tabindex="-1" aria-labelledby="courseDetailsModalLabel{{ $course->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="courseDetailsModalLabel{{ $course->id }}">Course Details: {{ $course->course_name }}</h5>
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
                                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
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
                <button type="button" class="btn btn-success" onclick="downloadTranscriptsPDF()">
                    <i class="fas fa-download"></i> Download PDF
                </button>
                <button type="button" class="btn btn-primary" onclick="printTranscripts()">
                    <i class="fas fa-print"></i> Print Transcripts
                </button>
            </div>
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
                <button type="button" class="btn btn-success" onclick="downloadCourseTranscriptsPDF()">
                    <i class="fas fa-download"></i> Download PDF
                </button>
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

<!-- Payment Schedule Modal -->
<div class="modal fade" id="paymentScheduleModal" tabindex="-1" aria-labelledby="paymentScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentScheduleModalLabel">
                    <i class="fas fa-calendar"></i> Payment Schedule by Term
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="paymentScheduleContent">
                    <div class="text-center py-4">
                        <span class="spinner-border"></span> Loading payment schedule...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="downloadPaymentSchedulePDF()">
                    <i class="fas fa-download"></i> Download PDF
                </button>
                <button type="button" class="btn btn-primary" onclick="printPaymentSchedule()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    let allTranscripts = [];
    let currentPaymentScheduleContext = {}; // Store current course/term context
    
    // School information for print headers/footers
    const schoolInfo = {
        name: '{{ $hdrSchoolName }}',
        address: '{{ $hdrAddress }}',
        phone: '{{ $hdrPhone }}',
        email: '{{ $hdrEmail }}',
        logoUrl: '{{ $logoUrl }}',
        principalSignatureUrl: '{{ $principalSignatureUrl }}',
        stampSignatureUrl: '{{ $stampSignatureUrl }}',
        principalName: '{{ $principalName }}'
    };

    console.log('Course fees script loaded');

    // Load all transcripts when modal opens
    document.getElementById('transcriptModal').addEventListener('show.bs.modal', function () {
        console.log('Transcript modal opening');
        loadAllTranscripts();
    });

    function loadAllTranscripts() {
        console.log('Loading all transcripts...');
        fetch('/api/transcripts/all')
            .then(response => {
                console.log('Response received:', response);
                return response.json();
            })
            .then(data => {
                console.log('Data loaded:', data);
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
        console.log('Loading course transcripts for course:', courseId);
        try {
            const modal = new bootstrap.Modal(document.getElementById('courseTranscriptsModal'));
            const courseNames = {!! json_encode($courses->pluck('course_name', 'id')->toArray()) !!};
            const courseName = courseNames[courseId];
            document.getElementById('courseTranscriptsModalLabel').innerHTML = 
                '<i class="fas fa-file-invoice"></i> Transcripts: ' + courseName;
            
            modal.show();
            
            fetch('/api/transcripts/course/' + courseId)
                .then(response => {
                    console.log('Course transcripts response:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Course transcripts data:', data);
                    displayCourseTranscripts(data);
                })
                .catch(error => {
                    console.error('Error loading course transcripts:', error);
                    document.getElementById('courseTranscriptContent').innerHTML = 
                        '<div class="alert alert-danger">Error loading transcripts. Please try again.</div>';
                });
        } catch (error) {
            console.error('Error in loadCourseTranscripts:', error);
            alert('Error loading course transcripts. Please check console for details.');
        }
    }

    function displayTranscripts(transcripts) {
        const content = document.getElementById('transcriptContent');
        
        try {
            // Check if transcripts is an error object
            if (transcripts.error) {
                content.innerHTML = '<div class="alert alert-danger">' + transcripts.error + '</div>';
                return;
            }
            
            if (!Array.isArray(transcripts) || transcripts.length === 0) {
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
        } catch (error) {
            console.error('Error in displayTranscripts:', error);
            content.innerHTML = '<div class="alert alert-danger">Error displaying transcripts. Please try again.</div>';
        }
    }

    function displayCourseTranscripts(transcripts) {
        const content = document.getElementById('courseTranscriptContent');
        
        // Check if transcripts is an error object
        if (transcripts.error) {
            content.innerHTML = '<div class="alert alert-danger">' + transcripts.error + '</div>';
            return;
        }
        
        if (!Array.isArray(transcripts) || transcripts.length === 0) {
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
        console.log('Print student transcript for course:', courseId);
        
        // Validate that a course ID is provided
        if (!courseId || courseId === '' || courseId === 0) {
            alert('Please select a course first.');
            return;
        }
        
        try {
            // Show student selection modal
            const modal = new bootstrap.Modal(document.getElementById('studentSelectionModal'));
            document.getElementById('studentSelectionModalLabel').innerHTML = 
                '<i class="fas fa-users"></i> Select Student for Transcript';
            
            modal.show();
            
            // Load students for this course
            fetch('/api/courses/' + courseId + '/students')
                .then(response => {
                    console.log('Students response:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Students data:', data);
                    displayStudentSelection(data, courseId);
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    document.getElementById('studentSelectionContent').innerHTML = 
                        '<div class="alert alert-danger">Error loading students. Please try again.</div>';
                });
        } catch (error) {
            console.error('Error in printStudentTranscript:', error);
            alert('Error opening student selection. Please check console for details.');
        }
    }

    function displayStudentSelection(students, courseId) {
        const content = document.getElementById('studentSelectionContent');
        
        // Handle error responses
        if (!Array.isArray(students) && students && students.error) {
            content.innerHTML = '<div class="alert alert-danger">' + students.error + '</div>';
            return;
        }
        
        // Ensure we have an array
        if (!Array.isArray(students)) {
            students = [];
        }
        
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
            html += '<td>' + (student.full_name || student.name || 'N/A') + '</td>';
            html += '<td>' + (student.reg_number || 'N/A') + '</td>';
            html += '<td>' + (student.class || student.branch_name || 'N/A') + '</td>';
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

    function getTranscriptPrintHTML() {
        const printContent = document.getElementById('transcriptContent').innerHTML;
        const logoImg = schoolInfo.logoUrl ? `<img src="${schoolInfo.logoUrl}" alt="School Logo" style="height: 40px; margin-right: 10px;">` : '';
        const footer = `<footer style="border-top: 2px solid #333; margin-top: 10px; padding-top: 8px; font-size: 8px; text-align: center; color: #333;">
            <div style="margin-bottom: 8px; padding: 0 10px;">
                <strong>${schoolInfo.name}</strong> | ${schoolInfo.address} | Phone: ${schoolInfo.phone} ${schoolInfo.email ? '| Email: ' + schoolInfo.email : ''}
            </div>
            <div style="display: flex; justify-content: space-around; align-items: flex-end;">
                <div style="text-align: center; flex: 1;">
                    ${schoolInfo.principalSignatureUrl ? `<img src="${schoolInfo.principalSignatureUrl}" alt="Principal Signature" style="height: 35px; max-width: 80px; margin: 0 auto;">` : '<div style="height: 35px; margin: 0 auto;"></div>'}
                    <div style="border-top: 1px solid #333; margin-top: 3px; font-size: 7px; padding-top: 2px;">${schoolInfo.principalName}</div>
                </div>
                <div style="text-align: center; flex: 1;">
                    ${schoolInfo.stampSignatureUrl ? `<img src="${schoolInfo.stampSignatureUrl}" alt="School Stamp" style="height: 40px; max-width: 80px; margin: 0 auto;">` : '<div style="height: 40px; margin: 0 auto;"></div>'}
                    <div style="font-size: 7px; padding-top: 2px;">Official Stamp</div>
                </div>
            </div>
        </footer>`;
        return `
            <html>
            <head>
                <title>Payment Transcripts</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    * { margin: 0; padding: 0; }
                    body { padding: 12mm; font-family: Arial, sans-serif; font-size: 10px; line-height: 1.3; }
                    .print-header { border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 8px; text-align: center; display: flex; align-items: center; justify-content: center; }
                    .logo-section { flex-shrink: 0; }
                    .header-text { flex-grow: 1; }
                    .print-header h2 { margin: 0; font-size: 14px; font-weight: bold; }
                    .print-header p { margin: 2px 0; font-size: 9px; }
                    table { width: 100%; border-collapse: collapse; font-size: 9px; }
                    th, td { border: 1px solid #999; padding: 6px; text-align: left; }
                    th { background-color: #2c3e50; color: white; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    footer { border-top: 2px solid #333; margin-top: 10px; padding-top: 8px; font-size: 8px; text-align: center; color: #333; }
                    footer img { max-width: 100%; height: auto; }
                    @media print { body { padding: 8mm; margin: 0; } @page { size: A4; margin: 8mm; } }
                </style>
            </head>
            <body>
                <div class="print-header">
                    ${logoImg ? `<div class="logo-section">${logoImg}</div>` : ''}
                    <div class="header-text">
                        <h2>${schoolInfo.name}</h2>
                        <p>${schoolInfo.address}</p>
                        <p>Phone: ${schoolInfo.phone} ${schoolInfo.email ? '| Email: ' + schoolInfo.email : ''}</p>
                    </div>
                </div>
                <h3 style="margin: 8px 0 4px 0; color: #1a3a52; font-size: 12px;">Student Payment Transcripts</h3>
                <p style="font-size: 9px; color: #666; margin: 2px 0;">Generated on: ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                ${printContent}
                ${footer}
            </body>
            </html>
        `;
    }

    function printTranscripts() {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(getTranscriptPrintHTML());
        printWindow.document.close();
        printWindow.print();
    }

    function downloadTranscriptsPDF() {
        try {
            const element = document.createElement('div');
            element.innerHTML = getTranscriptPrintHTML();
            
            const opt = {
                margin: 10,
                filename: `Payment_Transcripts_${new Date().toISOString().split('T')[0]}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
            };
            
            html2pdf().set(opt).from(element).save();
        } catch (error) {
            console.error('Error downloading PDF:', error);
            alert('Error downloading PDF. Please try again.');
        }
    }

    function getCourseTranscriptsPrintHTML() {
        const printContent = document.getElementById('courseTranscriptContent').innerHTML;
        const logoImg = schoolInfo.logoUrl ? `<img src="${schoolInfo.logoUrl}" alt="School Logo" style="height: 40px; margin-right: 10px;">` : '';
        const footer = `<footer style="border-top: 2px solid #333; margin-top: 10px; padding-top: 8px; font-size: 8px; text-align: center; color: #333;">
            <div style="margin-bottom: 8px; padding: 0 10px;">
                <strong>${schoolInfo.name}</strong> | ${schoolInfo.address} | Phone: ${schoolInfo.phone} ${schoolInfo.email ? '| Email: ' + schoolInfo.email : ''}
            </div>
            <div style="display: flex; justify-content: space-around; align-items: flex-end;">
                <div style="text-align: center; flex: 1;">
                    ${schoolInfo.principalSignatureUrl ? `<img src="${schoolInfo.principalSignatureUrl}" alt="Principal Signature" style="height: 35px; max-width: 80px; margin: 0 auto;">` : '<div style="height: 35px; margin: 0 auto;"></div>'}
                    <div style="border-top: 1px solid #333; margin-top: 3px; font-size: 7px; padding-top: 2px;">${schoolInfo.principalName}</div>
                </div>
                <div style="text-align: center; flex: 1;">
                    ${schoolInfo.stampSignatureUrl ? `<img src="${schoolInfo.stampSignatureUrl}" alt="School Stamp" style="height: 40px; max-width: 80px; margin: 0 auto;">` : '<div style="height: 40px; margin: 0 auto;"></div>'}
                    <div style="font-size: 7px; padding-top: 2px;">Official Stamp</div>
                </div>
            </div>
        </footer>`;
        return `
            <html>
            <head>
                <title>Course Transcripts</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    * { margin: 0; padding: 0; }
                    body { padding: 12mm; font-family: Arial, sans-serif; font-size: 10px; line-height: 1.3; }
                    .print-header { border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 8px; text-align: center; display: flex; align-items: center; justify-content: center; }
                    .logo-section { flex-shrink: 0; }
                    .header-text { flex-grow: 1; }
                    .print-header h2 { margin: 0; font-size: 14px; font-weight: bold; }
                    .print-header p { margin: 2px 0; font-size: 9px; }
                    table { width: 100%; border-collapse: collapse; font-size: 9px; }
                    th, td { border: 1px solid #999; padding: 6px; text-align: left; }
                    th { background-color: #2c3e50; color: white; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    footer { border-top: 2px solid #333; margin-top: 10px; padding-top: 8px; font-size: 8px; text-align: center; color: #333; }
                    footer img { max-width: 100%; height: auto; }
                    @media print { body { padding: 8mm; margin: 0; } @page { size: A4; margin: 8mm; } }
                </style>
            </head>
            <body>
                <div class="print-header">
                    ${logoImg ? `<div class="logo-section">${logoImg}</div>` : ''}
                    <div class="header-text">
                        <h2>${schoolInfo.name}</h2>
                        <p>${schoolInfo.address}</p>
                        <p>Phone: ${schoolInfo.phone} ${schoolInfo.email ? '| Email: ' + schoolInfo.email : ''}</p>
                    </div>
                </div>
                <h3 style="margin: 8px 0 4px 0; color: #1a3a52; font-size: 12px;">Course Payment Transcripts</h3>
                <p style="font-size: 9px; color: #666; margin: 2px 0;">Generated on: ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                ${printContent}
                ${footer}
            </body>
            </html>
        `;
    }

    function printCourseTranscripts() {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(getCourseTranscriptsPrintHTML());
        printWindow.document.close();
        printWindow.print();
    }

    function downloadCourseTranscriptsPDF() {
        try {
            const element = document.createElement('div');
            element.innerHTML = getCourseTranscriptsPrintHTML();
            
            const opt = {
                margin: 10,
                filename: `Course_Transcripts_${new Date().toISOString().split('T')[0]}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
            };
            
            html2pdf().set(opt).from(element).save();
        } catch (error) {
            console.error('Error downloading PDF:', error);
            alert('Error downloading PDF. Please try again.');
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('rw-RW', {
            style: 'currency',
            currency: 'RWF',
            minimumFractionDigits: 2
        }).format(amount);
    }

    // Load payment statistics for a course
    function loadPaymentStatistics(courseId) {
        fetch('/api/transcripts/course/' + courseId + '/payment-stats')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                
                const statusDiv = document.getElementById('paymentStatus' + courseId);
                if (!statusDiv) return;
                
                let html = '<div class="row g-2">';
                html += '<div class="col-6"><small><strong>Paid All:</strong> <span class="badge bg-success">' + data.paid_all + '</span></small></div>';
                html += '<div class="col-6"><small><strong>Paid Half:</strong> <span class="badge bg-warning text-dark">' + data.paid_half + '</span></small></div>';
                html += '<div class="col-6"><small><strong>Unpaid:</strong> <span class="badge bg-danger">' + data.paid_nothing + '</span></small></div>';
                html += '<div class="col-6"><small><strong>Total Amount Due:</strong> <span class="badge bg-info">' + formatCurrency(data.total_due) + '</span></small></div>';
                html += '</div>';
                
                statusDiv.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading payment stats:', error);
            });
    }

    // Show payment schedule modal
    function showPaymentSchedule(courseId) {
        const modal = new bootstrap.Modal(document.getElementById('paymentScheduleModal'));
        const courseNames = {!! json_encode($courses->pluck('course_name', 'id')->toArray()) !!};
        const courseName = courseNames[courseId];
        
        // Store current context for print function
        currentPaymentScheduleContext = {
            courseId: courseId,
            courseName: courseName
        };
        
        document.getElementById('paymentScheduleModalLabel').innerHTML = 
            '<i class="fas fa-calendar"></i> Payment Schedule: ' + courseName + ' by Term';
        
        modal.show();
        
        fetch('/api/transcripts/course/' + courseId + '/term-fees')
            .then(response => response.json())
            .then(data => {
                displayPaymentSchedule(data);
            })
            .catch(error => {
                console.error('Error loading payment schedule:', error);
                document.getElementById('paymentScheduleContent').innerHTML = 
                    '<div class="alert alert-danger">Error loading payment schedule. Please try again.</div>';
            });
    }

    // Display payment schedule
    function displayPaymentSchedule(termFees) {
        const content = document.getElementById('paymentScheduleContent');
        
        if (termFees.error) {
            content.innerHTML = '<div class="alert alert-danger">' + termFees.error + '</div>';
            return;
        }
        
        if (!Array.isArray(termFees) || termFees.length === 0) {
            content.innerHTML = '<div class="text-center py-4 text-muted">No term fees found for this course.</div>';
            return;
        }

        let html = '<div class="table-responsive">';
        html += '<table class="table table-striped table-hover">';
        html += '<thead><tr>';
        html += '<th>Term</th>';
        html += '<th>Academic Year</th>';
        html += '<th>Start Date</th>';
        html += '<th>End Date</th>';
        html += '<th>Tuition Fee</th>';
        html += '<th>Registration Fee</th>';
        html += '<th>Practical Fee</th>';
        html += '<th>Trip Fee</th>';
        html += '<th>Student Card</th>';
        html += '<th>Total</th>';
        html += '</tr></thead>';
        html += '<tbody>';
        
        termFees.forEach(term => {
            html += '<tr>';
            html += '<td><strong>' + term.term_name + '</strong></td>';
            html += '<td>' + term.academic_year + '</td>';
            html += '<td>' + term.start_date + '</td>';
            html += '<td>' + term.end_date + '</td>';
            html += '<td>' + formatCurrency(term.tuition_fee) + '</td>';
            html += '<td>' + formatCurrency(term.registration_fee) + '</td>';
            html += '<td>' + formatCurrency(term.practical_fee) + '</td>';
            html += '<td>' + formatCurrency(term.trip_fee) + '</td>';
            html += '<td>' + formatCurrency(term.student_card_fee) + '</td>';
            html += '<td><strong>' + formatCurrency(term.total_fee) + '</strong></td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        content.innerHTML = html;
    }

    // Get payment schedule print HTML
    function getPaymentSchedulePrintHTML() {
        const printContent = document.getElementById('paymentScheduleContent').innerHTML;
        const courseName = currentPaymentScheduleContext.courseName || 'Payment Schedule';
        
        // Get term information from the table
        const termTable = document.querySelector('#paymentScheduleContent table tbody');
        let termsInfo = '';
        let academicYears = new Set();
        
        if (termTable) {
            const rows = termTable.querySelectorAll('tr');
            const termCount = rows.length;
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells[1]) { // Academic year column
                    academicYears.add(cells[1].textContent.trim());
                }
            });
            termsInfo = `Terms: ${termCount} | Academic Year(s): ${Array.from(academicYears).join(', ')}`;
        }
        
        const logoImg = schoolInfo.logoUrl ? `<img src="${schoolInfo.logoUrl}" alt="School Logo" style="height: 40px; margin-right: 10px;">` : '';
        
        const footer = `<footer style="border-top: 2px solid #333; margin-top: 10px; padding-top: 8px; font-size: 8px; text-align: center; color: #333;">
            <div style="margin-bottom: 8px; padding: 0 10px;">
                <strong>${schoolInfo.name}</strong> | ${schoolInfo.address} | Phone: ${schoolInfo.phone} ${schoolInfo.email ? '| Email: ' + schoolInfo.email : ''}
            </div>
            <div style="display: flex; justify-content: space-around; align-items: flex-end;">
                <div style="text-align: center; flex: 1;">
                    ${schoolInfo.principalSignatureUrl ? `<img src="${schoolInfo.principalSignatureUrl}" alt="Principal Signature" style="height: 35px; max-width: 80px; margin: 0 auto;">` : '<div style="height: 35px; margin: 0 auto;"></div>'}
                    <div style="border-top: 1px solid #333; margin-top: 3px; font-size: 7px; padding-top: 2px;">${schoolInfo.principalName}</div>
                </div>
                <div style="text-align: center; flex: 1;">
                    ${schoolInfo.stampSignatureUrl ? `<img src="${schoolInfo.stampSignatureUrl}" alt="School Stamp" style="height: 40px; max-width: 80px; margin: 0 auto;">` : '<div style="height: 40px; margin: 0 auto;"></div>'}
                    <div style="font-size: 7px; padding-top: 2px;">Official Stamp</div>
                </div>
            </div>
        </footer>`;
        
        return `
            <html>
            <head>
                <title>Payment Schedule - ${courseName}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    * { margin: 0; padding: 0; }
                    body { 
                        padding: 12mm; 
                        font-family: Arial, sans-serif; 
                        font-size: 10px;
                        line-height: 1.3;
                    }
                    .print-header { 
                        border-bottom: 2px solid #333; 
                        padding-bottom: 8px; 
                        margin-bottom: 8px; 
                        text-align: center;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .logo-section {
                        flex-shrink: 0;
                    }
                    .header-text {
                        flex-grow: 1;
                    }
                    .print-header h2 { 
                        margin: 0; 
                        font-size: 14px; 
                        font-weight: bold; 
                    }
                    .print-header p { 
                        margin: 2px 0; 
                        font-size: 9px; 
                    }
                    .course-header {
                        background: #f5f5f5;
                        border: 1px solid #ddd;
                        padding: 6px;
                        margin: 6px 0;
                        border-radius: 3px;
                    }
                    .course-header h3 {
                        margin: 0 0 3px 0;
                        font-size: 12px;
                        font-weight: bold;
                        color: #1a3a52;
                    }
                    .course-info {
                        font-size: 9px;
                        color: #555;
                        margin: 2px 0;
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin: 4px 0;
                        font-size: 9px;
                    }
                    th, td { 
                        border: 1px solid #999; 
                        padding: 4px; 
                        text-align: left; 
                    }
                    th { 
                        background-color: #2c3e50; 
                        color: white;
                        font-weight: bold;
                        font-size: 9px;
                    }
                    tr:nth-child(even) {
                        background-color: #f9f9f9;
                    }
                    footer { 
                        border-top: 2px solid #333; 
                        margin-top: 8px; 
                        padding-top: 5px; 
                        font-size: 8px; 
                        text-align: center; 
                        color: #333;
                    }
                    footer img {
                        max-width: 100%;
                        height: auto;
                    }
                    @media print { 
                        body { padding: 8mm; margin: 0; }
                        html, body { height: 100%; }
                        .print-header { border-bottom: 2px solid #333; }
                        @page { size: A4; margin: 8mm; }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    ${logoImg ? `<div class="logo-section">${logoImg}</div>` : ''}
                    <div class="header-text">
                        <h2>${schoolInfo.name}</h2>
                        <p>${schoolInfo.address}</p>
                        <p>Phone: ${schoolInfo.phone} ${schoolInfo.email ? '| Email: ' + schoolInfo.email : ''}</p>
                    </div>
                </div>
                
                <div class="course-header">
                    <h3>Payment Schedule by Term</h3>
                    <div class="course-info"><strong>Course:</strong> ${courseName}</div>
                    <div class="course-info">${termsInfo}</div>
                    <div class="course-info"><strong>Generated:</strong> ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })} at ${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</div>
                </div>
                
                ${printContent}
                ${footer}
            </body>
            </html>
        `;
    }

    // Print payment schedule
    function printPaymentSchedule() {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(getPaymentSchedulePrintHTML());
        printWindow.document.close();
        printWindow.print();
    }

    function downloadPaymentSchedulePDF() {
        try {
            const element = document.createElement('div');
            element.innerHTML = getPaymentSchedulePrintHTML();
            
            const opt = {
                margin: [8, 8, 8, 8],
                filename: `Payment_Schedule_${currentPaymentScheduleContext.courseName || 'Report'}_${new Date().toISOString().split('T')[0]}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
            };
            
            html2pdf().set(opt).from(element).save();
        } catch (error) {
            console.error('Error downloading PDF:', error);
            alert('Error downloading PDF. Please try again.');
        }
    }

    // Load payment statistics for all courses when page loads
    window.addEventListener('load', function() {
        const courseIds = {!! json_encode($courses->pluck('id')->toArray()) !!};
        courseIds.forEach(courseId => {
            loadPaymentStatistics(courseId);
        });
    });
</script>
@endpush
@endsection