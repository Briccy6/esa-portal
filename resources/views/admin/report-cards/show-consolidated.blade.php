@extends('admin.layout')

@push('styles')
    @include('admin.report-cards.partials.layout-fixes')
@endpush

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
<div class="content">
    <div class="report-container">
        <div class="container-fluid px-4 py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 mb-1">Report card — multiple terms (one table)</h1>
            <p class="text-muted mb-0 small">
                {{ $first->student->full_name }} · {{ $first->course->course_name }} · {{ $first->academicYear->year }}
                · Overall avg <strong>{{ number_format($overallAvg, 1) }}%</strong>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.report-cards.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.report-cards.consolidated-print', $consolidatedRouteParams ?? ['student_id' => $first->student_id, 'course_id' => $first->course_id, 'academic_year_id' => $first->academic_year_id]) }}" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                <i class="fas fa-print"></i> Print PDF ({{ $termCount }} {{ Str::plural('term', $termCount) }})
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
                                <th colspan="2">{{ $t['name'] }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Module</th>
                            <th class="text-left">Competence</th>
                            <th>Cr</th>
                            @foreach($terms as $t)
                                <th>Test</th>
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
                                    @php $cell = $row['by_term_id'][$t['id']] ?? ['test' => '—', 'total' => '—']; @endphp
                                    <td>{{ $cell['test'] }}</td>
                                    <td><strong>{{ $cell['total'] }}</strong></td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ 3 + 2 * $termCount }}" class="text-muted">No subjects</td></tr>
                        @endforelse
                        @if(count($mRows) > 0)
                            <tr class="table-secondary font-weight-bold">
                                <td colspan="3" class="text-left">TOTAL</td>
                                @foreach($terms as $t)
                                    @php $tot = $totalsByTerm[$t['id']] ?? null; @endphp
                                    <td>{{ $tot['test_sum'] ?? '—' }}</td>
                                    <td>{{ $tot['total_sum'] ?? '—' }}</td>
                                @endforeach
                            </tr>
                            <tr class="table-warning small">
                                <td colspan="3" class="text-left">Position</td>
                                @foreach($terms as $t)
                                    <td colspan="2">{{ $positionByTerm[$t['id']] ?? '—' }}</td>
                                @endforeach
                            </tr>
                            <tr class="table-light small">
                                <td colspan="3" class="text-left">Average %</td>
                                @foreach($terms as $t)
                                    @php $avg = $averageByTerm[$t['id']] ?? null; @endphp
                                    <td colspan="2">{{ $avg !== null ? number_format((float) $avg, 1).'%' : '—' }}</td>
                                @endforeach
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Student Documents Section --}}
    <div class="card mb-4">
        <div class="card-header py-2">
            <strong><i class="fas fa-file-image me-2"></i> Student Documents</strong>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- ID Photo --}}
                <div class="col-md-6 mb-3">
                    <div class="rc-document-card">
                        <div class="rc-document-card__header">
                            <i class="fas fa-id-card me-2"></i> ID Photo
                        </div>
                        <div class="rc-document-card__body">
                            @if($first->student->id_photo)
                                @if(str_contains($first->student->id_photo, 'http'))
                                    <img src="{{ $first->student->id_photo }}" alt="ID Photo" class="rc-document-img">
                                @else
                                    <img src="{{ asset($first->student->id_photo) }}" alt="ID Photo" class="rc-document-img"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rc-document-error" style="display:none;">
                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                        <span>Image not found</span>
                                    </div>
                                @endif
                            @else
                                <div class="rc-document-placeholder">
                                    <i class="fas fa-image text-muted"></i>
                                    <span class="text-muted">No ID photo uploaded</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Passport Photo --}}
                <div class="col-md-6 mb-3">
                    <div class="rc-document-card">
                        <div class="rc-document-card__header">
                            <i class="fas fa-user me-2"></i> Passport Photo
                        </div>
                        <div class="rc-document-card__body">
                            @if($first->student->passport_photo)
                                @if(str_contains($first->student->passport_photo, 'http'))
                                    <img src="{{ $first->student->passport_photo }}" alt="Passport Photo" class="rc-document-img">
                                @else
                                    <img src="{{ asset($first->student->passport_photo) }}" alt="Passport Photo" class="rc-document-img"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rc-document-error" style="display:none;">
                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                        <span>Image not found</span>
                                    </div>
                                @endif
                            @else
                                <div class="rc-document-placeholder">
                                    <i class="fas fa-user-circle text-muted"></i>
                                    <span class="text-muted">No passport photo uploaded</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Senior 6 Document --}}
                <div class="col-md-6 mb-3">
                    <div class="rc-document-card">
                        <div class="rc-document-card__header">
                            <i class="fas fa-graduation-cap me-2"></i> Senior 6 Document
                        </div>
                        <div class="rc-document-card__body">
                            @if($first->student->senior6_document)
                                @if(str_contains($first->student->senior6_document, 'http'))
                                    <a href="{{ $first->student->senior6_document }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file-pdf me-2"></i> View Senior 6 Document
                                    </a>
                                @else
                                    <a href="{{ asset($first->student->senior6_document) }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file-pdf me-2"></i> View Senior 6 Document
                                    </a>
                                @endif
                            @else
                                <div class="rc-document-placeholder">
                                    <i class="fas fa-file-alt text-muted"></i>
                                    <span class="text-muted">No Senior 6 document uploaded</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Senior 3 Document --}}
                <div class="col-md-6 mb-3">
                    <div class="rc-document-card">
                        <div class="rc-document-card__header">
                            <i class="fas fa-school me-2"></i> Senior 3 Document
                        </div>
                        <div class="rc-document-card__body">
                            @if($first->student->senior3_document)
                                @if(str_contains($first->student->senior3_document, 'http'))
                                    <a href="{{ $first->student->senior3_document }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file-pdf me-2"></i> View Senior 3 Document
                                    </a>
                                @else
                                    <a href="{{ asset($first->student->senior3_document) }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file-pdf me-2"></i> View Senior 3 Document
                                    </a>
                                @endif
                            @else
                                <div class="rc-document-placeholder">
                                    <i class="fas fa-file-alt text-muted"></i>
                                    <span class="text-muted">No Senior 3 document uploaded</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Other Supporting Document --}}
                <div class="col-md-6 mb-3">
                    <div class="rc-document-card">
                        <div class="rc-document-card__header">
                            <i class="fas fa-paperclip me-2"></i> Other Supporting Document
                        </div>
                        <div class="rc-document-card__body">
                            @if($first->student->other_supporting_document)
                                @if(str_contains($first->student->other_supporting_document, 'http'))
                                    <a href="{{ $first->student->other_supporting_document }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file me-2"></i> View Supporting Document
                                    </a>
                                @else
                                    <a href="{{ asset($first->student->other_supporting_document) }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file me-2"></i> View Supporting Document
                                    </a>
                                @endif
                            @else
                                <div class="rc-document-placeholder">
                                    <i class="fas fa-file text-muted"></i>
                                    <span class="text-muted">No supporting document uploaded</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- NESA Document --}}
                <div class="col-md-6 mb-3">
                    <div class="rc-document-card">
                        <div class="rc-document-card__header">
                            <i class="fas fa-certificate me-2"></i> NESA Document
                        </div>
                        <div class="rc-document-card__body">
                            @if($first->student->nesa_document)
                                @if(str_contains($first->student->nesa_document, 'http'))
                                    <a href="{{ $first->student->nesa_document }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file-pdf me-2"></i> View NESA Document
                                    </a>
                                @else
                                    <a href="{{ asset($first->student->nesa_document) }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file-pdf me-2"></i> View NESA Document
                                    </a>
                                @endif
                            @else
                                <div class="rc-document-placeholder">
                                    <i class="fas fa-certificate text-muted"></i>
                                    <span class="text-muted">No NESA document uploaded</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
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
    </div>
