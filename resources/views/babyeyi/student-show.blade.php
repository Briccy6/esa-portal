@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h1><i class="fas fa-scroll"></i> Babyeyi Document</h1>
            <small class="text-muted">Reference: {{ $babyeyi->reference_number }}</small>
        </div>
        <div class="col text-right">
            <a href="{{ route('student.babyeyi.download', $babyeyi) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Document -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body bg-white" style="min-height: 600px;">
                    <!-- School Header -->
                    @if ($schoolInfo)
                        <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px;">
                            @if ($schoolInfo->logo_path)
                                <img src="{{ asset('storage/' . $schoolInfo->logo_path) }}" style="max-height: 80px; margin-bottom: 10px;" alt="School Logo">
                            @endif
                            <h2 style="margin: 5px 0; color: #1a1a1a;">{{ $schoolInfo->name ?? 'School Name' }}</h2>
                            <p style="margin: 2px 0; color: #666; font-size: 14px;">{{ $schoolInfo->location ?? '' }}</p>
                            @if ($schoolInfo->phone)
                                <p style="margin: 2px 0; color: #666; font-size: 14px;">Tel: {{ $schoolInfo->phone }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Document Title -->
                    <h3 style="text-align: center; margin: 20px 0; color: #1a1a1a;">BABYEYI</h3>

                    <!-- Main Content -->
                    <div class="document-content">
                        {!! $babyeyi->content !!}
                    </div>

                    <!-- Fee Details Section for Student -->
                    @if ($courseTermFees)
                        <div style="margin-top: 40px; padding: 20px; background-color: #f9f9f9; border-left: 4px solid #007bff; border-radius: 4px;">
                            <h4 style="margin-bottom: 15px; color: #1a1a1a;">
                                <i class="fas fa-money-bill-wave"></i> YOUR FEE STRUCTURE FOR THIS TERM
                            </h4>
                            
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                                <thead>
                                    <tr style="background-color: #e8e8e8;">
                                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Fee Type</th>
                                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold;">Amount (RWF)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($courseTermFees->registration_fee > 0)
                                        <tr>
                                            <td style="padding: 10px; border: 1px solid #ddd;">Registration Fee</td>
                                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{ number_format($courseTermFees->registration_fee, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($courseTermFees->tuition_fee > 0)
                                        <tr>
                                            <td style="padding: 10px; border: 1px solid #ddd;">Tuition Fee</td>
                                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{ number_format($courseTermFees->tuition_fee, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($courseTermFees->practical_fee > 0)
                                        <tr>
                                            <td style="padding: 10px; border: 1px solid #ddd;">Practical Fee</td>
                                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{ number_format($courseTermFees->practical_fee, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($courseTermFees->trip_fee > 0)
                                        <tr>
                                            <td style="padding: 10px; border: 1px solid #ddd;">Trip Fee</td>
                                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{ number_format($courseTermFees->trip_fee, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($courseTermFees->student_card_fee > 0)
                                        <tr>
                                            <td style="padding: 10px; border: 1px solid #ddd;">Student Card Fee</td>
                                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{ number_format($courseTermFees->student_card_fee, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr style="background-color: #e8e8e8; font-weight: bold;">
                                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">TOTAL FEES DUE</td>
                                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                            {{ number_format($courseTermFees->registration_fee + $courseTermFees->tuition_fee + $courseTermFees->practical_fee + $courseTermFees->trip_fee + $courseTermFees->student_card_fee, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <p style="color: #d32f2f; font-weight: bold; margin: 10px 0;">
                                ⚠️ All fees must be paid before the start of the term to attend classes.
                            </p>
                        </div>
                    @endif

                    <!-- Footer Section -->
                    <div style="margin-top: 60px; padding-top: 30px; border-top: 1px solid #ccc;">
                        <div style="display: flex; justify-content: space-between; margin-top: 40px;">
                            <div style="width: 30%;">
                                <p style="margin-bottom: 50px; border-bottom: 1px solid #333;"></p>
                                <p style="font-weight: bold; margin: 5px 0;">Head Teacher Signature</p>
                                <p style="font-size: 12px; color: #666; margin: 2px 0;">Stamp of Institution</p>
                            </div>
                            <div style="width: 30%;">
                                <p style="margin-bottom: 50px; border-bottom: 1px solid #333;"></p>
                                <p style="font-weight: bold; margin: 5px 0;">Date</p>
                            </div>
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
                    <hr>
                    <div class="mb-3">
                        <small class="text-muted"><strong>Academic Year:</strong></small><br>
                        {{ $babyeyi->academicYear->year ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <small class="text-muted"><strong>Term:</strong></small><br>
                        {{ $babyeyi->term->name ?? 'N/A' }}<br>
                        <small class="text-muted">
                            {{ $babyeyi->term->start_date->format('M d, Y') }} - 
                            {{ $babyeyi->term->end_date->format('M d, Y') }}
                        </small>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted"><strong>Your Course:</strong></small><br>
                        <strong>{{ $student->course->name ?? 'N/A' }}</strong>
                    </div>
                    <hr>
                    @if ($babyeyi->trip_known && $babyeyi->trip_place)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Trip Destination:</strong></small><br>
                            <strong>{{ $babyeyi->trip_place }}</strong>
                        </div>
                    @endif
                    @if ($babyeyi->trip_fees)
                        <div class="mb-3">
                            <small class="text-muted"><strong>Trip Fees:</strong></small><br>
                            <strong>RWF {{ number_format($babyeyi->trip_fees, 2) }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            @if ($courseTermFees)
                <div class="card shadow mb-4 border-left-4" style="border-left: 4px solid #28a745;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Your Total Fees</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Registration</small><br>
                            <strong>RWF {{ number_format($courseTermFees->registration_fee, 2) }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Tuition</small><br>
                            <strong>RWF {{ number_format($courseTermFees->tuition_fee, 2) }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Practical</small><br>
                            <strong>RWF {{ number_format($courseTermFees->practical_fee, 2) }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Trip</small><br>
                            <strong>RWF {{ number_format($courseTermFees->trip_fee, 2) }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Student Card</small><br>
                            <strong>RWF {{ number_format($courseTermFees->student_card_fee, 2) }}</strong>
                        </div>
                        <hr>
                        <div style="background-color: #f0f0f0; padding: 12px; border-radius: 4px; text-align: center;">
                            <small class="text-muted"><strong>TOTAL DUE</strong></small><br>
                            <h4 class="mb-0" style="color: #28a745;">
                                RWF {{ number_format($courseTermFees->registration_fee + $courseTermFees->tuition_fee + $courseTermFees->practical_fee + $courseTermFees->trip_fee + $courseTermFees->student_card_fee, 2) }}
                            </h4>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-grid gap-2">
                <a href="{{ route('student.babyeyi.download', $babyeyi) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-download"></i> Download PDF
                </a>
                <a href="{{ route('student.babyeyi.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Back to Documents
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

.border-left-4 {
    border-left: 4px solid #28a745;
}

code {
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>
@endsection
