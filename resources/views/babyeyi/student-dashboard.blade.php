@extends('students.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-scroll"></i> My Babyeyi Documents</h1>
            <p class="text-muted">Term registration and fee information for your course</p>
        </div>
    </div>

    @if (isset($message))
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
        </div>
    @endif

    @if ($babyeyis->count() > 0)
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    @foreach ($babyeyis as $babyeyi)
                        <div class="col-md-12 mb-4">
                            <div class="card shadow-sm border-left-4" style="border-left: 4px solid #007bff;">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <h5 class="card-title mb-2">
                                                <i class="fas fa-file-alt"></i>
                                                {{ $babyeyi->academicYear->year ?? 'Academic Year' }} - 
                                                <strong>{{ $babyeyi->term->name ?? 'Term' }}</strong>
                                            </h5>
                                            
                                            <!-- Scope Badge -->
                                            @if (!$babyeyi->course_id && !$babyeyi->course_type)
                                                <span class="badge bg-success mb-2"><i class="fas fa-globe"></i> For All Courses</span>
                                            @elseif ($babyeyi->course_id)
                                                <span class="badge bg-primary mb-2"><i class="fas fa-graduation-cap"></i> Specific Course</span>
                                            @elseif ($babyeyi->course_type)
                                                <span class="badge bg-info mb-2"><i class="fas fa-tag"></i> {{ $babyeyi->course_type }}</span>
                                            @endif
                                            
                                            <p class="card-text text-secondary small mb-2">
                                                <strong>Reference:</strong> <code>{{ $babyeyi->reference_number }}</code>
                                            </p>

                                            @if ($babyeyi->term)
                                                <p class="card-text text-muted small mb-2">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    {{ $babyeyi->term->start_date->format('M d, Y') }} - 
                                                    {{ $babyeyi->term->end_date->format('M d, Y') }}
                                                </p>
                                            @endif

                                            @if ($babyeyi->trip_known && $babyeyi->trip_place)
                                                <p class="card-text text-muted small mb-1">
                                                    <i class="fas fa-bus"></i>
                                                    <strong>Trip:</strong> {{ $babyeyi->trip_place }}
                                                    @if ($babyeyi->trip_fees)
                                                        - <strong>RWF {{ number_format($babyeyi->trip_fees, 2) }}</strong>
                                                    @endif
                                                </p>
                                            @endif
                                        </div>

                                        <div class="col-md-4 text-right">
                                            <div class="btn-group-vertical w-100" role="group">
                                                <a href="{{ route('student.babyeyi.show', $babyeyi) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('student.babyeyi.download', $babyeyi) }}" class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-download"></i> Download PDF
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fee Schedule Table -->
                                    @if ($babyeyi->courseTermFees)
                                        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
                                            <p style="margin: 0 0 10px 0;"><strong><i class="fas fa-money-bill-wave"></i> Your Fee Breakdown for This Term:</strong></p>
                                            <table style="width: 100%; font-size: 13px; margin-bottom: 10px;">
                                                <tr style="background-color: #f5f5f5;">
                                                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;"><strong>Fee Type</strong></th>
                                                    <th style="padding: 8px; text-align: right; border: 1px solid #ddd;"><strong>Amount (RWF)</strong></th>
                                                </tr>
                                                @if ($babyeyi->courseTermFees->registration_fee > 0)
                                                    <tr>
                                                        <td style="padding: 8px; border: 1px solid #ddd;">Registration Fee</td>
                                                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($babyeyi->courseTermFees->registration_fee, 2) }}</td>
                                                    </tr>
                                                @endif
                                                @if ($babyeyi->courseTermFees->tuition_fee > 0)
                                                    <tr style="background-color: #f9f9f9;">
                                                        <td style="padding: 8px; border: 1px solid #ddd;">Tuition Fee</td>
                                                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($babyeyi->courseTermFees->tuition_fee, 2) }}</td>
                                                    </tr>
                                                @endif
                                                @if ($babyeyi->courseTermFees->practical_fee > 0)
                                                    <tr>
                                                        <td style="padding: 8px; border: 1px solid #ddd;">Practical Fee</td>
                                                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($babyeyi->courseTermFees->practical_fee, 2) }}</td>
                                                    </tr>
                                                @endif
                                                @if ($babyeyi->courseTermFees->trip_fee > 0)
                                                    <tr style="background-color: #f9f9f9;">
                                                        <td style="padding: 8px; border: 1px solid #ddd;">Trip Fee</td>
                                                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($babyeyi->courseTermFees->trip_fee, 2) }}</td>
                                                    </tr>
                                                @endif
                                                @if ($babyeyi->courseTermFees->student_card_fee > 0)
                                                    <tr>
                                                        <td style="padding: 8px; border: 1px solid #ddd;">Student Card Fee</td>
                                                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format($babyeyi->courseTermFees->student_card_fee, 2) }}</td>
                                                    </tr>
                                                @endif
                                                <tr style="background-color: #e8e8e8; font-weight: bold;">
                                                    <td style="padding: 10px; border: 2px solid #333;"><strong>TOTAL DUE</strong></td>
                                                    <td style="padding: 10px; border: 2px solid #333; text-align: right; color: #d32f2f;">
                                                        <strong>{{ number_format($babyeyi->courseTermFees->registration_fee + $babyeyi->courseTermFees->tuition_fee + $babyeyi->courseTermFees->practical_fee + $babyeyi->courseTermFees->trip_fee + $babyeyi->courseTermFees->student_card_fee, 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </table>
                                            <p style="margin: 8px 0; font-size: 12px; color: #d32f2f;"><strong>⚠️ Payment required before term start date</strong></p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar: Course Info -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-book"></i> Your Course</h5>
                    </div>
                    <div class="card-body">
                        @if ($student && $student->course)
                            <p class="mb-2">
                                <strong>{{ $student->course->course_name ?? $student->course->name ?? 'Course' }}</strong>
                            </p>
                            @if ($student->course->course_type)
                                <p class="mb-2 text-muted small">
                                    <strong>Type:</strong> {{ $student->course->course_type }}
                                </p>
                            @endif
                            <p class="mb-2 text-muted small">
                                <strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}
                            </p>
                            <p class="mb-0 text-muted small">
                                <strong>Academic Year:</strong> 
                                {{ $student->academicYear->year ?? 'N/A' }}
                            </p>
                        @else
                            <p class="text-danger">No course assigned</p>
                        @endif
                    </div>
                </div>

                @if ($schoolInfo)
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-school"></i> School Information</h5>
                        </div>
                        <div class="card-body">
                            @if ($schoolInfo->logo_path)
                                <img src="{{ asset('storage/' . $schoolInfo->logo_path) }}" class="img-fluid mb-3" style="max-height: 80px;" alt="School Logo">
                            @endif
                            <p class="mb-2"><strong>{{ $schoolInfo->school_name ?? 'School Name' }}</strong></p>
                            @if ($schoolInfo->location)
                                <p class="mb-2 text-muted small">
                                    <i class="fas fa-map-marker-alt"></i> {{ $schoolInfo->location }}
                                </p>
                            @endif
                            @if ($schoolInfo->phone)
                                <p class="mb-2 text-muted small">
                                    <i class="fas fa-phone"></i> {{ $schoolInfo->phone }}
                                </p>
                            @endif
                            @if ($schoolInfo->email)
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-envelope"></i> {{ $schoolInfo->email }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i> 
            <strong>No documents yet.</strong> Your course's babyeyi documents will appear here once they are published by the administration.
        </div>
    @endif
</div>
@endsection
