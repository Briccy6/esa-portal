@extends('staff.partials.sidebar_sec')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h1>Babyeyi Document</h1>
            <small class="text-muted">Reference: {{ $babyeyi->reference_number }}</small>
        </div>
        <div class="col text-right">
            @if ($babyeyi->status === 'published')
                <a href="{{ route('babyeyi.download', $babyeyi) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-3">
        @if ($babyeyi->status === 'draft')
            <span class="badge badge-warning" style="font-size: 14px;">Draft</span>
        @elseif ($babyeyi->status === 'preview')
            <span class="badge badge-info" style="font-size: 14px;">Preview</span>
        @else
            <span class="badge badge-success" style="font-size: 14px;">Published</span>
        @endif
    </div>

    <div class="row">
        <!-- Document Content -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body bg-white" style="min-height: 600px;">
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
                    @endif

                    <!-- Document Title -->
                    @if ($babyeyi->title)
                        <h3 style="text-align: center; margin: 20px 0; color: #1a1a1a; font-weight: bold;">{{ $babyeyi->title }}</h3>
                    @else
                        <h3 style="text-align: center; margin: 20px 0; color: #1a1a1a; font-weight: bold;">BABYEYI - TERM REGISTRATION DOCUMENT</h3>
                    @endif

                    <!-- Document Content -->
                    <div class="document-content" style="font-family: 'Georgia', serif; line-height: 1.8; color: #333; font-size: 15px;">
                        {!! $babyeyi->content !!}
                    </div>

                    <!-- Footer Section with Signature & Stamp -->
                    <div style="margin-top: 80px; padding-top: 40px; border-top: 2px solid #333; display: flex; justify-content: space-between; align-items: flex-end;">
                        <!-- Left: School Stamp & Principal Signature -->
                        <div style="width: 30%; text-align: center;">
                            <div style="margin-bottom: 60px; min-height: 100px; border: 2px solid #333; display: flex; align-items: center; justify-content: center; background: #fff; border-radius: 4px; overflow: hidden;">
                                @if ($schoolInfo && $schoolInfo->stamp_signature_url)
                                    <img src="{{ $schoolInfo->stamp_signature_url }}" style="max-height: 95px; max-width: 120px; object-fit: contain;" alt="School Stamp">
                                @elseif ($schoolInfo && $schoolInfo->principal_signature_url)
                                    <img src="{{ $schoolInfo->principal_signature_url }}" style="max-height: 95px; max-width: 120px; object-fit: contain;" alt="Principal Signature">
                                @else
                                    <p style="margin: 0; color: #ccc; font-size: 11px; line-height: 1.3;"><strong>School Stamp</strong><br>& Principal Signature</p>
                                @endif
                            </div>
                            <p style="font-weight: bold; margin: 8px 0; font-size: 13px;">Head Teacher/Principal</p>
                            <p style="font-size: 11px; color: #666; margin: 3px 0;">{{ $schoolInfo->principal_name ?? '____________________' }}</p>
                            <p style="font-size: 11px; color: #666; margin: 5px 0;">Date: ____________________</p>
                        </div>

                        <!-- Middle: Document Certification -->
                        <div style="width: 35%; text-align: center;">
                            <div style="margin-bottom: 60px; min-height: 100px; border: 2px solid #333; display: flex; align-items: center; justify-content: center; background: #fff; border-radius: 4px;">
                                <p style="margin: 0; color: #666; font-size: 11px; line-height: 1.4; font-style: italic;">Certified by<br>School Administration</p>
                            </div>
                            <p style="font-weight: bold; margin: 8px 0; font-size: 13px;">Document Verified</p>
                            <p style="font-size: 11px; color: #666; margin: 3px 0;">____________________</p>
                            <p style="font-size: 11px; color: #666; margin: 5px 0;">Date: ____________________</p>
                        </div>

                        <!-- Right: Accountant Signature (No Storage) -->
                        <div style="width: 30%; text-align: center;">
                            <div style="margin-bottom: 60px; min-height: 100px; border: 2px dashed #999; display: flex; align-items: center; justify-content: center; background: #f5f5f5; border-radius: 4px;">
                                <p style="margin: 0; color: #999; font-size: 11px; line-height: 1.3;"><strong>Accountant</strong><br>Signature Area</p>
                            </div>
                            <p style="font-weight: bold; margin: 8px 0; font-size: 13px;">Accountant/Office Manager</p>
                            <p style="font-size: 11px; color: #666; margin: 3px 0;">____________________</p>
                            <p style="font-size: 11px; color: #666; margin: 5px 0;">Date: ____________________</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Document Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Document Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted"><strong>Reference Number:</strong></small><br>
                        <code>{{ $babyeyi->reference_number }}</code>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted"><strong>Status:</strong></small><br>
                        @if ($babyeyi->status === 'draft')
                            <span class="badge badge-warning">Draft</span>
                        @elseif ($babyeyi->status === 'preview')
                            <span class="badge badge-info">Preview</span>
                        @elseif ($babyeyi->status === 'published')
                            <span class="badge badge-success">Published</span>
                        @endif
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
                            Start: {{ $babyeyi->term->start_date->format('M d, Y') }}<br>
                            End: {{ $babyeyi->term->end_date->format('M d, Y') }}
                        </small>
                    </div>
                    @if ($babyeyi->course)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Course:</strong></small><br>
                            <strong>{{ $babyeyi->course->name }}</strong>
                        </div>
                    @endif
                    @if ($babyeyi->course_type)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Course Type:</strong></small><br>
                            {{ $babyeyi->course_type }}
                        </div>
                    @endif
                    <hr>
                    @if ($babyeyi->trip_known && $babyeyi->trip_place)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Trip Destination:</strong></small><br>
                            <strong>{{ $babyeyi->trip_place }}</strong>
                        </div>
                    @elseif ($babyeyi->trip_fees)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Trip Information:</strong></small><br>
                            <em>Place to be communicated</em>
                        </div>
                    @endif
                    @if ($babyeyi->trip_fees)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Trip Fees:</strong></small><br>
                            <strong>RWF {{ number_format($babyeyi->trip_fees, 2) }}</strong>
                        </div>
                    @endif
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted"><strong>Created by:</strong></small><br>
                        {{ $babyeyi->creator->name ?? 'System' }}<br>
                        <small class="text-muted">{{ $babyeyi->created_at->format('M d, Y H:i') }}</small>
                    </div>
                    @if ($babyeyi->editor)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Edited by:</strong></small><br>
                            {{ $babyeyi->editor->name }}<br>
                            <small class="text-muted">{{ $babyeyi->updated_at->format('M d, Y H:i') }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2">
                @if ($babyeyi->status === 'published')
                    <a href="{{ route('babyeyi.download', $babyeyi) }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                @endif
                <a href="{{ route('babyeyi.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.document-content {
    font-family: 'Calibri', 'Arial', sans-serif;
    line-height: 1.7;
    color: #333;
    font-size: 14px;
}

.document-content h1 {
    font-size: 24px;
    margin: 20px 0 10px 0;
    color: #1a1a1a;
}

.document-content h2 {
    font-size: 18px;
    margin: 15px 0 8px 0;
    color: #1a1a1a;
}

.document-content h3 {
    font-size: 16px;
    margin: 12px 0 6px 0;
    color: #333;
}

.document-content p {
    margin-bottom: 8px;
}

.document-content ul,
.document-content ol {
    margin-left: 30px;
    margin-bottom: 10px;
}

.document-content li {
    margin-bottom: 5px;
}

.document-content table {
    margin: 15px 0;
    width: 100%;
    border-collapse: collapse;
}

.document-content table td,
.document-content table th {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
}

.document-content table th {
    background-color: #f5f5f5;
    font-weight: bold;
}
</style>
@endsection