</div>

<style>
/* Document Cards Styles */
.rc-document-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    height: 100%;
}
.rc-document-card__header {
    background: linear-gradient(135deg,#f8f9fa,#e9ecef);
    padding: 0.6rem 0.9rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: #495057;
    border-bottom: 1px solid #e9ecef;
}
.rc-document-card__body {
    padding: 1rem;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.rc-document-img {
    max-width: 100%;
    max-height: 150px;
    border-radius: 6px;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.rc-document-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg,#e7f1ff,#dbeafe);
    color: #084298;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    border: 1px solid #bfdbfe;
    transition: all .2s;
}
.rc-document-link:hover {
    background: linear-gradient(135deg,#dbeafe,#bfdbfe);
    color: #0a58ca;
    transform: translateY(-1px);
    text-decoration: none;
}
.rc-document-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    text-align: center;
    color: #6c757d;
}
.rc-document-placeholder i {
    font-size: 2rem;
    opacity: 0.6;
}
.rc-document-placeholder span {
    font-size: 0.8rem;
}
.rc-document-error {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    text-align: center;
    color: #856404;
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 6px;
}
.rc-document-error i {
    font-size: 1.5rem;
}
.rc-document-error span {
    font-size: 0.8rem;
    font-weight: 600;
}
</style>
@endsection
