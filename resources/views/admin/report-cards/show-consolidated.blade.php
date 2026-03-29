@extends('admin.layout')

@section('content')
@php
    $first = $reportCards->first();
    $overallAvg = (float) $reportCards->avg('average_marks');
@endphp
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">Report card — all terms</h1>
            <p class="text-muted mb-0 small">
                {{ $first->student->full_name }} · {{ $first->course->course_name }} · {{ $first->academicYear->year }}
                · Overall avg <strong>{{ number_format($overallAvg, 1) }}%</strong>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.report-cards.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.report-cards.consolidated-print', ['student_id' => $first->student_id, 'course_id' => $first->course_id, 'academic_year_id' => $first->academic_year_id]) }}" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                <i class="fas fa-print"></i> Print all terms
            </a>
        </div>
    </div>

    @if($schoolInfo)
        <div class="alert alert-light border mb-4">
            <strong>{{ $schoolInfo->school_name }}</strong>
            @if($schoolInfo->address)<span class="text-muted"> · {{ $schoolInfo->address }}</span>@endif
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2"><strong>Student</strong></div>
                <div class="card-body py-2 small">
                    <div><strong>Name:</strong> {{ $first->student->full_name }}</div>
                    <div><strong>Reg:</strong> {{ $first->student->reg_number ?? '—' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2"><strong>Terms included</strong></div>
                <div class="card-body py-2">
                    @foreach($reportCards as $rc)
                        <span class="badge badge-secondary mr-1 mb-1">{{ $rc->term?->name ?? '—' }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @foreach($reportCards as $reportCard)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span><i class="fas fa-calendar-alt mr-1"></i> <strong>{{ $reportCard->term?->name ?? 'Term' }}</strong></span>
                <div>
                    <span class="badge badge-{{ $reportCard->status === 'approved' ? 'success' : ($reportCard->status === 'printed' ? 'info' : 'warning') }}">
                        {{ ucfirst($reportCard->status) }}
                    </span>
                    <a href="{{ route('admin.report-cards.show', $reportCard) }}" class="btn btn-sm btn-outline-primary ml-2">Single term</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Subject</th>
                                <th class="text-center">Exam</th>
                                <th class="text-center">Test avg</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportCard->subjects as $subject)
                                <tr>
                                    <td>{{ $subject->subject_name }}</td>
                                    <td class="text-center">{{ number_format($subject->exam_mark, 2) }}</td>
                                    <td class="text-center">{{ number_format($subject->test_average, 2) }}</td>
                                    <td class="text-center"><strong>{{ number_format($subject->total_mark, 2) }}</strong></td>
                                    <td class="text-center">{{ $subject->grade }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No subjects</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top bg-light small">
                    <strong>Term average:</strong> {{ number_format($reportCard->average_marks ?? 0, 2) }}%
                    · <strong>Grade:</strong> {{ $reportCard->grade }}
                    · <strong>Position:</strong> {{ $reportCard->position_display }}
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
