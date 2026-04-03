@extends('layouts.app')

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
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="row">
        <!-- Preview Content -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body bg-white" style="min-height: 600px;">
                    <!-- School Header (will be in PDF) -->
                    @if ($schoolInfo)
                        <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px;">
                            @if ($schoolInfo->logo_path)
                                <img src="{{ asset('storage/' . $schoolInfo->logo_path) }}" style="max-height: 60px; margin-bottom: 10px;" alt="School Logo">
                            @endif
                            <h3 style="margin: 5px 0;">{{ $schoolInfo->name ?? 'School Name' }}</h3>
                            <p style="margin: 2px 0; color: #666;">{{ $schoolInfo->location ?? '' }}</p>
                            @if ($schoolInfo->phone)
                                <p style="margin: 2px 0; color: #666;">Tel: {{ $schoolInfo->phone }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Document Content -->
                    <div class="document-content">
                        {!! $babyeyi->content !!}
                    </div>

                    <!-- Footer Space (for signature in PDF) -->
                    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
                        <div style="display: inline-block; width: 30%;">
                            <p style="margin-bottom: 40px;">_________________</p>
                            <p style="font-size: 14px;"><strong>Head Teacher</strong></p>
                            <p style="font-size: 12px; color: #666;">Signature & Stamp</p>
                        </div>
                        <div style="display: inline-block; width: 30%; margin-left: 10%;">
                            <p style="margin-bottom: 40px;">_________________</p>
                            <p style="font-size: 14px;"><strong>Date</strong></p>
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
                            {{ $babyeyi->term->start_date->format('M d, Y') }} - {{ $babyeyi->term->end_date->format('M d, Y') }}
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
                    <div class="mb-2">
                        <small class="text-muted"><strong>Created by:</strong></small><br>
                        {{ $babyeyi->creator->name ?? 'System' }}
                    </div>
                    @if ($babyeyi->editor)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Edited by:</strong></small><br>
                            {{ $babyeyi->editor->name }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <small class="text-muted"><strong>Status:</strong></small><br>
                        <span class="badge badge-warning">{{ ucfirst($babyeyi->status) }}</span>
                    </div>
                </div>
            </div>

            <!-- Review Checklist -->
            <div class="card shadow mb-4 bg-light">
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
                <a href="{{ route('babyeyi.edit', $babyeyi) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Again
                </a>
                <a href="{{ route('babyeyi.index') }}" class="btn btn-light border">
                    <i class="fas fa-times"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.document-content {
    font-family: 'Georgia', serif;
    line-height: 1.6;
    color: #333;
}

.document-content h1, 
.document-content h2, 
.document-content h3 {
    margin-top: 15px;
    margin-bottom: 10px;
    color: #1a1a1a;
}

.document-content p {
    margin-bottom: 10px;
}

.document-content ul,
.document-content ol {
    margin-left: 20px;
    margin-bottom: 10px;
}

.document-content table {
    margin: 15px 0;
    border-collapse: collapse;
}

.document-content table td,
.document-content table th {
    border: 1px solid #999;
    padding: 8px;
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
});
</script>
@endsection
