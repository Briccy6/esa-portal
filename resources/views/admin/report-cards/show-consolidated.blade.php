@extends('admin.layout')

@section('content')
@php
    $first = $reportCards->first();
    $overallAvg = (float) $reportCards->avg('average_marks');
    $terms = $assessmentMatrix['terms'];
    $mRows = $assessmentMatrix['rows'];
    $totalsByTerm = $assessmentMatrix['totals_by_term'];
    $positionByTerm = $assessmentMatrix['position_by_term'];
    $averageByTerm = $assessmentMatrix['average_by_term'];
    $termCount = count($terms);
@endphp
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">Report card — all terms (one table)</h1>
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
                <i class="fas fa-print"></i> Print PDF
            </a>
        </div>
    </div>

    @if($schoolInfo)
        <div class="alert alert-light border mb-4">
            <strong>{{ $schoolInfo->school_name }}</strong>
            @if($schoolInfo->address)<span class="text-muted"> · {{ $schoolInfo->address }}</span>@endif
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header py-2 d-flex flex-wrap justify-content-between align-items-center">
            <strong>Subject performance by term</strong>
            <span class="small text-muted">Each term has TEST, EXAM, and TOT (subject total that term)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 text-center" style="font-size: 0.85rem;">
                    <thead class="thead-light">
                        <tr>
                            <th colspan="3" class="text-left">Module / competence / credits</th>
                            @foreach($terms as $t)
                                <th colspan="3">{{ $t['name'] }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Module</th>
                            <th class="text-left">Competence</th>
                            <th>Cr</th>
                            @foreach($terms as $t)
                                <th>Test</th>
                                <th>Exam</th>
                                <th>Tot</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mRows as $row)
                            <tr>
                                <td class="text-left">{{ Str::limit($row['module_code'], 32) }}</td>
                                <td class="text-left">{{ Str::limit($row['competence_title'], 36) }}</td>
                                <td>{{ $row['credits'] }}</td>
                                @foreach($terms as $t)
                                    @php $cell = $row['by_term_id'][$t['id']] ?? ['test' => '—', 'exam' => '—', 'total' => '—']; @endphp
                                    <td>{{ $cell['test'] }}</td>
                                    <td>{{ $cell['exam'] }}</td>
                                    <td><strong>{{ $cell['total'] }}</strong></td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ 3 + 3 * $termCount }}" class="text-muted">No subjects</td></tr>
                        @endforelse
                        @if(count($mRows) > 0)
                            <tr class="table-secondary font-weight-bold">
                                <td colspan="3" class="text-left">TOTAL</td>
                                @foreach($terms as $t)
                                    @php $tot = $totalsByTerm[$t['id']] ?? null; @endphp
                                    <td>{{ $tot['test_sum'] ?? '—' }}</td>
                                    <td>{{ $tot['exam_sum'] ?? '—' }}</td>
                                    <td>{{ $tot['total_sum'] ?? '—' }}</td>
                                @endforeach
                            </tr>
                            <tr class="table-warning small">
                                <td colspan="3" class="text-left">Position</td>
                                @foreach($terms as $t)
                                    <td colspan="3">{{ $positionByTerm[$t['id']] ?? '—' }}</td>
                                @endforeach
                            </tr>
                            <tr class="table-light small">
                                <td colspan="3" class="text-left">Average %</td>
                                @foreach($terms as $t)
                                    @php $avg = $averageByTerm[$t['id']] ?? null; @endphp
                                    <td colspan="3">{{ $avg !== null ? number_format((float) $avg, 1).'%' : '—' }}</td>
                                @endforeach
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header py-2"><strong>Open a single term</strong></div>
        <div class="card-body py-2 small">
            @foreach($reportCards as $rc)
                <a href="{{ route('admin.report-cards.show', $rc) }}" class="btn btn-sm btn-outline-primary mr-1 mb-1">
                    {{ $rc->term?->name ?? 'Term' }} · {{ ucfirst($rc->status) }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
