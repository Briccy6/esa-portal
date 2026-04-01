@extends('admin.layout')

@section('title', 'Dismissed Student Details')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header-gradient">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title-modern">
                            <i class="bi bi-person-x-fill text-danger me-3"></i>
                            Dismissed Student Details
                        </h1>
                        <p class="page-subtitle-modern">Complete information about the dismissed student</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('admin.dismissed-students.index') }}" class="btn btn-modern btn-secondary-modern me-2">
                            <i class="bi bi-arrow-left me-2"></i>Back to List
                        </a>
                        <form method="POST" action="{{ route('admin.dismissed-students.restore', $dismissedStudent) }}" 
                              onsubmit="return confirmRestore('{{ $dismissedStudent->full_name }}')" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-modern btn-success-modern">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Restore Student
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Information Cards -->
    <div class="row">
        <!-- Personal Information -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="info-card-modern">
                <div class="info-header-modern info-header-personal">
                    <h5 class="info-title-modern">
                        <i class="bi bi-person-fill me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="info-body-modern">
                    <div class="info-item-modern">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $dismissedStudent->full_name }}</div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Gender</div>
                        <div class="info-value">
                            <span class="badge-modern badge-gender">
                                <i class="bi bi-{{ $dismissedStudent->sex == 'Male' ? 'gender-male' : 'gender-female' }} me-1"></i>
                                {{ $dismissedStudent->sex }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">
                            <a href="mailto:{{ $dismissedStudent->email }}" class="info-link">
                                <i class="bi bi-envelope me-1"></i>{{ $dismissedStudent->email }}
                            </a>
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value">
                            <a href="tel:{{ $dismissedStudent->phone }}" class="info-link">
                                <i class="bi bi-phone me-1"></i>{{ $dismissedStudent->phone }}
                            </a>
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">ID Number</div>
                        <div class="info-value">{{ $dismissedStudent->id_number ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">ID Type</div>
                        <div class="info-value">{{ $dismissedStudent->identification_type ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="info-card-modern">
                <div class="info-header-modern info-header-academic">
                    <h5 class="info-title-modern">
                        <i class="bi bi-mortarboard-fill me-2"></i>Academic Information
                    </h5>
                </div>
                <div class="info-body-modern">
                    <div class="info-item-modern">
                        <div class="info-label">Registration Number</div>
                        <div class="info-value">
                            <span class="badge-modern badge-reg-number">
                                <i class="bi bi-hash me-1"></i>{{ $dismissedStudent->reg_number }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Branch</div>
                        <div class="info-value">
                            <span class="badge-modern badge-branch">{{ $dismissedStudent->branch->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Course</div>
                        <div class="info-value">
                            <span class="badge-modern badge-course">{{ $dismissedStudent->course->course_name ?? $dismissedStudent->course->abbreviation ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Academic Year</div>
                        <div class="info-value">
                            <span class="badge-modern badge-year">{{ $dismissedStudent->academicYear->year ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Original Student ID</div>
                        <div class="info-value">{{ $dismissedStudent->original_student_id }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dismissal Information -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="info-card-modern">
                <div class="info-header-modern info-header-dismissal">
                    <h5 class="info-title-modern">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Dismissal Information
                    </h5>
                </div>
                <div class="info-body-modern">
                    <div class="info-item-modern">
                        <div class="info-label">Dismissal Date</div>
                        <div class="info-value">
                            <span class="dismissal-date-badge">
                                <i class="bi bi-calendar-x me-1"></i>
                                {{ $dismissedStudent->formattedDismissalDate }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Dismissed By</div>
                        <div class="info-value">
                            @if($dismissedStudent->dismissedBy)
                                <span class="badge-modern badge-admin">
                                    <i class="bi bi-person-badge me-1"></i>
                                    {{ $dismissedStudent->dismissedBy->first_name ?? 'Admin' }} {{ $dismissedStudent->dismissedBy->last_name ?? '' }}
                                </span>
                            @else
                                <span class="badge-modern badge-system">System</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Dismissal Reason</div>
                        <div class="info-value dismissal-reason">{{ $dismissedStudent->dismissal_reason }}</div>
                    </div>
                    @if($dismissedStudent->dismissal_notes)
                        <div class="info-item-modern">
                            <div class="info-label">Additional Notes</div>
                            <div class="info-value dismissal-notes">{{ $dismissedStudent->dismissal_notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- NESA Registration Information -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="info-card-modern">
                <div class="info-header-modern info-header-nesa">
                    <h5 class="info-title-modern">
                        <i class="bi bi-award-fill me-2"></i>NESA Registration
                    </h5>
                </div>
                <div class="info-body-modern">
                    <div class="info-item-modern">
                        <div class="info-label">Registration Status</div>
                        <div class="info-value">
                            @if($dismissedStudent->nesa_registered)
                                <span class="badge-modern badge-nesa-registered">
                                    <i class="bi bi-check-circle-fill me-1"></i>Registered
                                </span>
                            @else
                                <span class="badge-modern badge-nesa-not-registered">
                                    <i class="bi bi-x-circle-fill me-1"></i>Not Registered
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($dismissedStudent->nesa_registered)
                        <div class="info-item-modern">
                            <div class="info-label">NESA SDMS Code</div>
                            <div class="info-value">{{ $dismissedStudent->nesa_sdms_code ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item-modern">
                            <div class="info-label">NESA Registration Number</div>
                            <div class="info-value">{{ $dismissedStudent->nesa_reg_number ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item-modern">
                            <div class="info-label">Registration Date</div>
                            <div class="info-value">{{ $dismissedStudent->nesa_registration_date ? \Carbon\Carbon::parse($dismissedStudent->nesa_registration_date)->format('d F Y') : 'N/A' }}</div>
                        </div>
                        @if($dismissedStudent->nesa_remarks)
                            <div class="info-item-modern">
                                <div class="info-label">NESA Remarks</div>
                                <div class="info-value">{{ $dismissedStudent->nesa_remarks }}</div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Documents Information -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="info-card-modern">
                <div class="info-header-modern info-header-documents">
                    <h5 class="info-title-modern">
                        <i class="bi bi-file-earmark-fill me-2"></i>Documents
                    </h5>
                </div>
                <div class="info-body-modern">
                    <div class="info-item-modern">
                        <div class="info-label">ID Photo</div>
                        <div class="info-value">
                            @if($dismissedStudent->id_photo)
                                <a href="{{ asset('storage/' . $dismissedStudent->id_photo) }}" target="_blank" class="btn btn-sm btn-modern btn-info-modern">
                                    <i class="bi bi-eye me-1"></i>View ID Photo
                                </a>
                            @else
                                <span class="text-muted">No ID Photo</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Passport Photo</div>
                        <div class="info-value">
                            @if($dismissedStudent->passport_photo)
                                <a href="{{ asset('storage/' . $dismissedStudent->passport_photo) }}" target="_blank" class="btn btn-sm btn-modern btn-info-modern">
                                    <i class="bi bi-eye me-1"></i>View Passport
                                </a>
                            @else
                                <span class="text-muted">No Passport Photo</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Senior 6 Document</div>
                        <div class="info-value">
                            @if($dismissedStudent->senior6_document)
                                <a href="{{ asset('storage/' . $dismissedStudent->senior6_document) }}" target="_blank" class="btn btn-sm btn-modern btn-info-modern">
                                    <i class="bi bi-download me-1"></i>Download S6
                                </a>
                            @else
                                <span class="text-muted">No Senior 6 Document</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Senior 3 Document</div>
                        <div class="info-value">
                            @if($dismissedStudent->senior3_document)
                                <a href="{{ asset('storage/' . $dismissedStudent->senior3_document) }}" target="_blank" class="btn btn-sm btn-modern btn-info-modern">
                                    <i class="bi bi-download me-1"></i>Download S3
                                </a>
                            @else
                                <span class="text-muted">No Senior 3 Document</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item-modern">
                        <div class="info-label">Other Documents</div>
                        <div class="info-value">
                            @if($dismissedStudent->other_supporting_document)
                                <a href="{{ asset('storage/' . $dismissedStudent->other_supporting_document) }}" target="_blank" class="btn btn-sm btn-modern btn-info-modern">
                                    <i class="bi bi-download me-1"></i>Download Other
                                </a>
                            @else
                                <span class="text-muted">No Other Documents</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline/Activity -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="info-card-modern">
                <div class="info-header-modern info-header-timeline">
                    <h5 class="info-title-modern">
                        <i class="bi bi-clock-history me-2"></i>Activity Timeline
                    </h5>
                </div>
                <div class="info-body-modern">
                    <div class="timeline-modern">
                        <div class="timeline-item-modern">
                            <div class="timeline-marker-modern marker-created"></div>
                            <div class="timeline-content-modern">
                                <div class="timeline-title">Student Created</div>
                                <div class="timeline-date">{{ $dismissedStudent->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="timeline-item-modern">
                            <div class="timeline-marker-modern marker-dismissed"></div>
                            <div class="timeline-content-modern">
                                <div class="timeline-title">Student Dismissed</div>
                                <div class="timeline-date">{{ $dismissedStudent->formattedDismissalDate }}</div>
                                <div class="timeline-description">{{ $dismissedStudent->dismissalReasonSummary }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="action-card-modern">
                <div class="action-header-modern">
                    <h5 class="action-title-modern">
                        <i class="bi bi-gear-fill me-2"></i>Actions
                    </h5>
                </div>
                <div class="action-body-modern">
                    <div class="action-buttons-group">
                        <form method="POST" action="{{ route('admin.dismissed-students.restore', $dismissedStudent) }}" 
                              onsubmit="return confirmRestore('{{ $dismissedStudent->full_name }}')" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-modern btn-success-modern btn-lg">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Restore Student to Active Records
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.dismissed-students.index') }}" class="btn btn-modern btn-secondary-modern btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dismissed Students List
                        </a>
                        
                        <button class="btn btn-modern btn-info-modern btn-lg" onclick="printStudentInfo()">
                            <i class="bi bi-printer me-2"></i>Print Student Information
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
/* Modern Page Styles */
.page-header-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.page-title-modern {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle-modern {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.btn-modern {
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn-secondary-modern {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.btn-success-modern {
    background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
    color: white;
}

.btn-info-modern {
    background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%);
    color: white;
}

/* Info Cards */
.info-card-modern {
    border-radius: 15px;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    background: white;
    overflow: hidden;
    transition: all 0.3s ease;
}

.info-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.info-header-modern {
    padding: 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    color: white;
}

.info-header-personal {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.info-header-academic {
    background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%);
}

.info-header-dismissal {
    background: linear-gradient(135deg, #f5365c 0%, #f56036 100%);
}

.info-header-nesa {
    background: linear-gradient(135deg, #fb6340 0%, #fbb140 100%);
}

.info-header-documents {
    background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
}

.info-header-timeline {
    background: linear-gradient(135deg, #8965e0 0%, #5e72e4 100%);
}

.info-title-modern {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
}

.info-body-modern {
    padding: 1.5rem;
}

.info-item-modern {
    margin-bottom: 1.25rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f3f5;
}

.info-item-modern:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.info-value {
    font-size: 1rem;
    color: #2d3748;
    font-weight: 500;
}

.info-link {
    color: #667eea;
    text-decoration: none;
    transition: color 0.3s ease;
}

.info-link:hover {
    color: #764ba2;
    text-decoration: underline;
}

/* Badge Styles */
.badge-modern {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
}

.badge-gender {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.badge-reg-number {
    background: linear-gradient(135deg, #f5365c 0%, #f56036 100%);
    color: white;
}

.badge-branch {
    background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%);
    color: white;
}

.badge-course {
    background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
    color: white;
}

.badge-year {
    background: linear-gradient(135deg, #fb6340 0%, #fbb140 100%);
    color: white;
}

.badge-admin {
    background: linear-gradient(135deg, #8965e0 0%, #5e72e4 100%);
    color: white;
}

.badge-system {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.badge-nesa-registered {
    background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
    color: white;
}

.badge-nesa-not-registered {
    background: linear-gradient(135deg, #f5365c 0%, #f56036 100%);
    color: white;
}

/* Dismissal Specific Styles */
.dismissal-date-badge {
    background: linear-gradient(135deg, #f5365c 0%, #f56036 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    font-weight: 600;
}

.dismissal-reason {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    padding: 1rem;
    border-radius: 10px;
    border-left: 4px solid #f5365c;
    font-weight: 500;
    line-height: 1.5;
}

.dismissal-notes {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1rem;
    border-radius: 10px;
    border-left: 4px solid #6c757d;
    font-style: italic;
    line-height: 1.5;
}

/* Timeline Styles */
.timeline-modern {
    position: relative;
    padding-left: 2rem;
}

.timeline-item-modern {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-marker-modern {
    position: absolute;
    left: -2rem;
    top: 0;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.marker-created {
    background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
}

.marker-dismissed {
    background: linear-gradient(135deg, #f5365c 0%, #f56036 100%);
}

.timeline-content-modern {
    padding-left: 0.5rem;
}

.timeline-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.timeline-date {
    font-size: 0.85rem;
    color: #718096;
    margin-bottom: 0.25rem;
}

.timeline-description {
    font-size: 0.9rem;
    color: #4a5568;
    font-style: italic;
}

/* Action Card */
.action-card-modern {
    border-radius: 15px;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    background: white;
}

.action-header-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.25rem;
    border-bottom: 1px solid #e2e8f0;
}

.action-title-modern {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
}

.action-body-modern {
    padding: 1.5rem;
}

.action-buttons-group {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header-gradient {
        padding: 1.5rem;
    }
    
    .page-title-modern {
        font-size: 1.5rem;
    }
    
    .header-actions {
        margin-top: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .action-buttons-group {
        flex-direction: column;
    }
    
    .btn-modern {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .page-header-gradient .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .info-body-modern {
        padding: 1rem;
    }
    
    .info-item-modern {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }
}
</style>

<!-- JavaScript -->
<script>
function confirmRestore(studentName) {
    return confirm(`Are you sure you want to restore "${studentName}" back to active students?\n\nThis will move the student from dismissed records back to the active student list.`);
}

function printStudentInfo() {
    window.print();
}
</script>

<!-- Print Styles -->
<style media="print">
.page-header-gradient {
    background: none !important;
    color: black !important;
    border-bottom: 2px solid black;
}

.header-actions {
    display: none !important;
}

.action-card-modern {
    display: none !important;
}

.btn-modern {
    display: none !important;
}

.info-card-modern {
    break-inside: avoid;
    page-break-inside: avoid;
}

.info-header-modern {
    background: #f8f9fa !important;
    color: black !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.badge-modern {
    background: #f8f9fa !important;
    color: black !important;
    border: 1px solid #dee2e6 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.dismissal-reason,
.dismissal-notes {
    background: #f8f9fa !important;
    border: 1px solid #dee2e6 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.timeline-marker-modern {
    background: #f8f9fa !important;
    border: 2px solid black !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
</style>
@endsection
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Registration #:</div>
                        <div class="col-sm-8 fw-bold">{{ $dismissedStudent->reg_number }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Email:</div>
                        <div class="col-sm-8">{{ $dismissedStudent->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Phone:</div>
                        <div class="col-sm-8">{{ $dismissedStudent->phone }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Gender:</div>
                        <div class="col-sm-8">{{ $dismissedStudent->sex }}</div>
                    </div>
                    @if($dismissedStudent->id_number)
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">ID Number:</div>
                            <div class="col-sm-8">{{ $dismissedStudent->id_number }} ({{ $dismissedStudent->identification_type }})</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-mortarboard me-2"></i>Academic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Branch:</div>
                        <div class="col-sm-8">
                            <span class="badge bg-info">{{ $dismissedStudent->branch->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Course:</div>
                        <div class="col-sm-8">
                            <span class="badge bg-primary">{{ $dismissedStudent->course->abbreviation ?? $dismissedStudent->course->course_name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Academic Year:</div>
                        <div class="col-sm-8">
                            <span class="badge bg-secondary">{{ $dismissedStudent->academicYear->year ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @if($dismissedStudent->education_level)
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Education Level:</div>
                            <div class="col-sm-8">{{ $dismissedStudent->education_level }}</div>
                        </div>
                    @endif
                    @if($dismissedStudent->discount)
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Discount:</div>
                            <div class="col-sm-8">{{ $dismissedStudent->discount }}%</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dismissal Information -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Dismissal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Dismissal Date:</div>
                                <div class="col-sm-8">
                                    <span class="badge bg-danger">{{ $dismissedStudent->formatted_dismissal_date }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Time Since Dismissal:</div>
                                <div class="col-sm-8">{{ $dismissedStudent->dismissal_date->diffForHumans() }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Dismissed By:</div>
                                <div class="col-sm-8">{{ $dismissedStudent->dismissedBy->name ?? 'System' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-sm-3 text-muted">Reason:</div>
                                <div class="col-sm-9">
                                    <div class="alert alert-warning">
                                        {{ $dismissedStudent->dismissal_reason }}
                                    </div>
                                </div>
                            </div>
                            @if($dismissedStudent->dismissal_notes)
                                <div class="row mb-3">
                                    <div class="col-sm-3 text-muted">Notes:</div>
                                    <div class="col-sm-9">
                                        <div class="alert alert-info">
                                            {{ $dismissedStudent->dismissal_notes }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NESA Information -->
    @if($dismissedStudent->nesa_registered)
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-award me-2"></i>NESA Registration Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Status:</div>
                                    <div class="col-sm-8">
                                        <span class="badge bg-success">Registered</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">SDMS Code:</div>
                                    <div class="col-sm-8">{{ $dismissedStudent->nesa_sdms_code }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Reg Number:</div>
                                    <div class="col-sm-8">{{ $dismissedStudent->nesa_reg_number }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Registration Date:</div>
                                    <div class="col-sm-8">{{ $dismissedStudent->nesa_registration_date ? $dismissedStudent->nesa_registration_date->format('d M Y') : 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @if($dismissedStudent->nesa_remarks)
                                    <div class="row mb-3">
                                        <div class="col-sm-4 text-muted">Remarks:</div>
                                        <div class="col-sm-8">{{ $dismissedStudent->nesa_remarks }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Documents Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark me-2"></i>Student Documents
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <strong>ID Photo:</strong>
                            @if($dismissedStudent->id_photo)
                                <img src="{{ asset('storage/' . $dismissedStudent->id_photo) }}" alt="ID Photo" class="img-thumbnail mt-2" style="max-width: 100px;">
                            @else
                                <span class="text-muted">Not uploaded</span>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Passport Photo:</strong>
                            @if($dismissedStudent->passport_photo)
                                <img src="{{ asset('storage/' . $dismissedStudent->passport_photo) }}" alt="Passport Photo" class="img-thumbnail mt-2" style="max-width: 100px;">
                            @else
                                <span class="text-muted">Not uploaded</span>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Senior 6 Document:</strong>
                            @if($dismissedStudent->senior6_document)
                                <a href="{{ asset('storage/' . $dismissedStudent->senior6_document) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            @else
                                <span class="text-muted">Not uploaded</span>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Senior 3 Document:</strong>
                            @if($dismissedStudent->senior3_document)
                                <a href="{{ asset('storage/' . $dismissedStudent->senior3_document) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            @else
                                <span class="text-muted">Not uploaded</span>
                            @endif
                        </div>
                    </div>
                    @if($dismissedStudent->other_supporting_document)
                        <div class="row">
                            <div class="col-12">
                                <strong>Other Supporting Document:</strong>
                                <a href="{{ asset('storage/' . $dismissedStudent->other_supporting_document) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
