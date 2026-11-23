@extends('staff.partials.sidebar_sec')

@section('title', 'Generated Student Cards')

@section('content')
@php
    $schoolInfo = \App\Models\SchoolInformation::getActive();
    $schoolName = $schoolInfo->school_name ?? config('app.name', 'Excellent Servant Academy');
    $schoolAddress = 'Kigali, Ndamage House, 2nd Floor';
    $schoolPhone = $schoolInfo->phone ?? '+250 788 525 247';
    $schoolEmail = $schoolInfo->email ?? 'info@excellentservantacademy.com';
    $schoolWebsite = $schoolInfo->website ?? 'www.excellentservantacademy.com';
    $logoUrl = $schoolInfo ? $schoolInfo->logo_url : \App\Helpers\LogoHelper::getLogoUrl();
@endphp

<div class="container-fluid py-4 student-cards-page">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="eyebrow mb-1">Student records</p>
            <h1 class="page-title mb-1">Generated Student Cards</h1>
            <p class="page-subtitle mb-0">Review, print, and download student ID cards.</p>
        </div>
        <a href="{{ route('staff.students.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2" aria-hidden="true"></i>Back to Students
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="summary-card">
                <span class="summary-icon"><i class="fas fa-id-card" aria-hidden="true"></i></span>
                <div><div class="summary-label">Total cards</div><div class="summary-value">{{ $cards->total() }}</div></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="summary-card">
                <span class="summary-icon summary-icon-alt"><i class="fas fa-qrcode" aria-hidden="true"></i></span>
                <div><div class="summary-label">QR codes ready on this page</div><div class="summary-value">{{ $cards->getCollection()->whereNotNull('qr_code')->whereNotNull('verification_url')->count() }}</div></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="summary-card">
                <span class="summary-icon summary-icon-slate"><i class="fas fa-calendar" aria-hidden="true"></i></span>
                <div><div class="summary-label">Showing page</div><div class="summary-value">{{ $cards->currentPage() }} <small>of {{ $cards->lastPage() }}</small></div></div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0"><i class="fas fa-filter me-2 text-primary" aria-hidden="true"></i>Find cards</h2></div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="searchInput" class="form-label">Search</label>
                    <input type="search" class="form-control" id="searchInput" placeholder="Student name or registration number">
                </div>
                <div class="col-12 col-md-2">
                    <label for="courseFilter" class="form-label">Course</label>
                    <select class="form-select" id="courseFilter">
                        <option value="">All courses</option>
                        @foreach($cards->getCollection()->unique('course')->sortBy('course') as $card)
                            @if($card->course)<option value="{{ $card->course }}">{{ $card->course }}</option>@endif
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="branchFilter" class="form-label">Branch</label>
                    <select class="form-select" id="branchFilter">
                        <option value="">All branches</option>
                        @foreach($cards->getCollection()->unique('branch')->sortBy('branch') as $card)
                            @if($card->branch)<option value="{{ $card->branch }}">{{ $card->branch }}</option>@endif
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="yearFilter" class="form-label">Academic Year</label>
                    <select class="form-select" id="yearFilter">
                        <option value="">All years</option>
                        @foreach($cards->getCollection()->unique('academic_year')->sortBy('academic_year') as $card)
                            @if($card->academic_year)<option value="{{ $card->academic_year }}">{{ $card->academic_year }}</option>@endif
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                        <i class="fas fa-rotate-left me-1" aria-hidden="true"></i>Clear filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-semibold mb-0">Student cards</h2>
            <span class="text-muted small">{{ $cards->count() }} shown / {{ $cards->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="cardsTable">
                <thead class="table-light">
                    <tr><th>Student</th><th>Registration Number</th><th>Course</th><th>Branch</th><th>Academic Year</th><th>Issued</th><th>QR</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($cards as $card)
                        <tr class="card-row"
                            data-name="{{ strtolower($card->student->full_name ?? '') }}"
                            data-reg="{{ strtolower($card->reg_number ?? '') }}"
                            data-course="{{ $card->course ?? '' }}"
                            data-branch="{{ $card->branch ?? '' }}"
                            data-year="{{ $card->academic_year ?? '' }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="student-avatar">{{ strtoupper(substr($card->student->full_name ?? '?', 0, 1)) }}</span>
                                    <span class="fw-semibold">{{ $card->student->full_name ?? 'Unknown student' }}</span>
                                </div>
                            </td>
                            <td class="font-monospace">{{ $card->reg_number ?? '—' }}</td>
                            <td>{{ $card->course ?: '—' }}</td>
                            <td>{{ $card->branch ?: '—' }}</td>
                            <td>{{ $card->academic_year ?: '—' }}</td>
                            <td class="text-nowrap">{{ $card->generated_at ? \Carbon\Carbon::parse($card->generated_at)->format('M d, Y') : '—' }}</td>
                            <td>
                                @if($card->qr_code && $card->verification_url)
                                    <span class="badge rounded-pill text-bg-success">Ready</span>
                                @else
                                    <span class="badge rounded-pill text-bg-secondary">Unavailable</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Student card actions">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#previewCardModal{{ $card->id }}" aria-label="Preview card" title="Preview"><i class="fas fa-eye" aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-outline-primary" onclick="downloadStudentCardImage({{ $card->id }}, this.dataset.studentName, 'front')" data-student-name="{{ $card->student->full_name ?? 'student' }}" aria-label="Download front" title="Download Front"><i class="fas fa-download" aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-outline-primary" onclick="printStudentCard({{ $card->id }}, 'both')" aria-label="Print card" title="Print Both Sides"><i class="fas fa-print" aria-hidden="true"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-id-card fa-2x d-block mb-2" aria-hidden="true"></i><div class="fw-semibold">No student cards yet</div><small>Generate a card from the student record after the card fee is paid.</small></td></tr>
                    @endforelse
                    <tr id="noFilterResults" class="d-none"><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-magnifying-glass fa-2x d-block mb-2" aria-hidden="true"></i>No cards match these filters.</td></tr>
                </tbody>
            </table>
        </div>
        @if($cards->hasPages())
            <div class="card-footer bg-white">{{ $cards->links() }}</div>
        @endif
    </div>
</div>

@foreach($cards as $card)
    <div class="modal fade" id="previewCardModal{{ $card->id }}" tabindex="-1" aria-labelledby="previewCardModalLabel{{ $card->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <div><h2 class="modal-title h5 fw-semibold" id="previewCardModalLabel{{ $card->id }}">Student ID Card - {{ $card->student->full_name ?? 'Unknown' }}</h2><p class="text-muted small mb-0">CR80 Standard: 85.60mm × 53.98mm | 300 DPI</p></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="front-tab-{{ $card->id }}" data-bs-toggle="tab" data-bs-target="#front-{{ $card->id }}" type="button" role="tab" aria-controls="front-{{ $card->id }}" aria-selected="true">Front Side</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="back-tab-{{ $card->id }}" data-bs-toggle="tab" data-bs-target="#back-{{ $card->id }}" type="button" role="tab" aria-controls="back-{{ $card->id }}" aria-selected="false">Back Side</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="both-tab-{{ $card->id }}" data-bs-toggle="tab" data-bs-target="#both-{{ $card->id }}" type="button" role="tab" aria-controls="both-{{ $card->id }}" aria-selected="false">Both Sides</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="front-{{ $card->id }}" role="tabpanel" aria-labelledby="front-tab-{{ $card->id }}">
                            <div class="card-preview-container">
                                @include('students.partials.student-card-side', [
                                    'card' => $card,
                                    'side' => 'front',
                                    'idPrefix' => 'cardFront',
                                    'schoolName' => $schoolName,
                                    'schoolAddress' => $schoolAddress,
                                    'schoolPhone' => $schoolPhone,
                                    'schoolEmail' => $schoolEmail,
                                    'schoolWebsite' => $schoolWebsite,
                                    'logoUrl' => $logoUrl,
                                    'schoolInfo' => $schoolInfo,
                                ])
                            </div>
                        </div>
                        <div class="tab-pane fade" id="back-{{ $card->id }}" role="tabpanel" aria-labelledby="back-tab-{{ $card->id }}">
                            <div class="card-preview-container">
                                @include('students.partials.student-card-side', [
                                    'card' => $card,
                                    'side' => 'back',
                                    'idPrefix' => 'cardBack',
                                    'schoolName' => $schoolName,
                                    'schoolAddress' => $schoolAddress,
                                    'schoolPhone' => $schoolPhone,
                                    'schoolEmail' => $schoolEmail,
                                    'schoolWebsite' => $schoolWebsite,
                                    'logoUrl' => $logoUrl,
                                    'schoolInfo' => $schoolInfo,
                                ])
                            </div>
                        </div>
                        <div class="tab-pane fade" id="both-{{ $card->id }}" role="tabpanel" aria-labelledby="both-tab-{{ $card->id }}">
                            <div class="both-sides-container">
                                <div class="both-side-wrapper">
                                    <div class="side-label">FRONT</div>
                                    @include('students.partials.student-card-side', [
                                        'card' => $card,
                                        'side' => 'front',
                                        'idPrefix' => 'previewFront',
                                        'schoolName' => $schoolName,
                                        'schoolAddress' => $schoolAddress,
                                        'schoolPhone' => $schoolPhone,
                                        'schoolEmail' => $schoolEmail,
                                        'schoolWebsite' => $schoolWebsite,
                                        'logoUrl' => $logoUrl,
                                        'schoolInfo' => $schoolInfo,
                                    ])
                                </div>
                                <div class="both-side-wrapper">
                                    <div class="side-label">BACK</div>
                                    @include('students.partials.student-card-side', [
                                        'card' => $card,
                                        'side' => 'back',
                                        'idPrefix' => 'previewBack',
                                        'schoolName' => $schoolName,
                                        'schoolAddress' => $schoolAddress,
                                        'schoolPhone' => $schoolPhone,
                                        'schoolEmail' => $schoolEmail,
                                        'schoolWebsite' => $schoolWebsite,
                                        'logoUrl' => $logoUrl,
                                        'schoolInfo' => $schoolInfo,
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($card->verification_url)
                        <div class="verification-link mt-3">
                            <span class="small text-muted">Verification link</span>
                            <a href="{{ $card->verification_url }}" target="_blank" rel="noopener noreferrer" class="text-break">{{ $card->verification_url }}</a>
                        </div>
                    @else
                        <div class="alert alert-warning mt-3 mb-0">This card does not have a verification link yet. Reopen or regenerate the card to refresh its QR code.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <div class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="printStudentCard({{ $card->id }}, 'front')"><i class="fas fa-print me-2" aria-hidden="true"></i>Print Front</button>
                        <button type="button" class="btn btn-outline-primary" onclick="printStudentCard({{ $card->id }}, 'back')"><i class="fas fa-print me-2" aria-hidden="true"></i>Print Back</button>
                        <button type="button" class="btn btn-primary" onclick="printStudentCard({{ $card->id }}, 'both')"><i class="fas fa-print me-2" aria-hidden="true"></i>Print Both</button>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-success" onclick="downloadStudentCardImage({{ $card->id }}, this.dataset.studentName, 'front')" data-student-name="{{ $card->student->full_name ?? 'student' }}"><i class="fas fa-download me-2" aria-hidden="true"></i>Front PNG</button>
                        <button type="button" class="btn btn-outline-success" onclick="downloadStudentCardImage({{ $card->id }}, this.dataset.studentName, 'back')" data-student-name="{{ $card->student->full_name ?? 'student' }}"><i class="fas fa-download me-2" aria-hidden="true"></i>Back PNG</button>
                        <button type="button" class="btn btn-success" onclick="downloadStudentCardPDF({{ $card->id }}, this.dataset.studentName)" data-student-name="{{ $card->student->full_name ?? 'student' }}"><i class="fas fa-file-pdf me-2" aria-hidden="true"></i>Both PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .student-cards-page {
        --cards-teal: #0f766e;
        --cards-ink: #173247;
        --cards-muted: #64748b;
        --cards-border: #e2e8f0;
        font-family: 'Poppins', sans-serif;
        color: var(--cards-ink);
    }
    .student-cards-page .eyebrow { color: var(--cards-teal); font-size: .75rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
    .student-cards-page .page-title { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; }
    .student-cards-page .page-subtitle, .student-cards-page .summary-label { color: var(--cards-muted); }
    .student-cards-page .card { border-radius: 14px; border-color: var(--cards-border); box-shadow: 0 5px 18px rgba(15, 23, 42, .05) !important; }
    .student-cards-page .card-header { border-color: var(--cards-border); }
    .student-cards-page .btn-primary { background: var(--cards-teal); border-color: var(--cards-teal); }
    .student-cards-page .btn-primary:hover { background: #115e59; border-color: #115e59; }
    .student-cards-page .btn-outline-primary { color: var(--cards-teal); border-color: var(--cards-teal); }
    .student-cards-page .btn-outline-primary:hover { color: #fff; background: var(--cards-teal); }
    .student-cards-page .form-control:focus, .student-cards-page .form-select:focus { border-color: #5aa69f; box-shadow: 0 0 0 .2rem rgba(15, 118, 110, .15); }
    .student-cards-page .summary-card { display: flex; align-items: center; gap: 14px; height: 100%; padding: 18px; border: 1px solid var(--cards-border); border-radius: 14px; background: #fff; }
    .student-cards-page .summary-icon { display: grid; place-items: center; width: 44px; height: 44px; border-radius: 12px; color: var(--cards-teal); background: #e9f5f3; font-size: 1.15rem; }
    .student-cards-page .summary-icon-alt { color: #27669a; background: #edf4fa; }
    .student-cards-page .summary-icon-slate { color: #475569; background: #f1f5f9; }
    .student-cards-page .summary-value { color: var(--cards-ink); font-size: 1.35rem; font-weight: 700; }
    .student-cards-page .summary-value small { color: var(--cards-muted); font-size: .8rem; font-weight: 500; }
    .student-cards-page .student-avatar { display: grid; place-items: center; width: 34px; height: 34px; flex: 0 0 34px; border-radius: 50%; color: #fff; background: var(--cards-teal); font-size: .9rem; font-weight: 700; }
    .student-cards-page .table thead th { color: #475569; font-size: .75rem; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }

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

    /* Standard CR80 front and back layout */
    .cr80-card {
        width: 505px;
        max-width: 100%;
        height: 318px;
        aspect-ratio: 680 / 428;
        border-radius: 12px;
        font-family: 'Segoe UI', Roboto, Arial, sans-serif;
        color: #0f172a;
    }
    .cr80-front { display: flex; flex-direction: column; }
    .cr80-front .front-header {
        height: 58px;
        min-height: 58px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(90deg,#1d4ed8 0%,#2563eb 65%,#3b82f6 100%);
        color: #fff;
    }
    .cr80-front .school-title { font-size: 15px; font-weight: 800; }
    .cr80-front .school-address { margin-top: 2px; font-size: 9px; opacity: .92; }
    .cr80-front .logo-badge { width: 40px; height: 40px; border-radius: 8px; background: #fff; padding: 3px; display: flex; align-items: center; justify-content: center; }
    .cr80-front .logo-badge img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .cr80-front .front-body { flex: 1; min-height: 0; padding: 12px 16px 10px; display: grid; grid-template-columns: 125px 1fr; gap: 15px; }
    .cr80-front .photo-column { display: flex; flex-direction: column; gap: 6px; }
    .cr80-front .photo-frame { width: 125px; height: 152px; border-radius: 9px; border: 2px solid #dbeafe; overflow: hidden; background: #f8fafc; display: flex; align-items: center; justify-content: center; }
    .cr80-front .photo-frame img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
    .cr80-front .photo-placeholder { font-size: 28px; color: #94a3b8; font-weight: 800; }
    .cr80-front .id-badge-pill { text-align: center; color: #1d4ed8; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 5px; padding: 4px; font-size: 8px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; }
    .cr80-front .details-column { min-width: 0; display: flex; flex-direction: column; justify-content: space-between; }
    .cr80-front .student-identity { border-bottom: 2px solid #2563eb; padding-bottom: 5px; margin-bottom: 7px; }
    .cr80-front .student-name { font-size: 16px; line-height: 1.12; font-weight: 800; text-transform: uppercase; }
    .cr80-front .student-role { margin-top: 2px; color: #2563eb; font-size: 8px; font-weight: 800; letter-spacing: .8px; text-transform: uppercase; }
    .cr80-front .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px 10px; }
    .cr80-front .meta-item { display: flex; flex-direction: column; min-width: 0; }
    .cr80-front .meta-label { color: #64748b; font-size: 7px; font-weight: 800; text-transform: uppercase; letter-spacing: .4px; }
    .cr80-front .meta-value { margin-top: 2px; color: #0f172a; font-size: 9px; font-weight: 800; overflow-wrap: anywhere; }
    .cr80-front .meta-value.mono { color: #1e3a8a; font-family: 'Courier New', monospace; font-size: 9px; }
    .cr80-front .front-contact-strip { margin-top: 6px; background: #f8fafc; border-left: 3px solid #2563eb; border-radius: 5px; padding: 6px 8px; display: flex; justify-content: space-between; gap: 8px; font-size: 8px; }
    .cr80-back { display: flex; flex-direction: column; padding: 0; gap: 0; }
    .cr80-back .back-header { height: 50px; padding: 10px 16px; background: #1e293b; color: #fff; display: flex; align-items: center; justify-content: space-between; }
    .cr80-back .back-header .title { font-size: 11px; font-weight: 800; letter-spacing: .3px; }
    .cr80-back .back-header .sub { color: #cbd5e1; font-size: 8px; }
    .cr80-back .back-body { flex: 1; min-height: 0; padding: 13px 16px 10px; display: flex; flex-direction: column; justify-content: space-between; }
    .cr80-back .notice-box { height: 172px; background: #fdf4ff; border: 1px solid #f0abfc; border-left: 4px solid #a855f7; border-radius: 9px; padding: 10px; display: grid; grid-template-columns: 1fr 84px; gap: 12px; align-items: center; }
    .cr80-back .notice-title { margin-bottom: 5px; color: #6b21a8; font-size: 10px; font-weight: 800; }
    .cr80-back .notice-text { color: #334155; font-size: 8px; line-height: 1.45; }
    .cr80-back .notice-text p + p { margin-top: 5px; }
    .cr80-back .qr-box { width: 84px; height: 84px; padding: 5px; border: 1px solid #e9d5ff; border-radius: 7px; background: #fff; text-align: center; }
    .cr80-back .qr-caption { color: #6b21a8; font-size: 6px; font-weight: 800; margin-bottom: 3px; text-transform: uppercase; }
    .cr80-back .qr-box svg { width: 66px; height: 66px; display: block; margin: 0 auto; }
    .cr80-back .qr-box img { width: 66px; height: 66px; display: block; margin: 0 auto; }
    .cr80-back .back-footer { display: grid; grid-template-columns: 1.3fr 1fr; gap: 12px; align-items: end; min-height: 58px; border-top: 1px dashed #cbd5e1; }
    .cr80-back .office-info { font-size: 8px; line-height: 1.4; color: #475569; }
    .cr80-back .signature-block { text-align: right; }
    .cr80-back .signature-line { width: 125px; height: 20px; margin: 0 0 3px auto; border-bottom: 1.5px solid #334155; }
    .cr80-back .signature-image { width: 125px; height: 30px; object-fit: contain; object-position: right bottom; display: block; margin-left: auto; }
    .cr80-back .signature-label { font-size: 8px; font-weight: 800; }
    .cr80-back .signature-role { font-size: 7px; color: #64748b; }

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

    @media (max-width: 575.98px) {
        .student-cards-page .table { min-width: 980px; }
        .cr80-card { border-radius: 6px; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('searchInput');
    const course = document.getElementById('courseFilter');
    const branch = document.getElementById('branchFilter');
    const year = document.getElementById('yearFilter');
    const rows = Array.from(document.querySelectorAll('#cardsTable .card-row'));
    const noResults = document.getElementById('noFilterResults');

    function filterRows() {
        const term = search.value.trim().toLowerCase();
        let visibleCount = 0;
        rows.forEach(function (row) {
            const visible = (!term || row.dataset.name.includes(term) || row.dataset.reg.includes(term))
                && (!course.value || row.dataset.course === course.value)
                && (!branch.value || row.dataset.branch === branch.value)
                && (!year.value || row.dataset.year === year.value);
            row.classList.toggle('d-none', !visible);
            if (visible) visibleCount++;
        });
        noResults.classList.toggle('d-none', visibleCount > 0 || rows.length === 0);
    }

    search.addEventListener('input', filterRows);
    [course, branch, year].forEach(function (select) {
        select.addEventListener('change', filterRows);
    });
    document.getElementById('clearFilters').addEventListener('click', function () {
        search.value = '';
        course.value = '';
        branch.value = '';
        year.value = '';
        filterRows();
    });

    const previewId = new URLSearchParams(window.location.search).get('preview');
    const previewModal = previewId && document.getElementById('previewCardModal' + previewId);
    if (previewModal) {
        bootstrap.Modal.getOrCreateInstance(previewModal).show();
        const cleanUrl = new URL(window.location.href);
        cleanUrl.searchParams.delete('preview');
        window.history.replaceState({}, '', cleanUrl);
    }
});

function printStudentCard(cardId, side) {
    const frontElement = document.getElementById('cardFront' + cardId);
    const backElement = document.getElementById('cardBack' + cardId);

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
            .cr80-front .front-header { height: 10mm; min-height: 10mm; padding: 2mm 3mm; background: linear-gradient(90deg,#1d4ed8,#2563eb,#3b82f6); color: #fff; }
            .cr80-front .school-title { font-size: 7pt; font-weight: bold; }
            .cr80-front .school-address { font-size: 4.5pt; }
            .cr80-front .logo-badge { width: 8mm; height: 8mm; background: #fff; border-radius: 1.5mm; padding: 1mm; }
            .cr80-front .logo-badge img { max-width: 100%; max-height: 100%; object-fit: contain; }
            .cr80-front .front-body { padding: 2mm 3mm 1.5mm; grid-template-columns: 25mm 1fr; gap: 3mm; }
            .cr80-front .photo-frame { width: 25mm; height: 30mm; border: 0.3mm solid #dbeafe; border-radius: 1.5mm; overflow: hidden; }
            .cr80-front .photo-frame img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
            .cr80-front .id-badge-pill { margin-top: 1mm; text-align: center; font-size: 3.5pt; color: #1d4ed8; background: #eff6ff; border: 0.2mm solid #bfdbfe; border-radius: 1mm; padding: 0.7mm; }
            .cr80-front .student-name { font-size: 8pt; font-weight: bold; border-bottom: 0.3mm solid #2563eb; padding-bottom: 1mm; }
            .cr80-front .student-role { font-size: 4pt; color: #2563eb; margin-top: 1mm; }
            .cr80-front .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5mm 2mm; margin-top: 1.5mm; }
            .cr80-front .meta-label { display: block; font-size: 3.5pt; color: #64748b; font-weight: bold; text-transform: uppercase; }
            .cr80-front .meta-value { display: block; font-size: 4.5pt; font-weight: bold; margin-top: 0.5mm; }
            .cr80-front .front-contact-strip { margin-top: 1.5mm; background: #f8fafc; border-left: 0.6mm solid #2563eb; font-size: 3.5pt; padding: 1mm 1.5mm; display: flex; justify-content: space-between; }
            .cr80-back .back-header { height: 9mm; padding: 2mm 3mm; background: #1e293b; color: #fff; display: flex; align-items: center; justify-content: space-between; }
            .cr80-back .back-header .title { font-size: 5.5pt; font-weight: bold; }
            .cr80-back .back-header .sub { font-size: 4pt; color: #cbd5e1; }
            .cr80-back .back-body { padding: 2mm 3mm 1.5mm; }
            .cr80-back .notice-box { height: 28mm; padding: 1.5mm; display: grid; grid-template-columns: 1fr 16mm; gap: 2mm; background: #fdf4ff; border: 0.2mm solid #f0abfc; border-left: 0.8mm solid #a855f7; border-radius: 1.5mm; }
            .cr80-back .notice-title { font-size: 5pt; color: #6b21a8; font-weight: bold; }
            .cr80-back .notice-text { font-size: 3.5pt; line-height: 1.35; }
            .cr80-back .qr-box { width: 16mm; height: 16mm; padding: 1mm; background: #fff; border: 0.2mm solid #e9d5ff; text-align: center; }
            .cr80-back .qr-caption { font-size: 3pt; color: #6b21a8; }
            .cr80-back .qr-box svg { width: 13mm; height: 13mm; display: block; }
            .cr80-back .qr-box img { width: 13mm; height: 13mm; display: block; margin: 0 auto; }
            .cr80-back .back-footer { min-height: 9mm; display: grid; grid-template-columns: 1.3fr 1fr; border-top: 0.2mm dashed #cbd5e1; }
            .cr80-back .office-info { font-size: 3.5pt; line-height: 1.35; }
            .cr80-back .signature-block { text-align: right; }
            .cr80-back .signature-line { width: 24mm; height: 4mm; border-bottom: 0.3mm solid #334155; margin-left: auto; }
            .cr80-back .signature-image { width: 24mm; height: 5mm; object-fit: contain; object-position: right bottom; }
            .cr80-back .signature-label { font-size: 3.5pt; font-weight: bold; }
            .cr80-back .signature-role { font-size: 3pt; }
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

function downloadStudentCardImage(cardId, studentName, side) {
    const elementId = side === 'front' ? 'cardFront' + cardId : 'cardBack' + cardId;
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

function downloadStudentCardPDF(cardId, studentName) {
    const frontElement = document.getElementById('cardFront' + cardId);
    const backElement = document.getElementById('cardBack' + cardId);

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
</script>
@endpush
