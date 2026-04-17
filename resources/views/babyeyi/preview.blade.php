@extends('staff.partials.sidebar_sec')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h1>Preview Babyeyi Document</h1>
            <small class="text-muted">Reference: {{ $babyeyi->reference_number }}</small>
        </div>
    </div>

    <!-- Preview Alert -->
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-eye"></i> <strong>Preview Mode:</strong> This is how your babyeyi will look when published. Review carefully before confirming.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="row">
        <!-- Preview Content -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body bg-white p-5" style="min-height: 800px;">
                    <!-- School Header -->
                    @if ($schoolInfo)
                        <div style="text-align: center; border-bottom: 3px solid #333; padding-bottom: 25px; margin-bottom: 35px;">
                            @if ($schoolInfo->logo_url)
                                <img src="{{ $schoolInfo->logo_url }}" style="max-height: 90px; margin-bottom: 15px;" alt="School Logo">
                            @else
                                <div style="width: 90px; height: 90px; margin: 0 auto 15px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 40px;">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            @endif
                            <h2 style="margin: 10px 0; color: #1a1a1a; font-weight: bold;">{{ $schoolInfo->school_name ?? 'Excellent Servant Academy' }}</h2>
                            <p style="margin: 5px 0; color: #666; font-size: 15px;">{{ $schoolInfo->address ?? 'Kigali, Rwanda' }}</p>
                            @if ($schoolInfo->phone)
                                <p style="margin: 3px 0; color: #666; font-size: 13px;">Tel: {{ $schoolInfo->phone }}</p>
                            @endif
                            @if ($schoolInfo->email)
                                <p style="margin: 3px 0; color: #666; font-size: 13px;">Email: {{ $schoolInfo->email }}</p>
                            @endif
                        </div>
                    @else
                        <div style="text-align: center; border-bottom: 3px solid #333; padding-bottom: 25px; margin-bottom: 35px;">
                            <h2 style="margin: 10px 0; color: #1a1a1a; font-weight: bold;">Excellent Servant Academy</h2>
                            <p style="margin: 5px 0; color: #666; font-size: 15px;">Kigali, Rwanda</p>
                        </div>
                    @endif

                    <!-- Document Title -->
                    @if ($babyeyi->title)
                        <h3 style="text-align: center; margin: 25px 0; color: #1a1a1a; font-weight: bold;">{{ $babyeyi->title }}</h3>
                    @else
                        <h3 style="text-align: center; margin: 25px 0; color: #1a1a1a; font-weight: bold;">BABYEYI - TERM REGISTRATION DOCUMENT</h3>
                    @endif

                    <!-- Document Content -->
                    <div class="document-content" style="font-family: 'Georgia', serif; line-height: 1.8; color: #333; font-size: 15px;">
                        {!! $babyeyi->content !!}
                    </div>

                    <!-- Footer Section with Signature & Stamp -->
                    <div style="margin-top: 80px; padding-top: 40px; border-top: 2px solid #333; display: flex; justify-content: space-between; align-items: flex-start;">
                        <!-- Left: School Stamp & Principal Signature -->
                        <div style="width: 40%; text-align: center;">
                            <div style="margin-bottom: 40px; min-height: 80px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                @if ($schoolInfo && $schoolInfo->stamp_signature_url)
                                    <img src="{{ $schoolInfo->stamp_signature_url }}" style="max-height: 75px; max-width: 110px; object-fit: contain;" alt="School Stamp">
                                @elseif ($schoolInfo && $schoolInfo->principal_signature_url)
                                    <img src="{{ $schoolInfo->principal_signature_url }}" style="max-height: 75px; max-width: 110px; object-fit: contain;" alt="Principal Signature">
                                @else
                                    <p style="margin: 0; color: #ccc; font-size: 11px; line-height: 1.3;"><strong>School Stamp</strong><br>& Principal Signature</p>
                                @endif
                            </div>
                            <p style="font-weight: bold; margin: 8px 0; font-size: 13px;">Head Teacher/Principal</p>
                            <p style="font-size: 11px; color: #666; margin: 3px 0;">{{ $schoolInfo->principal_name ?? '____________________' }}</p>
                            <p style="font-size: 11px; color: #666; margin: 5px 0;">Date: {{ $babyeyi->created_at->format('M d, Y') }}</p>
                        </div>

                        <!-- Right: Accountant Signature -->
                        <div style="width: 50%; text-align: center; padding-left: 30px;">
                            <div style="margin-bottom: 40px; border-bottom: 1px solid #333;"></div>
                            <p style="font-weight: bold; margin: 8px 0; font-size: 13px;">Accountant/Office Manager</p>
                            <p style="font-size: 11px; color: #666; margin: 3px 0;">_____________________</p>
                            <p style="font-size: 11px; color: #666; margin: 5px 0;">Date: {{ $babyeyi->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Review & Actions -->
        <div class="col-lg-4">
            <!-- Document Info -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Document Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted"><strong>Reference Number:</strong></small><br>
                        <code>{{ $babyeyi->reference_number }}</code>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <small class="text-muted"><strong>Academic Year:</strong></small><br>
                        {{ $babyeyi->academicYear->year ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <small class="text-muted"><strong>Term:</strong></small><br>
                        {{ $babyeyi->term->name ?? 'N/A' }}<br>
                        <small class="text-muted">
                            {{ $babyeyi->term->start_date->format('M d, Y') ?? '' }} - {{ $babyeyi->term->end_date->format('M d, Y') ?? '' }}
                        </small>
                    </div>
                    @if ($babyeyi->course)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Course:</strong></small><br>
                            {{ $babyeyi->course->name }}
                        </div>
                    @endif
                    @if ($babyeyi->course_type)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Course Type:</strong></small><br>
                            {{ $babyeyi->course_type }}
                        </div>
                    @endif
                    @if ($babyeyi->trip_known && $babyeyi->trip_place)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Trip Destination:</strong></small><br>
                            {{ $babyeyi->trip_place }}
                        </div>
                    @endif
                    @if ($babyeyi->trip_fees)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Trip Fees:</strong></small><br>
                            RWF {{ number_format($babyeyi->trip_fees, 2) }}
                        </div>
                    @endif
                    <hr>
                    <div class="mb-3">
                        <small class="text-muted"><strong>Status:</strong></small><br>
                        @if ($babyeyi->status === 'draft')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @elseif ($babyeyi->status === 'preview')
                            <span class="badge bg-info">Preview</span>
                        @elseif ($babyeyi->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Review Checklist -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Review Checklist</h5>
                </div>
                <div class="card-body">
                    <small>
                        <div class="form-check mb-2">
                            <input class="form-check-input review-check" type="checkbox" id="check1">
                            <label class="form-check-label" for="check1">
                                Content is correct and complete
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input review-check" type="checkbox" id="check2">
                            <label class="form-check-label" for="check2">
                                All formatting is correct
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input review-check" type="checkbox" id="check3">
                            <label class="form-check-label" for="check3">
                                Trip information is accurate
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input review-check" type="checkbox" id="check4">
                            <label class="form-check-label" for="check4">
                                Fee information is complete
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input review-check" type="checkbox" id="check5">
                            <label class="form-check-label" for="check5">
                                School information is correct
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input review-check" type="checkbox" id="check6">
                            <label class="form-check-label" for="check6">
                                Ready for publication
                            </label>
                        </div>
                    </small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2">
                <form action="{{ route('babyeyi.publish', $babyeyi) }}" method="POST" id="publishForm">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg" id="publishBtn" disabled>
                        <i class="fas fa-check"></i> Publish Babyeyi
                    </button>
                </form>
                <a href="{{ route('babyeyi.edit', $babyeyi) }}" class="btn btn-warning btn-lg">
                    <i class="fas fa-edit"></i> Edit Again
                </a>
                <a href="{{ route('babyeyi.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.document-content {
    font-family: 'Georgia', serif;
    line-height: 1.8;
    color: #333;
}

.document-content h1, 
.document-content h2, 
.document-content h3 {
    margin-top: 20px;
    margin-bottom: 15px;
    color: #1a1a1a;
    font-weight: bold;
}

.document-content p {
    margin-bottom: 12px;
    text-align: justify;
}

.document-content ul,
.document-content ol {
    margin: 15px 0;
    padding-left: 30px;
}

.document-content li {
    margin-bottom: 8px;
    line-height: 1.6;
}

.document-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.document-content table td,
.document-content table th {
    border: 1px solid #ddd;
    padding: 10px;
}

.document-content table th {
    background: #f5f5f5;
    font-weight: bold;
}

@media print {
    .container-fluid > .row:first-child,
    .alert,
    .col-lg-4,
    .btn,
    button {
        display: none !important;
    }
    
    .col-lg-8 {
        max-width: 100% !important;
        margin-left: 0 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.review-check');
    const publishBtn = document.getElementById('publishBtn');
    
    function updatePublishButton() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        publishBtn.disabled = !allChecked;
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePublishButton);
    });
    
    // Print functionality
    document.addEventListener('keydown', function(event) {
        if ((event.ctrlKey || event.metaKey) && event.key === 'p') {
            event.preventDefault();
            window.print();
        }
    });
});
</script>
@endsection
