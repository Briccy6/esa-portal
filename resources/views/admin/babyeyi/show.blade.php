@extends('admin.layout')

@section('title', 'View Babyeyi')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-contract text-primary me-2"></i>
                Babyeyi Document
            </h2>
            <div class="text-muted">
                Reference: <code>{{ $babyeyi->reference_number }}</code>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if ($babyeyi->status === 'published')
                <a href="{{ route('babyeyi.download', $babyeyi) }}" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> Download PDF
                </a>
            @endif
            <a href="{{ route('babyeyi.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="mb-3">
        @if ($babyeyi->status === 'draft')
            <span class="badge bg-warning text-dark">Draft</span>
        @elseif ($babyeyi->status === 'preview')
            <span class="badge bg-info text-dark">Preview</span>
        @else
            <span class="badge bg-success">Published</span>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body bg-white p-4" style="min-height: 700px;">
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

                    @if ($babyeyi->title)
                        <h3 style="text-align: center; margin: 20px 0; color: #1a1a1a; font-weight: bold;">{{ $babyeyi->title }}</h3>
                    @else
                        <h3 style="text-align: center; margin: 20px 0; color: #1a1a1a; font-weight: bold;">BABYEYI - TERM REGISTRATION DOCUMENT</h3>
                    @endif

                    <div class="document-content" style="font-family: 'Georgia', serif; line-height: 1.8; color: #333; font-size: 15px;">
                        {!! $babyeyi->content !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <strong>Document Information</strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block"><strong>Reference Number</strong></small>
                        <code>{{ $babyeyi->reference_number }}</code>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <small class="text-muted d-block"><strong>Academic Year</strong></small>
                        {{ $babyeyi->academicYear->year ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block"><strong>Term</strong></small>
                        {{ $babyeyi->term->name ?? 'N/A' }}
                    </div>
                    @if ($babyeyi->course)
                        <div class="mb-3">
                            <small class="text-muted d-block"><strong>Course</strong></small>
                            {{ $babyeyi->course->name }}
                        </div>
                    @endif
                    @if ($babyeyi->course_type)
                        <div class="mb-3">
                            <small class="text-muted d-block"><strong>Course Type</strong></small>
                            {{ $babyeyi->course_type }}
                        </div>
                    @endif
                    <hr>
                    <div class="mb-3">
                        <small class="text-muted d-block"><strong>Status</strong></small>
                        @if ($babyeyi->status === 'draft')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @elseif ($babyeyi->status === 'preview')
                            <span class="badge bg-info text-dark">Preview</span>
                        @elseif ($babyeyi->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @endif
                    </div>
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted d-block"><strong>Created by</strong></small>
                        {{ $babyeyi->creator->name ?? 'System' }} · <span class="text-muted">{{ $babyeyi->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    @if ($babyeyi->editor)
                        <div class="mb-0">
                            <small class="text-muted d-block"><strong>Edited by</strong></small>
                            {{ $babyeyi->editor->name }} · <span class="text-muted">{{ $babyeyi->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
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

