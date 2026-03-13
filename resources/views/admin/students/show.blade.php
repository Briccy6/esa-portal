@extends('admin.layout')

@section('title', 'Student Details')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-2">Student Details</h1>
                <p class="text-muted">Complete student information and documentation</p>
            </div>

            <!-- Main Card -->
            <div id="printable-area" class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0 fw-bold">{{ $student->full_name }}</h3>
                            <p class="mb-0 opacity-75">{{ $student->reg_number }}</p>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-light text-primary fs-6 px-3 py-2">
                                {{ $student->course->course_name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <div class="row g-5">
                        <!-- Left Column: Personal Information -->
                        <div class="col-lg-6">
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    Personal Information
                                </h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Full Name</span>
                                        <span class="info-value">{{ $student->full_name }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Email Address</span>
                                        <span class="info-value">
                                            <a href="mailto:{{ $student->email }}" class="text-decoration-none">
                                                {{ $student->email }}
                                            </a>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Phone Number</span>
                                        <span class="info-value">
                                            @if($student->phone)
                                                <a href="tel:{{ $student->phone }}" class="text-decoration-none">
                                                    {{ $student->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">Not provided</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Registration Number</span>
                                        <span class="info-value fw-bold text-primary">{{ $student->reg_number }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Branch</span>
                                        <span class="info-value">{{ $student->branch->name ?? 'Not assigned' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Class/Course</span>
                                        <span class="info-value">{{ $student->course->course_name ?? 'Not assigned' }}</span>
                                    </div>
                                  <div class="info-item">
    <span class="info-label">Account Password</span>
    <span class="info-value">
        <div class="password-management">
            <div class="password-display">
                <div class="password-field">
                    <input type="password" 
                           id="passwordField" 
                           value="{{ $student->password_plain ?? 'Not available' }}" 
                           class="form-control-plaintext border rounded px-2 py-1" 
                           readonly 
                           style="display: inline-block; width: auto; min-width: 200px; background: #f8f9fa;">
                    <button type="button" 
                            class="btn btn-sm btn-outline-secondary ms-2" 
                            onclick="togglePasswordVisibility()" 
                            id="togglePasswordBtn">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-outline-info ms-1" 
                            onclick="copyPassword()" 
                            title="Copy password">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
                <div class="password-status mt-2">
                    <span class="badge bg-success">
                        <i class="bi bi-shield-check me-1"></i>
                        Password Available
                    </span>
                </div>
            </div>
            <div class="password-actions mt-2">
                <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="resetPassword()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset Password
                </button>
                <button type="button" class="btn btn-sm btn-outline-info me-2" onclick="sendPasswordReset()">
                    <i class="bi bi-envelope me-1"></i>Send Reset Link
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="generateNewPassword()">
                    <i class="bi bi-key me-1"></i>Generate New
                </button>
            </div>
        </div>
        <small class="text-muted d-block mt-2">
            <i class="bi bi-info-circle me-1"></i>
            Current password is displayed above. Handle with care and ensure secure practices.
        </small>
    </span>
</div>

                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Photos -->
                        <div class="col-lg-6">
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="bi bi-camera text-primary me-2"></i>
                                    Student Photos
                                </h4>
                                <div class="photos-grid">
                                    <div class="photo-item">
                                        <h6 class="photo-title">ID Photo</h6>
                                        <div class="photo-frame">
                                            @if($student->id_photo)
                                                <img src="data:image/jpeg;base64,{{ $student->id_photo }}"
                                                     alt="Student ID Photo"
                                                     class="photo-img"
                                                     onclick="openPhotoModal(this)">
                                            @else
                                                <span class="text-muted">No ID photo uploaded</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="photo-item">
                                        <h6 class="photo-title">Passport Photo</h6>
                                        <div class="photo-frame">
                                            @if($student->passport_photo)
                                                <img src="data:image/jpeg;base64,{{ $student->passport_photo }}"
                                                     alt="Student Passport Photo"
                                                     class="photo-img"
                                                     onclick="openPhotoModal(this)">
                                            @else
                                                <span class="text-muted">No passport photo uploaded</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- School Fee Section -->
                    <div class="section-divider"></div>
                    
                    <div class="fee-section">
                        <h4 class="section-title">
                            <i class="bi bi-cash-coin text-success me-2"></i>
                            School Fee Information
                        </h4>
                        @if($schoolFee)
                            <div class="fee-card">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="fee-item">
                                            <span class="fee-label">Academic Year</span>
                                            <span class="fee-value">{{ $schoolFee->academicYear->year }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fee-item">
                                            <span class="fee-label">Term</span>
                                            <span class="fee-value">{{ $schoolFee->term->term }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fee-item">
                                            <span class="fee-label">Amount</span>
                                            <span class="fee-value fee-amount">{{ number_format($schoolFee->amount, 2) }} RWF</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 shadow-sm">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                No fee information available for the selected academic year and term.
                            </div>
                        @endif
                    </div>

                    <!-- School Fees Breakdown -->
                    <div class="section-divider"></div>
                    <div class="fee-section">
                        <h4 class="section-title">
                            <i class="bi bi-cash-coin text-success me-2"></i>
                            School Fees Breakdown
                        </h4>
                        @php
                            $course = $student->course;
                            $discount = $student->discount ?? 0;
                            
                            // Fee amounts
                            $registrationFee = $course ? floatval($course->registration_fee) : 0;
                            $tuitionFee = $course ? floatval($course->tuition_fee) : 0;
                            $practicalFee = $course ? floatval($course->practical_fee) : 0;
                            $tripFee = $course ? floatval($course->trip_fee) : 0;
                            $studentCardFee;
                            
                            // Calculate discountable fees (exclude registration fee from discount)
                            $discountableFees = $tuitionFee + $practicalFee + $tripFee;
                            
                            // Apply discount only to discountable fees
                            $discountAmount = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;
                            
                            // Total fees after discount (registration fee is not discounted)
                            $totalFees = $registrationFee + $studentCardFee+ ($discountableFees - $discountAmount);
                            
                            // Get payments from student_transactions table
                            $studentTransactions = \App\Models\StudentTransaction::where('student_id', $student->id)
                                ->whereIn('status', ['successful', 'paid'])
                                ->get();
                            
                            $paid = [
                                'registration' => $studentTransactions->where('type', 'registration')->sum('amount'),
                                'tuition'      => $studentTransactions->where('type', 'school_fee')->sum('amount'),
                                'practical'    => $studentTransactions->where('type', 'practical')->sum('amount'),
                                'trip'         => $studentTransactions->where('type', 'trip')->sum('amount'),
                                'school card'  =>$studentTransactions->where('type', 'student_card')->sum('amount'),
                            ];
                            
                            $totalPaid = $studentTransactions->sum('amount');
                            $remaining = $totalFees - $totalPaid;
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Paid</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Registration Fee</td>
                                        <td>{{ number_format($registrationFee, 2) }}</td>
                                        <td>{{ number_format($paid['registration'], 2) }}</td>
                                        <td>
                                            @if($paid['registration'] >= $registrationFee && $registrationFee > 0)
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-danger">Not Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>School Fee</td>
                                        <td>{{ number_format($tuitionFee, 2) }}</td>
                                        <td>{{ number_format($paid['tuition'], 2) }}</td>
                                        <td>
                                            @if($paid['tuition'] >= $tuitionFee && $tuitionFee > 0)
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-danger">Not Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Practical Fee</td>
                                        <td>{{ number_format($practicalFee, 2) }}</td>
                                        <td>{{ number_format($paid['practical'], 2) }}</td>
                                        <td>
                                            @if($paid['practical'] >= $practicalFee && $practicalFee > 0)
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-danger">Not Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Trip Fee</td>
                                        <td>{{ number_format($tripFee, 2) }}</td>
                                        <td>{{ number_format($paid['trip'], 2) }}</td>
                                        <td>
                                            @if($paid['trip'] >= $tripFee && $tripFee > 0)
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-danger">Not Paid</span>
                                            @endif
                                        </td>
                                    </tr>
<tr>
    <td>student Card Fee</td>
    <td>{{ number_format($studentCardFee, 2) }}</td>
    <td>{{ number_format($studentCardPaid, 2) }}</td>
    <td>
        @if($studentCardPaid >= $studentCardFee && $studentCardFee > 0)
            <span class="badge bg-success">Paid</span>
        @else
            <span class="badge bg-danger">Not Paid</span>
        @endif
    </td>
</tr>

                                </tbody>
                            </table>
                        </div>
                        
                      <!-- Detailed Fee Breakdown -->
<div class="mt-4">
    <h6 class="text-muted mb-3">Fee Calculation Details:</h6>
    <div class="row">
        <!-- Fee Components -->
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="card-title">Fee Components</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between">
                            <span>Registration Fee:</span>
                            <span>{{ number_format($registrationFee, 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>School Fee:</span>
                            <span>{{ number_format($tuitionFee, 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Practical Fee:</span>
                            <span>{{ number_format($practicalFee, 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Trip Fee:</span>
                            <span>{{ number_format($tripFee, 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Student Card Fee:</span>
                            <span>{{ number_format($studentCardFee, 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between border-top pt-2">
                            <strong>Subtotal (Discountable):</strong>
                            <strong>{{ number_format($discountableFees, 2) }} RWF</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="card-title">Payment Summary</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between">
                            <span>Registration Paid:</span>
                            <span>{{ number_format($paid['registration'], 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>School Fees Paid:</span>
                            <span>{{ number_format($paid['tuition'], 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Practical Paid:</span>
                            <span>{{ number_format($paid['practical'], 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Trip Paid:</span>
                            <span>{{ number_format($paid['trip'], 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Student Card Paid:</span>
                            <span>{{ number_format($studentCardPaid, 2) }} RWF</span>
                        </li>
                        <li class="d-flex justify-content-between border-top pt-2">
                            <strong>Total Paid:</strong>
                            <strong>{{ number_format($totalPaid + $studentCardPaid, 2) }} RWF</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

                        <div class="mt-3">
                            @if($discount > 0)
                                <span class="badge bg-secondary fs-6">Discount: {{ $discount }}% (Applied to tuition, practical & trip fees only)</span>
                                <span class="badge bg-success fs-6 ms-2">Discount Amount: {{ number_format($discountAmount, 2) }}</span>
                            @else
                                <span class="badge bg-secondary fs-6">No Discount Applied</span>
                            @endif
                            <span class="badge bg-primary fs-6 ms-2">Total to Pay: {{ number_format($totalFees, 2) }}</span>
                            <span class="badge bg-info fs-6 ms-2">Total Paid: {{ number_format($totalPaid, 2) }}</span>
                            <span class="badge bg-warning text-dark fs-6 ms-2">Remaining: {{ number_format($remaining, 2) }}</span>
                        </div>
                    </div>

                    <!-- Supporting Documents -->
                    <div class="section-divider"></div>
                    
                    @php
    function getMimeType($base64) {
        if (!$base64) return null;
        $header = substr($base64, 0, 5);
        if ($header === '/9j/4') return 'image/jpeg';
        if (strpos($base64, 'iVBOR') === 0) return 'image/png';
        if (strpos($base64, 'JVBER') === 0) return 'application/pdf';
        return 'application/octet-stream';
    }
@endphp

<div class="documents-section">
    <h4 class="section-title">
        <i class="bi bi-file-earmark-text text-info me-2"></i>
        Supporting Documents
    </h4>
    <div class="row g-4">
        <!-- Senior 6 Document -->
        <div class="col-md-4">
            <div class="document-card">
                <div class="document-icon">
                    <i class="bi bi-file-earmark-pdf"></i>
                </div>
                <h6 class="document-title">Senior 6 Certificate</h6>
                @if ($student->senior6_document)
                    @php $mime = getMimeType($student->senior6_document); @endphp
                    @if($mime === 'application/pdf')
                        <a href="data:application/pdf;base64,{{ $student->senior6_document }}"
                           target="_blank"
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View PDF
                        </a>
                    @elseif($mime === 'image/jpeg' || $mime === 'image/png')
                        <img src="data:{{ $mime }};base64,{{ $student->senior6_document }}"
                             alt="Senior 6 Document"
                             class="photo-img"
                             style="max-width:200px;">
                    @else
                        <span class="text-muted">Unknown file format</span>
                    @endif
                @else
                    <span class="text-muted">Not uploaded</span>
                @endif
            </div>
        </div>
        <!-- Senior 3 Document -->
        <div class="col-md-4">
            <div class="document-card">
                <div class="document-icon">
                    <i class="bi bi-file-earmark-pdf"></i>
                </div>
                <h6 class="document-title">Senior 3 Certificate</h6>
                @if ($student->senior3_document)
                    @php $mime = getMimeType($student->senior3_document); @endphp
                    @if($mime === 'application/pdf')
                        <a href="data:application/pdf;base64,{{ $student->senior3_document }}"
                           target="_blank"
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View PDF
                        </a>
                    @elseif($mime === 'image/jpeg' || $mime === 'image/png')
                        <img src="data:{{ $mime }};base64,{{ $student->senior3_document }}"
                             alt="Senior 3 Document"
                             class="photo-img"
                             style="max-width:200px;">
                    @else
                        <span class="text-muted">Unknown file format</span>
                    @endif
                @else
                    <span class="text-muted">Not uploaded</span>
                @endif
            </div>
        </div>
        <!-- Other Supporting Document -->
        <div class="col-md-4">
            <div class="document-card">
                <div class="document-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h6 class="document-title">Other Documents</h6>
                @if ($student->other_supporting_document)
                    @php $mime = getMimeType($student->other_supporting_document); @endphp
                    @if($mime === 'application/pdf')
                        <a href="data:application/pdf;base64,{{ $student->other_supporting_document }}"
                           target="_blank"
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View PDF
                        </a>
                    @elseif($mime === 'image/jpeg' || $mime === 'image/png')
                        <img src="data:{{ $mime }};base64,{{ $student->other_supporting_document }}"
                             alt="Other Supporting Document"
                             class="photo-img"
                             style="max-width:200px;">
                    @else
                        <span class="text-muted">Unknown file format</span>
                    @endif
                @else
                    <span class="text-muted">Not uploaded</span>
                @endif
            </div>
        </div>
    </div>
</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="bi bi-arrow-left me-2"></i>Back to Students
                </a>
                <div>
                    <button onclick="printStudentDetails()" class="btn btn-primary btn-lg px-4 me-2">
                        <i class="bi bi-printer me-2"></i>Print Details
                    </button>
                    <button class="btn btn-success btn-lg px-4" onclick="exportToPDF()">
                        <i class="bi bi-file-pdf me-2"></i>Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPhoto" src="" alt="Student Photo" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .section-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    .info-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 2rem;
        height: 100%;
    }

    .info-grid {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        color: #2c3e50;
        font-weight: 500;
    }

    .password-management {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .password-status .badge {
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
    }

    .password-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .password-actions .btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .password-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .photos-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .photo-item {
        text-align: center;
    }

    .photo-title {
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .photo-frame {
        border: 3px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .photo-frame:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .photo-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .photo-img:hover {
        transform: scale(1.05);
    }

    .section-divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #e9ecef, transparent);
        margin: 3rem 0;
    }

    .fee-section {
        margin: 2rem 0;
    }

    .fee-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 2rem;
        border: 1px solid #dee2e6;
    }

    .fee-item {
        text-align: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .fee-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .fee-value {
        display: block;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .password-display {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
}

.password-field {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.password-management {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.password-status .badge {
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
}

.password-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.password-actions .btn {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.password-actions .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Print styles */
@media print {
    .password-actions,
    .password-field button {
        display: none !important;
    }
    
    .password-field input {
        border: 1px solid #ddd !important;
        background: white !important;
    }
}

    .fee-amount {
        color: #28a745;
        font-size: 1.3rem;
        font-weight: 700;
    }

    .documents-section {
        margin: 2rem 0;
    }

    .document-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        height: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .document-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border-color: #007bff;
    }

    .document-icon {
        font-size: 2.5rem;
        color: #007bff;
        margin-bottom: 1rem;
    }

    .document-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .photos-grid {
            grid-template-columns: 1fr;
        }
        
        .info-section {
            padding: 1.5rem;
        }
        
        .fee-card {
            padding: 1.5rem;
        }
    }

    @media print {
        .btn, .modal, .section-divider {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .bg-gradient-primary {
            background: #007bff !important;
            color: white !important;
        }
    }
</style>

<script>
    function togglePasswordVisibility() {
    const passwordField = document.getElementById('passwordField');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.className = 'bi bi-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleIcon.className = 'bi bi-eye';
    }
}

// Copy password to clipboard
function copyPassword() {
    const passwordField = document.getElementById('passwordField');
    
    // Create a temporary input to copy the password
    const tempInput = document.createElement('input');
    tempInput.value = passwordField.value;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    
    // Show feedback
    const copyBtn = event.target.closest('button');
    const originalHTML = copyBtn.innerHTML;
    copyBtn.innerHTML = '<i class="bi bi-check"></i>';
    copyBtn.classList.remove('btn-outline-info');
    copyBtn.classList.add('btn-success');
    
    setTimeout(() => {
        copyBtn.innerHTML = originalHTML;
        copyBtn.classList.remove('btn-success');
        copyBtn.classList.add('btn-outline-info');
    }, 2000);
}

// Generate new password
function generateNewPassword() {
    if (confirm('Generate a new password for this student? The old password will be replaced.')) {
        // Generate a random password
        const newPassword = generateRandomPassword(8);
        
        // Update the password field
        document.getElementById('passwordField').value = newPassword;
        
        // You can implement this with an AJAX call to your Laravel route
        fetch(`/admin/students/{{ $student->id }}/update-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                student_id: {{ $student->id }},
                new_password: newPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('New password generated successfully: ' + newPassword);
            } else {
                alert('Error generating new password: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

// Generate random password
function generateRandomPassword(length) {
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    return password;
}

// Updated reset password function
function resetPassword() {
    if (confirm('Are you sure you want to reset this student\'s password? A new password will be generated automatically.')) {
        const newPassword = generateRandomPassword(8);
        
        // Update the display
        document.getElementById('passwordField').value = newPassword;
        
        // You can implement this with an AJAX call to your Laravel route
        fetch(`/admin/students/{{ $student->id }}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                student_id: {{ $student->id }},
                new_password: newPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Password reset successfully! New password: ' + newPassword);
            } else {
                alert('Error resetting password: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}
    // Password management functions
    function resetPassword() {
        if (confirm('Are you sure you want to reset this student\'s password? They will need to create a new one.')) {
            // You can implement this with an AJAX call to your Laravel route
            fetch(`/admin/students/{{ $student->id }}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    student_id: {{ $student->id }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password reset successfully! New temporary password: ' + data.new_password);
                } else {
                    alert('Error resetting password: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
    }

    function sendPasswordReset() {
        if (confirm('Send password reset link to ' + '{{ $student->email }}' + '?')) {
            // You can implement this with an AJAX call to your Laravel route
            fetch(`/admin/students/{{ $student->id }}/send-reset-link`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    student_id: {{ $student->id }},
                    email: '{{ $student->email }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password reset link sent successfully to ' + '{{ $student->email }}');
                } else {
                    alert('Error sending reset link: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
    }

    function openPhotoModal(img) {
        const modal = new bootstrap.Modal(document.getElementById('photoModal'));
        const modalPhoto = document.getElementById('modalPhoto');
        modalPhoto.src = img.src;
        modalPhoto.alt = img.alt;
        modal.show();
    }

    function printStudentDetails() {
        const printContents = document.getElementById('printable-area').innerHTML;
        const originalContents = document.body.innerHTML;

        // Create a new window for printing
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Student Details - {{ $student->full_name }}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: Arial, sans-serif; }
                    .card { border: 1px solid #ddd; }
                    .bg-gradient-primary { background: #007bff !important; color: white; }
                    .btn, .password-display button { display: none; }
                    .password-hidden { font-family: monospace; }
                </style>
            </head>
            <body>
                ${printContents}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    }

    function exportToPDF() {
        // You can implement PDF export functionality here
        // For example, using jsPDF or html2pdf libraries
        alert('PDF export feature can be implemented using libraries like jsPDF or html2pdf');
    }

    // Initialize tooltips if Bootstrap is available
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });
</script>
@endsection