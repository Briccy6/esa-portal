@extends('dos.layout')

@section('title', 'Student Cards')

@section('content')
@php
    $schoolInfo = \App\Models\SchoolInformation::getActive();
    $schoolName = $schoolInfo->school_name ?? config('app.name', 'Excellent Servant Academy');
    $schoolAddress = $schoolInfo->address ?? 'Excellent Servant Academy, Rwanda';
    $schoolPhone = $schoolInfo->phone ?? '+250 788 525 247';
    $schoolEmail = $schoolInfo->email ?? 'info@excellentservantacademy.com';
    $schoolWebsite = $schoolInfo->website ?? 'www.excellentservantacademy.com';
    $logoUrl = $schoolInfo ? $schoolInfo->logo_url : \App\Helpers\LogoHelper::getLogoUrl();
@endphp

<div class="container-fluid p-4 student-card-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-id-card me-2"></i>
                Student Cards
            </h2>
            <p class="text-muted mb-0">Generate and manage student ID cards (CR80 Standard)</p>
        </div>
        <div class="d-flex gap-2 no-print">
            <button class="btn btn-outline-success" onclick="try { generateAllCards(); } catch(e) { console.error('Generate all cards error:', e); alert('Unable to generate all cards. Please try again.'); }">
                <i class="bi bi-download me-1"></i> Generate All Cards
            </button>
            <a href="{{ route('dos.students.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 no-print">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $students->count() }}</h4>
                            <p class="card-text">Total Students</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-id-card fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $students->where('payed_registration_fees', 1)->count() }}</h4>
                            <p class="card-text">Active Students</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ \App\Models\StudentCard::count() }}</h4>
                            <p class="card-text">Cards Generated</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-qr-code fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 no-print">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.students.StudentCards') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="branch_filter" class="form-label">Branch</label>
                        <select name="branch" id="branch_filter" class="form-select">
                            <option value="">All Branches</option>
                            @if(isset($branches))
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="course_filter" class="form-label">Course</label>
                        <select name="course" id="course_filter" class="form-select">
                            <option value="">All Courses</option>
                            @if(isset($courses))
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">Status</label>
                        <select name="status" id="status_filter" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (Paid Fees)</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive (Unpaid Fees)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('dos.students.StudentCards') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Student Cards Grid -->
    <div class="row" id="student-card-grid">
        @if(isset($students) && $students->count() > 0)
            @foreach($students as $student)
                @php
                    $studentCard = \App\Models\StudentCard::where('student_id', $student->id)->first();
                @endphp
                <div class="col-md-6 col-lg-4 mb-4 student-card-column" data-student-id="{{ $student->id }}">
                    <div class="card student-card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">{{ $student->full_name }}</h6>
                            @if($studentCard)
                                <span class="badge bg-success">Card Generated</span>
                            @else
                                <span class="badge bg-secondary">No Card</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Registration Number:</strong></td>
                                    <td>{{ $student->reg_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Branch:</strong></td>
                                    <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Academic Year:</strong></td>
                                    <td>{{ $student->academicYear->year ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $student->payed_registration_fees ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $student->payed_registration_fees ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer no-print">
                            <div class="btn-group w-100" role="group">
                                @if($studentCard)
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#previewCardModal{{ $student->id }}" title="Preview Card">
                                        <i class="bi bi-eye me-1"></i> Preview
                                    </button>
                                    <button onclick="try { printCard({{ $student->id }}); } catch(e) { console.error('Print error:', e); alert('Unable to print card. Please try again.'); }" class="btn btn-outline-secondary" title="Print Card">
                                        <i class="bi bi-printer me-1"></i> Print
                                    </button>
                                @else
                                    <button onclick="try { generateCard({{ $student->id }}); } catch(e) { console.error('Card generation error:', e); alert('Unable to generate card. Please try again.'); }" class="btn btn-primary w-100">
                                        <i class="bi bi-download me-1"></i> Generate Card
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($studentCard)
                    <div class="modal fade" id="previewCardModal{{ $student->id }}" tabindex="-1" aria-labelledby="previewCardModalLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header">
                                    <div><h2 class="modal-title h5 fw-semibold" id="previewCardModalLabel{{ $student->id }}">Student ID Card - {{ $student->full_name }}</h2><p class="text-muted small mb-0">CR80 Standard: 85.60mm × 53.98mm | 300 DPI</p></div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3 p-md-4">
                                    <ul class="nav nav-tabs mb-3" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="front-tab-{{ $student->id }}" data-bs-toggle="tab" data-bs-target="#front-{{ $student->id }}" type="button" role="tab" aria-controls="front-{{ $student->id }}" aria-selected="true">Front Side</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="back-tab-{{ $student->id }}" data-bs-toggle="tab" data-bs-target="#back-{{ $student->id }}" type="button" role="tab" aria-controls="back-{{ $student->id }}" aria-selected="false">Back Side</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="both-tab-{{ $student->id }}" data-bs-toggle="tab" data-bs-target="#both-{{ $student->id }}" type="button" role="tab" aria-controls="both-{{ $student->id }}" aria-selected="false">Both Sides</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="front-{{ $student->id }}" role="tabpanel" aria-labelledby="front-tab-{{ $student->id }}">
                                            <div class="card-preview-container">
                                                <div id="cardFront{{ $student->id }}" class="cr80-card cr80-front">
                                                    <div class="card-header">
                                                        <img src="{{ $logoUrl }}" alt="School Logo" class="school-logo">
                                                        <div class="school-info">
                                                            <div class="school-name">{{ $schoolName }}</div>
                                                            <div class="card-type">STUDENT IDENTIFICATION CARD</div>
                                                        </div>
                                                        <div class="academic-year">{{ $student->academicYear->year ?? 'Student' }}</div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="photo-section">
                                                            @if($studentCard->passport_photo)
                                                                <img src="data:image/jpeg;base64,{{ $studentCard->passport_photo }}" alt="Student Photo">
                                                            @elseif($student->passport_photo)
                                                                <img src="data:image/jpeg;base64,{{ $student->passport_photo }}" alt="Student Photo">
                                                            @else
                                                                <div class="photo-placeholder">{{ strtoupper(substr($student->full_name ?? '?', 0, 1)) }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="student-info">
                                                            <div class="student-name">{{ $student->full_name ?? 'Unknown Student' }}</div>
                                                            <div class="reg-number">{{ $student->reg_number ?? '—' }}</div>
                                                            <div class="info-row">
                                                                <span class="label">Programme</span>
                                                                <span class="value">{{ $student->course->course_name ?? '—' }}</span>
                                                            </div>
                                                            <div class="info-row">
                                                                <span class="label">Branch</span>
                                                                <span class="value">{{ $student->branch->name ?? '—' }}</span>
                                                            </div>
                                                            <div class="info-row">
                                                                <span class="label">Academic Year</span>
                                                                <span class="value">{{ $student->academicYear->year ?? '—' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="issue-date">Issued: {{ $studentCard->generated_at ? \Carbon\Carbon::parse($studentCard->generated_at)->format('M Y') : '—' }}</div>
                                                        <div class="status-badge">VALID STUDENT</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="back-{{ $student->id }}" role="tabpanel" aria-labelledby="back-tab-{{ $student->id }}">
                                            <div class="card-preview-container">
                                                <div id="cardBack{{ $student->id }}" class="cr80-card cr80-back">
                                                    <div class="qr-section">
                                                        @if($studentCard->qr_code && $studentCard->verification_url)
                                                            <div class="qr-code-wrapper">
                                                                {!! $studentCard->qr_code !!}
                                                            </div>
                                                            <div class="qr-instruction">Scan to verify student identity</div>
                                                        @else
                                                            <div class="qr-unavailable">QR Code Unavailable</div>
                                                        @endif
                                                    </div>
                                                    <div class="notice-section">
                                                        <div class="notice-title">IMPORTANT NOTICE</div>
                                                        <div class="notice-text">
                                                            <p><strong>IF FOUND:</strong> Please return this card to Excellent Servant Academy administration office.</p>
                                                            <p><strong>WARNING:</strong> This card is institutional property. Unauthorized use, modification, or lending to another person is prohibited and may result in disciplinary action.</p>
                                                        </div>
                                                    </div>
                                                    <div class="contact-section">
                                                        <div class="contact-title">CONTACT INFORMATION</div>
                                                        <div class="contact-item"><i class="bi bi-geo-alt me-2"></i>{{ $schoolAddress }}</div>
                                                        <div class="contact-item"><i class="bi bi-telephone me-2"></i>{{ $schoolPhone }}</div>
                                                        <div class="contact-item"><i class="bi bi-envelope me-2"></i>{{ $schoolEmail }}</div>
                                                        <div class="contact-item"><i class="bi bi-globe me-2"></i>{{ $schoolWebsite }}</div>
                                                    </div>
                                                    <div class="validity-section">
                                                        <div class="validity-item"><span class="label">Academic Year:</span> <span class="value">{{ $student->academicYear->year ?? '—' }}</span></div>
                                                        <div class="validity-item"><span class="label">Issue Date:</span> <span class="value">{{ $studentCard->generated_at ? \Carbon\Carbon::parse($studentCard->generated_at)->format('M d, Y') : '—' }}</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="both-{{ $student->id }}" role="tabpanel" aria-labelledby="both-tab-{{ $student->id }}">
                                            <div class="both-sides-container">
                                                <div class="both-side-wrapper">
                                                    <div class="side-label">FRONT</div>
                                                    <div class="cr80-card cr80-front both-preview">
                                                        <div class="card-header">
                                                            <img src="{{ $logoUrl }}" alt="School Logo" class="school-logo">
                                                            <div class="school-info">
                                                                <div class="school-name">{{ $schoolName }}</div>
                                                                <div class="card-type">STUDENT IDENTIFICATION CARD</div>
                                                            </div>
                                                            <div class="academic-year">{{ $student->academicYear->year ?? 'Student' }}</div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="photo-section">
                                                                @if($studentCard->passport_photo)
                                                                    <img src="data:image/jpeg;base64,{{ $studentCard->passport_photo }}" alt="Student Photo">
                                                                @elseif($student->passport_photo)
                                                                    <img src="data:image/jpeg;base64,{{ $student->passport_photo }}" alt="Student Photo">
                                                                @else
                                                                    <div class="photo-placeholder">{{ strtoupper(substr($student->full_name ?? '?', 0, 1)) }}</div>
                                                                @endif
                                                            </div>
                                                            <div class="student-info">
                                                                <div class="student-name">{{ $student->full_name ?? 'Unknown Student' }}</div>
                                                                <div class="reg-number">{{ $student->reg_number ?? '—' }}</div>
                                                                <div class="info-row">
                                                                    <span class="label">Programme</span>
                                                                    <span class="value">{{ $student->course->course_name ?? '—' }}</span>
                                                                </div>
                                                                <div class="info-row">
                                                                    <span class="label">Branch</span>
                                                                    <span class="value">{{ $student->branch->name ?? '—' }}</span>
                                                                </div>
                                                                <div class="info-row">
                                                                    <span class="label">Academic Year</span>
                                                                    <span class="value">{{ $student->academicYear->year ?? '—' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer">
                                                            <div class="issue-date">Issued: {{ $studentCard->generated_at ? \Carbon\Carbon::parse($studentCard->generated_at)->format('M Y') : '—' }}</div>
                                                            <div class="status-badge">VALID STUDENT</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="both-side-wrapper">
                                                    <div class="side-label">BACK</div>
                                                    <div class="cr80-card cr80-back both-preview">
                                                        <div class="qr-section">
                                                            @if($studentCard->qr_code && $studentCard->verification_url)
                                                                <div class="qr-code-wrapper">
                                                                    {!! $studentCard->qr_code !!}
                                                                </div>
                                                                <div class="qr-instruction">Scan to verify student identity</div>
                                                            @else
                                                                <div class="qr-unavailable">QR Code Unavailable</div>
                                                            @endif
                                                        </div>
                                                        <div class="notice-section">
                                                            <div class="notice-title">IMPORTANT NOTICE</div>
                                                            <div class="notice-text">
                                                                <p><strong>IF FOUND:</strong> Please return this card to Excellent Servant Academy administration office.</p>
                                                                <p><strong>WARNING:</strong> This card is institutional property. Unauthorized use, modification, or lending to another person is prohibited and may result in disciplinary action.</p>
                                                            </div>
                                                        </div>
                                                        <div class="contact-section">
                                                            <div class="contact-title">CONTACT INFORMATION</div>
                                                            <div class="contact-item"><i class="bi bi-geo-alt me-2"></i>{{ $schoolAddress }}</div>
                                                            <div class="contact-item"><i class="bi bi-telephone me-2"></i>{{ $schoolPhone }}</div>
                                                            <div class="contact-item"><i class="bi bi-envelope me-2"></i>{{ $schoolEmail }}</div>
                                                            <div class="contact-item"><i class="bi bi-globe me-2"></i>{{ $schoolWebsite }}</div>
                                                        </div>
                                                        <div class="validity-section">
                                                            <div class="validity-item"><span class="label">Academic Year:</span> <span class="value">{{ $student->academicYear->year ?? '—' }}</span></div>
                                                            <div class="validity-item"><span class="label">Issue Date:</span> <span class="value">{{ $studentCard->generated_at ? \Carbon\Carbon::parse($studentCard->generated_at)->format('M d, Y') : '—' }}</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($studentCard->verification_url)
                                        <div class="verification-link mt-3">
                                            <span class="small text-muted">Verification link</span>
                                            <a href="{{ $studentCard->verification_url }}" target="_blank" rel="noopener noreferrer" class="text-break">{{ $studentCard->verification_url }}</a>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mt-3 mb-0">This card does not have a verification link yet.</div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                    <div class="btn-group me-2" role="group">
                                        <button type="button" class="btn btn-outline-primary" onclick="printCard({{ $student->id }}, 'front')"><i class="bi bi-printer me-2"></i>Print Front</button>
                                        <button type="button" class="btn btn-outline-primary" onclick="printCard({{ $student->id }}, 'back')"><i class="bi bi-printer me-2"></i>Print Back</button>
                                        <button type="button" class="btn btn-primary" onclick="printCard({{ $student->id }}, 'both')"><i class="bi bi-printer me-2"></i>Print Both</button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-success" onclick="downloadCardImage({{ $student->id }}, '{{ $student->full_name }}', 'front')"><i class="bi bi-download me-2"></i>Front PNG</button>
                                        <button type="button" class="btn btn-outline-success" onclick="downloadCardImage({{ $student->id }}, '{{ $student->full_name }}', 'back')"><i class="bi bi-download me-2"></i>Back PNG</button>
                                        <button type="button" class="btn btn-success" onclick="downloadCardPDF({{ $student->id }}, '{{ $student->full_name }}')"><i class="bi bi-file-earmark-pdf me-2"></i>Both PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-id-card fs-1 text-muted"></i>
                    <p class="mt-2">No students found</p>
                    <a href="{{ route('dos.students.index') }}" class="btn btn-primary">
                        <i class="bi bi-people me-1"></i> View Students
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .student-card {
        transition: transform 0.2s ease-in-out;
    }
    .student-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    /* CR80 Card Dimensions: 85.60mm x 53.98mm at 300 DPI = 1011px x 638px */
    /* Aspect ratio: 1.586:1 */
    .card-preview-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background: #f8fafc;
        border-radius: 12px;
    }

    .cr80-card {
        width: 100%;
        max-width: 505px;
        aspect-ratio: 1.586 / 1;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        font-family: 'Poppins', sans-serif;
    }

    .cr80-front {
        display: flex;
        flex-direction: column;
    }

    .cr80-front .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #1e3a5f;
        color: #ffffff;
        min-height: 60px;
    }

    .cr80-front .school-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
        background: #ffffff;
        border-radius: 4px;
        padding: 2px;
    }

    .cr80-front .school-info {
        flex: 1;
    }

    .cr80-front .school-name {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
        text-transform: uppercase;
    }

    .cr80-front .card-type {
        font-size: 9px;
        font-weight: 500;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #e0e7ff;
        margin-top: 2px;
    }

    .cr80-front .academic-year {
        font-size: 10px;
        font-weight: 600;
        padding: 4px 8px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 4px;
        white-space: nowrap;
    }

    .cr80-front .card-body {
        display: flex;
        gap: 16px;
        padding: 16px;
        flex: 1;
    }

    .cr80-front .photo-section {
        width: 90px;
        height: 115px;
        flex-shrink: 0;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cr80-front .photo-section img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cr80-front .photo-placeholder {
        font-size: 32px;
        font-weight: 700;
        color: #9ca3af;
    }

    .cr80-front .student-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .cr80-front .student-name {
        font-size: 16px;
        font-weight: 700;
        color: #1e3a5f;
        line-height: 1.2;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cr80-front .reg-number {
        font-size: 12px;
        font-weight: 600;
        color: #0f766e;
        letter-spacing: 0.5px;
    }

    .cr80-front .info-row {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
    }

    .cr80-front .info-row .label {
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 9px;
        letter-spacing: 0.3px;
    }

    .cr80-front .info-row .value {
        color: #374151;
        font-weight: 600;
    }

    .cr80-front .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 16px;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .cr80-front .issue-date {
        font-size: 9px;
        color: #6b7280;
        font-weight: 500;
    }

    .cr80-front .status-badge {
        font-size: 9px;
        font-weight: 700;
        color: #0f766e;
        background: #ecfdf5;
        padding: 3px 8px;
        border-radius: 3px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Back Card Styles */
    .cr80-back {
        display: flex;
        flex-direction: column;
        padding: 16px;
        gap: 12px;
    }

    .cr80-back .qr-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 12px;
        background: #f9fafb;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }

    .cr80-back .qr-code-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cr80-back .qr-code-wrapper svg {
        width: 80px;
        height: 80px;
    }

    .cr80-back .qr-instruction {
        font-size: 9px;
        color: #6b7280;
        font-weight: 500;
        text-align: center;
    }

    .cr80-back .qr-unavailable {
        font-size: 10px;
        color: #9ca3af;
        font-weight: 500;
        padding: 20px;
        border: 1px dashed #d1d5db;
        border-radius: 4px;
    }

    .cr80-back .notice-section {
        padding: 10px;
        background: #fef3c7;
        border-radius: 6px;
        border-left: 3px solid #f59e0b;
    }

    .cr80-back .notice-title {
        font-size: 10px;
        font-weight: 700;
        color: #92400e;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .cr80-back .notice-text {
        font-size: 9px;
        color: #78350f;
        line-height: 1.4;
    }

    .cr80-back .notice-text p {
        margin: 3px 0;
    }

    .cr80-back .contact-section {
        padding: 10px;
        background: #f3f4f6;
        border-radius: 6px;
    }

    .cr80-back .contact-title {
        font-size: 10px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .cr80-back .contact-item {
        font-size: 9px;
        color: #4b5563;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
    }

    .cr80-back .validity-section {
        display: flex;
        justify-content: space-between;
        padding: 8px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
    }

    .cr80-back .validity-item {
        font-size: 9px;
    }

    .cr80-back .validity-item .label {
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
    }

    .cr80-back .validity-item .value {
        color: #374151;
        font-weight: 600;
    }

    /* Both Sides View */
    .both-sides-container {
        display: flex;
        gap: 24px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .both-side-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .side-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .both-preview {
        max-width: 350px;
    }

    .verification-link {
        display: grid;
        gap: 4px;
    }

    @media (max-width: 768px) {
        .both-sides-container {
            flex-direction: column;
            align-items: center;
        }

        .both-preview {
            max-width: 280px;
        }

        .modal-footer {
            flex-direction: column;
            gap: 10px;
        }

        .modal-footer .btn-group {
            width: 100%;
        }

        .modal-footer .btn-group .btn {
            flex: 1;
        }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function generateCard(studentId) {
    fetch(`/dos/students/${studentId}/generate-card`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Student card generated successfully!');
            location.reload();
        } else {
            alert('Error generating card: ' + data.message);
        }
    });
}

function printCard(studentId, side) {
    const frontElement = document.getElementById('cardFront' + studentId);
    const backElement = document.getElementById('cardBack' + studentId);

    if (!frontElement) {
        alert('Card front not found.');
        return;
    }

    let printContent = '';
    let printTitle = 'Student ID Card';

    if (side === 'front') {
        printContent = frontElement.outerHTML;
        printTitle = 'Student ID Card - Front';
    } else if (side === 'back') {
        if (!backElement) {
            alert('Card back not found.');
            return;
        }
        printContent = backElement.outerHTML;
        printTitle = 'Student ID Card - Back';
    } else if (side === 'both') {
        if (!backElement) {
            alert('Card back not found.');
            return;
        }
        printContent = `
            <div style="display: flex; flex-direction: column; gap: 10mm; align-items: center;">
                <div style="margin-bottom: 5mm; font-size: 10pt; font-weight: bold;">FRONT SIDE</div>
                ${frontElement.outerHTML}
                <div style="page-break-before: always; margin-bottom: 5mm; font-size: 10pt; font-weight: bold;">BACK SIDE</div>
                ${backElement.outerHTML}
            </div>
        `;
        printTitle = 'Student ID Card - Both Sides';
    } else {
        document.querySelectorAll('#student-card-grid .student-card-column').forEach(function (column) {
            column.classList.toggle('print-selected', column.dataset.studentId === String(studentId));
        });
        document.body.classList.add('printing-student-card');
        window.addEventListener('afterprint', function () {
            document.body.classList.remove('printing-student-card');
            document.querySelectorAll('#student-card-grid .student-card-column').forEach(function (column) {
                column.classList.remove('print-selected');
            });
        }, { once: true });
        window.print();
        return;
    }

    const printWindow = window.open('', '_blank', 'width=850,height=650');
    if (!printWindow) {
        alert('Allow pop-ups to print the student card.');
        return;
    }

    printWindow.document.write(`<!doctype html>
        <html lang="en"><head><meta charset="utf-8"><title>${printTitle}</title>
        <style>
            * { box-sizing: border-box; }
            body { margin: 0; padding: 10mm; font-family: 'Poppins', Arial, sans-serif; }
            .cr80-card {
                width: 85.6mm;
                height: 53.98mm;
                overflow: hidden;
                border: 0.3mm solid #cbd5e1;
                border-radius: 2mm;
                background: #ffffff;
                color: #173247;
            }
            .cr80-front { display: flex; flex-direction: column; }
            .cr80-front .card-header {
                display: flex;
                align-items: center;
                gap: 2mm;
                padding: 2mm 3mm;
                background: #1e3a5f;
                color: #ffffff;
                min-height: 10mm;
            }
            .cr80-front .school-logo {
                width: 7mm;
                height: 7mm;
                object-fit: contain;
                background: #ffffff;
                border-radius: 1mm;
            }
            .cr80-front .school-name { font-size: 7pt; font-weight: bold; text-transform: uppercase; }
            .cr80-front .card-type { font-size: 5pt; text-transform: uppercase; color: #e0e7ff; }
            .cr80-front .academic-year {
                font-size: 5pt;
                padding: 1mm 2mm;
                border: 0.2mm solid rgba(255,255,255,0.3);
                border-radius: 1mm;
            }
            .cr80-front .card-body {
                display: flex;
                gap: 2mm;
                padding: 2mm 3mm;
                flex: 1;
            }
            .cr80-front .photo-section {
                width: 16mm;
                height: 21mm;
                border: 0.3mm solid #e5e7eb;
                border-radius: 1mm;
                overflow: hidden;
                background: #f3f4f6;
            }
            .cr80-front .photo-section img { width: 100%; height: 100%; object-fit: cover; }
            .cr80-front .student-name { font-size: 8pt; font-weight: bold; color: #1e3a5f; }
            .cr80-front .reg-number { font-size: 6pt; font-weight: bold; color: #0f766e; }
            .cr80-front .info-row { font-size: 5pt; display: flex; justify-content: space-between; }
            .cr80-front .info-row .label { color: #6b7280; text-transform: uppercase; }
            .cr80-front .info-row .value { color: #374151; font-weight: bold; }
            .cr80-front .card-footer {
                display: flex;
                justify-content: space-between;
                padding: 1mm 3mm;
                border-top: 0.2mm solid #e5e7eb;
                background: #f9fafb;
                font-size: 4pt;
            }
            .cr80-back { display: flex; flex-direction: column; padding: 2mm; gap: 1mm; }
            .cr80-back .qr-section {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 1mm;
                background: #f9fafb;
                border: 0.2mm solid #e5e7eb;
                border-radius: 1mm;
            }
            .cr80-back .qr-code-wrapper svg { width: 12mm; height: 12mm; }
            .cr80-back .qr-instruction { font-size: 4pt; text-align: center; }
            .cr80-back .notice-section {
                padding: 1mm;
                background: #fef3c7;
                border-left: 0.5mm solid #f59e0b;
                font-size: 4pt;
            }
            .cr80-back .notice-title { font-weight: bold; text-transform: uppercase; }
            .cr80-back .contact-section {
                padding: 1mm;
                background: #f3f4f6;
                font-size: 4pt;
            }
            .cr80-back .validity-section {
                display: flex;
                justify-content: space-between;
                padding: 1mm;
                border: 0.2mm solid #e5e7eb;
                font-size: 4pt;
            }
            @page { size: A4; margin: 10mm; }
            @media print {
                body { padding: 0; }
                .cr80-card { page-break-inside: avoid; }
            }
        </style></head><body>${printContent}</body></html>`);
    printWindow.document.close();
    printWindow.focus();
    printWindow.addEventListener('afterprint', function () { printWindow.close(); }, { once: true });
    printWindow.setTimeout(function () { printWindow.print(); }, 500);
}

function downloadCardImage(studentId, studentName, side) {
    const elementId = side === 'front' ? 'cardFront' + studentId : 'cardBack' + studentId;
    const element = document.getElementById(elementId);

    if (!element) {
        alert('Card ' + side + ' not found.');
        return;
    }

    html2canvas(element, { scale: 3, useCORS: true, backgroundColor: '#ffffff' })
        .then(function (canvas) {
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'student_card_' + side + '_' + String(studentName || 'student').trim().replace(/[^a-z0-9_-]+/gi, '_') + '.png';
            link.click();
        })
        .catch(function (error) {
            console.error('Student card image generation failed:', error);
            alert('Unable to download this student card. Please try again.');
        });
}

function downloadCardPDF(studentId, studentName) {
    const frontElement = document.getElementById('cardFront' + studentId);
    const backElement = document.getElementById('cardBack' + studentId);

    if (!frontElement || !backElement) {
        alert('Both card sides must be available to generate PDF.');
        return;
    }

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'mm', 'a4');

    const cardWidth = 85.6;
    const cardHeight = 53.98;
    const margin = 10;
    const xPos = (210 - cardWidth) / 2;

    Promise.all([
        html2canvas(frontElement, { scale: 3, useCORS: true, backgroundColor: '#ffffff' }),
        html2canvas(backElement, { scale: 3, useCORS: true, backgroundColor: '#ffffff' })
    ]).then(function (canvases) {
        pdf.setFontSize(10);
        pdf.text('FRONT SIDE', xPos, margin);
        pdf.addImage(canvases[0].toDataURL('image/png'), 'PNG', xPos, margin + 5, cardWidth, cardHeight);

        pdf.addPage();
        pdf.setFontSize(10);
        pdf.text('BACK SIDE', xPos, margin);
        pdf.addImage(canvases[1].toDataURL('image/png'), 'PNG', xPos, margin + 5, cardWidth, cardHeight);

        pdf.save('student_card_both_' + String(studentName || 'student').trim().replace(/[^a-z0-9_-]+/gi, '_') + '.pdf');
    }).catch(function (error) {
        console.error('PDF generation failed:', error);
        alert('Unable to generate PDF. Please try again.');
    });
}

function generateAllCards() {
    if(confirm('Generate cards for all students? This may take some time.')) {
        fetch('/dos/students/generate-cards-bulk', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error generating cards: ' + data.message);
            }
        });
    }
}
</script>

<style media="print">
.student-card {
    break-inside: avoid;
    page-break-inside: avoid;
}
.btn-group {
    display: none !important;
}
.no-print {
    display: none !important;
}
</style>
@endsection
