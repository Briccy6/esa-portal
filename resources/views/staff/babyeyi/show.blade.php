@extends('staff.partials.sidebar_sec')

@section('content')
<div class="page-content">
    <!-- Page Header with Actions -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-eye text-primary"></i> Preview Babyeyi Document
            </h1>
            <p class="text-muted mb-0">Reference: <code>{{ $babyeyi->reference_number }}</code></p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('babyeyi.edit', $babyeyi) }}" class="btn btn-outline-secondary" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if ($babyeyi->status !== 'published')
                    <form action="{{ route('babyeyi.publish', $babyeyi) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-success" title="Publish">
                            <i class="fas fa-globe"></i> Publish
                        </button>
                    </form>
                @else
                    <a href="{{ route('babyeyi.download', $babyeyi) }}" class="btn btn-outline-primary" title="Download PDF">
                        <i class="fas fa-download"></i> Download
                    </a>
                @endif
                <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-light" title="Back">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="row mb-3">
        <div class="col-md-12">
            @if ($babyeyi->status === 'draft')
                <span class="badge bg-warning text-dark">Draft</span>
            @elseif ($babyeyi->status === 'preview')
                <span class="badge bg-info">Preview</span>
            @else
                <span class="badge bg-success">Published</span>
            @endif
        </div>
    </div>

    <!-- Document Preview Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body bg-white p-5" style="min-height: 600px;">
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
                            <p style="font-size: 11px; color: #666; margin: 3px 0;">____________________</p>
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
                            <p style="font-weight: bold; margin: 8px 0; font-size: 13px;">Accountant</p>
                            <p style="font-size: 11px; color: #666; margin: 3px 0;">____________________</p>
                            <p style="font-size: 11px; color: #666; margin: 5px 0;">Date: ____________________</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Information Footer -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Document Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted"><strong>Academic Year:</strong></small><br>
                            {{ $babyeyi->academicYear->year ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted"><strong>Term:</strong></small><br>
                            {{ $babyeyi->term->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted"><strong>Created:</strong></small><br>
                            {{ $babyeyi->created_at->format('M d, Y H:i') }} by {{ $babyeyi->creator->name ?? 'System' }}
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted"><strong>Last Modified:</strong></small><br>
                            {{ $babyeyi->updated_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
