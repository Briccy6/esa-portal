@extends('admin.layout')

@section('content')
@php
    $allTermIdsForStudent = $siblingReportCards->pluck('term_id')->push($reportCard->term_id)->unique()->sort()->values();
    $hasMultipleTerms = $allTermIdsForStudent->count() > 1;
    $consolidatedQueryForStudent = [
        'student_id' => $reportCard->student_id,
        'course_id' => $reportCard->course_id,
        'academic_year_id' => $reportCard->academic_year_id,
        'term_ids' => $allTermIdsForStudent->all(),
    ];
@endphp
<div class="container-fluid px-4 py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rc-flash" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    {{-- Page header (aligned with report cards index) --}}
    <div class="rc-page-header mb-4">
        <div class="rc-page-header__left">
            <div class="rc-page-header__icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <h1 class="rc-page-header__title">Report card detail</h1>
                <p class="rc-page-header__sub">
                    {{ $reportCard->student->full_name }}
                    <span class="rc-meta-sep">·</span>
                    {{ $reportCard->course->course_name }}
                    <span class="rc-meta-sep">·</span>
                    {{ $reportCard->academicYear->year }}
                    <span class="rc-meta-sep">·</span>
                    {{ $reportCard->term?->name ?? '—' }}
                </p>
            </div>
        </div>
        <div class="rc-page-header__actions">
            <a href="{{ route('admin.report-cards.index') }}" class="rc-btn rc-btn--ghost">
                <i class="fas fa-arrow-left"></i> Back to list
            </a>
            @if($hasMultipleTerms)
                <a href="{{ route('admin.report-cards.consolidated-print', $consolidatedQueryForStudent) }}" class="rc-btn rc-btn--primary" target="_blank" rel="noopener">
                    <i class="fas fa-print"></i> Print PDF ({{ $allTermIdsForStudent->count() }} terms)
                </a>
                <a href="{{ route('admin.report-cards.print', $reportCard) }}" class="rc-btn rc-btn--ghost" target="_blank" rel="noopener" title="PDF for this term only">
                    <i class="fas fa-file-alt"></i> This term only
                </a>
            @else
                <a href="{{ route('admin.report-cards.print', $reportCard) }}" class="rc-btn rc-btn--primary" target="_blank" rel="noopener">
                    <i class="fas fa-print"></i> Print PDF
                </a>
            @endif
        </div>
    </div>

    {{-- School banner --}}
    @if($schoolInfo)
        <div class="rc-card mb-4">
            <div class="rc-card__body rc-school-banner">
                <div class="rc-school-banner__logo">
                    @if($schoolInfo->logo_url)
                        <img src="{{ $schoolInfo->logo_url }}" alt="" class="rc-school-logo">
                    @else
                        <div class="rc-school-logo--placeholder"><i class="fas fa-school"></i></div>
                    @endif
                </div>
                <div class="rc-school-banner__text">
                    <div class="rc-school-name">{{ $schoolInfo->school_name }}</div>
                    <div class="rc-school-meta">
                        {{ $schoolInfo->address }}
                        @if($schoolInfo->phone) · Tel: {{ $schoolInfo->phone }} @endif
                        @if($schoolInfo->email) · {{ $schoolInfo->email }} @endif
                        @if($schoolInfo->motto) · “{{ $schoolInfo->motto }}” @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Year / term chips + siblings --}}
    <div class="rc-card mb-4">
        <div class="rc-card__body py-3">
            <div class="d-flex flex-wrap align-items-center gap-2 rc-chip-row">
                <span class="rc-chip rc-chip--year">
                    <i class="fas fa-calendar-alt me-1"></i> {{ $reportCard->academicYear->year }}
                </span>
                <span class="rc-chip rc-chip--term">
                    <i class="fas fa-bookmark me-1"></i> {{ $reportCard->term?->name ?? 'Term' }}
                </span>
                @if($reportCard->term && $reportCard->term->start_date && $reportCard->term->end_date)
                    <span class="rc-chip rc-chip--muted">
                        {{ $reportCard->term->start_date->format('M j, Y') }} — {{ $reportCard->term->end_date->format('M j, Y') }}
                    </span>
                @endif
                @php
                    $st = $reportCard->status;
                    $stClass = $st === 'approved' ? 'rc-chip--ok' : ($st === 'printed' ? 'rc-chip--info' : 'rc-chip--warn');
                @endphp
                <span class="rc-chip {{ $stClass }}">{{ ucfirst($st) }}</span>
            </div>
            @if($siblingReportCards->isNotEmpty())
                <div class="rc-siblings mt-3">
                    <span class="rc-siblings__label"><i class="fas fa-layer-group me-1"></i> Other terms (same student, course &amp; year):</span>
                    @foreach($siblingReportCards as $sibling)
                        <a href="{{ route('admin.report-cards.show', $sibling) }}" class="rc-sibling-link">
                            {{ $sibling->term?->name ?? 'Term #'.$sibling->term_id }}
                            <span class="rc-sibling-avg">{{ number_format($sibling->average_marks ?? 0, 1) }}%</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="rc-card h-100">
                <div class="rc-card__header rc-card__header--green">
                    <i class="fas fa-user me-2"></i> Student
                </div>
                <div class="rc-card__body">
                    <div class="rc-detail-grid">
                        <div class="rc-detail-row"><span class="rc-detail-k">Name</span><span class="rc-detail-v">{{ $reportCard->student->full_name }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Registration</span><span class="rc-detail-v">{{ $reportCard->student->reg_number ?? 'N/A' }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Sex</span><span class="rc-detail-v">{{ ucfirst($reportCard->student->sex ?? 'N/A') }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Course</span><span class="rc-detail-v">{{ $reportCard->course->course_name }}</span></div>
                    </div>
                    <div class="mt-3 text-center">
                        @if($reportCard->student->passport_photo)
                            @if(str_contains($reportCard->student->passport_photo, 'http'))
                                <img src="{{ $reportCard->student->passport_photo }}" alt="" class="rc-photo">
                            @else
                                <img src="{{ asset($reportCard->student->passport_photo) }}" alt="" class="rc-photo"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="rc-photo rc-photo--empty" style="display:none; margin:0 auto;">
                                    <i class="fas fa-user-circle fa-3x text-muted"></i>
                                </div>
                            @endif
                        @else
                            <div class="rc-photo rc-photo--empty d-inline-block">
                                <i class="fas fa-user-circle fa-3x text-muted"></i>
                                <div class="small text-muted mt-1">No photo</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="rc-card h-100">
                <div class="rc-card__header rc-card__header--blue">
                    <i class="fas fa-chart-line me-2"></i> Academic summary
                </div>
                <div class="rc-card__body">
                    <div class="rc-detail-grid">
                        <div class="rc-detail-row"><span class="rc-detail-k">Academic year</span><span class="rc-detail-v">{{ $reportCard->academicYear->year }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Term</span><span class="rc-detail-v">{{ $reportCard->term?->name ?? '—' }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Class position</span><span class="rc-detail-v">{{ $reportCard->position_display }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Cohort size</span><span class="rc-detail-v">{{ $reportCard->total_students ?? '—' }}</span></div>
                        @if($reportCard->approvedBy)
                            <div class="rc-detail-row"><span class="rc-detail-k">Approved by</span><span class="rc-detail-v">{{ $reportCard->approvedBy->email ?? '—' }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Subject table --}}
    <div class="rc-card mb-4">
        <div class="rc-card__header rc-card__header--green">
            <i class="fas fa-table me-2"></i> Subject performance
        </div>
        <div class="rc-card__body p-0">
            <div class="rc-table-wrap">
                <table class="rc-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th class="text-center">Exam (60%)</th>
                            <th class="text-center">Test avg (40%)</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportCard->subjects as $subject)
                            <tr class="rc-table__row">
                                <td><strong>{{ $subject->subject_name }}</strong></td>
                                <td class="text-center">{{ number_format($subject->exam_mark, 2) }}</td>
                                <td class="text-center">{{ number_format($subject->test_average, 2) }}</td>
                                <td class="text-center"><strong>{{ number_format($subject->total_mark, 2) }}</strong></td>
                                <td class="text-center">
                                    @php
                                        $g = $subject->grade;
                                        $gc = match(true) {
                                            in_array($g, ['A+','A']) => 'rc-badge--green',
                                            in_array($g, ['B+','B']) => 'rc-badge--blue',
                                            $g === 'C' => 'rc-badge--yellow',
                                            default => 'rc-badge--red',
                                        };
                                    @endphp
                                    <span class="rc-badge {{ $gc }}">{{ $g }}</span>
                                </td>
                                <td class="text-muted small">{{ $subject->remarks ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No subjects recorded for this report card.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($reportCard->subjects->isNotEmpty())
                        <tfoot>
                            <tr class="rc-table-foot">
                                <td><strong>Total / average</strong></td>
                                <td class="text-center">{{ number_format($reportCard->subjects->avg('exam_mark'), 2) }}</td>
                                <td class="text-center">{{ number_format($reportCard->subjects->avg('test_average'), 2) }}</td>
                                <td class="text-center"><strong>{{ number_format($reportCard->average_marks, 2) }}</strong></td>
                                <td class="text-center">
                                    @php
                                        $og = $reportCard->grade;
                                        $ogc = match(true) {
                                            in_array($og, ['A+','A']) => 'rc-badge--green',
                                            in_array($og, ['B+','B']) => 'rc-badge--blue',
                                            $og === 'C' => 'rc-badge--yellow',
                                            default => 'rc-badge--red',
                                        };
                                    @endphp
                                    <span class="rc-badge {{ $ogc }}">{{ $og }}</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    @php $hasTestBreakdown = $reportCard->subjects->contains(fn ($s) => $s->testMarks->isNotEmpty()); @endphp
    @if($hasTestBreakdown)
        <div class="rc-card mb-4">
            <div class="rc-card__header rc-card__header--blue">
                <i class="fas fa-list-ul me-2"></i> Test breakdown
            </div>
            <div class="rc-card__body">
                @foreach($reportCard->subjects as $subject)
                    @if($subject->testMarks->isNotEmpty())
                        <div class="rc-test-block mb-4">
                            <h6 class="rc-test-block__title">{{ $subject->subject_name }}</h6>
                            <div class="rc-table-wrap">
                                <table class="rc-table rc-table--compact">
                                    <thead>
                                        <tr>
                                            <th>Test</th>
                                            <th class="text-center">Score</th>
                                            <th class="text-center">Max</th>
                                            <th class="text-center">%</th>
                                            <th class="text-center">Normalized</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subject->testMarks as $testMark)
                                            <tr>
                                                <td>{{ $testMark->test_name }}</td>
                                                <td class="text-center">{{ $testMark->original_mark }}</td>
                                                <td class="text-center">{{ $testMark->max_mark }}</td>
                                                <td class="text-center">{{ number_format($testMark->percentage, 2) }}%</td>
                                                <td class="text-center">{{ number_format($testMark->normalized_mark, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="rc-card h-100">
                <div class="rc-card__header rc-card__header--green"><i class="fas fa-poll me-2"></i> Performance summary</div>
                <div class="rc-card__body">
                    <div class="rc-detail-grid">
                        <div class="rc-detail-row"><span class="rc-detail-k">Subjects</span><span class="rc-detail-v">{{ $reportCard->subjects->count() }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Average</span><span class="rc-detail-v"><strong>{{ number_format($reportCard->average_marks, 2) }}%</strong></span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Total marks</span><span class="rc-detail-v">{{ number_format($reportCard->total_marks, 2) }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Final grade</span><span class="rc-detail-v">
                            @php $fg = $reportCard->grade; $fgc = match(true) { in_array($fg, ['A+','A']) => 'rc-badge--green', in_array($fg, ['B+','B']) => 'rc-badge--blue', $fg === 'C' => 'rc-badge--yellow', default => 'rc-badge--red' }; @endphp
                            <span class="rc-badge {{ $fgc }}">{{ $fg }}</span>
                        </span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="rc-card h-100">
                <div class="rc-card__header rc-card__header--blue"><i class="fas fa-trophy me-2"></i> Class standing</div>
                <div class="rc-card__body">
                    <div class="rc-detail-grid">
                        <div class="rc-detail-row"><span class="rc-detail-k">Position</span><span class="rc-detail-v">{{ $reportCard->class_position ?? 'N/A' }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Total students</span><span class="rc-detail-v">{{ $reportCard->total_students ?? '—' }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Year / term</span><span class="rc-detail-v">{{ $reportCard->academicYear->year }} · {{ $reportCard->term?->name ?? '—' }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Student Documents Section --}}
    <div class="rc-card mb-4">
        <div class="rc-card__header rc-card__header--blue">
            <i class="fas fa-file-image me-2"></i> Student Documents
        </div>
        <div class="rc-card__body">
            <div class="row">
                {{-- ID Photo --}}
                <div class="col-md-6 mb-3">
                    <div class="rc-document-card">
                        <div class="rc-document-card__header">
                            <i class="fas fa-id-card me-2"></i> ID Photo
                        </div>
                        <div class="rc-document-card__body">
                            @if($reportCard->student->id_photo)
                                @if(str_contains($reportCard->student->id_photo, 'http'))
                                    <img src="{{ $reportCard->student->id_photo }}" alt="ID Photo" class="rc-document-img">
                                @else
                                    <img src="{{ asset($reportCard->student->id_photo) }}" alt="ID Photo" class="rc-document-img"
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
                            @if($reportCard->student->passport_photo)
                                @if(str_contains($reportCard->student->passport_photo, 'http'))
                                    <img src="{{ $reportCard->student->passport_photo }}" alt="Passport Photo" class="rc-document-img">
                                @else
                                    <img src="{{ asset($reportCard->student->passport_photo) }}" alt="Passport Photo" class="rc-document-img"
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
                            @if($reportCard->student->senior6_document)
                                @if(str_contains($reportCard->student->senior6_document, 'http'))
                                    <a href="{{ $reportCard->student->senior6_document }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file-pdf me-2"></i> View Senior 6 Document
                                    </a>
                                @else
                                    <a href="{{ asset($reportCard->student->senior6_document) }}" target="_blank" class="rc-document-link">
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
                            @if($reportCard->student->senior3_document)
                                @if(str_contains($reportCard->student->senior3_document, 'http'))
                                    <a href="{{ $reportCard->student->senior3_document }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file-pdf me-2"></i> View Senior 3 Document
                                    </a>
                                @else
                                    <a href="{{ asset($reportCard->student->senior3_document) }}" target="_blank" class="rc-document-link">
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
                            @if($reportCard->student->other_supporting_document)
                                @if(str_contains($reportCard->student->other_supporting_document, 'http'))
                                    <a href="{{ $reportCard->student->other_supporting_document }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file me-2"></i> View Supporting Document
                                    </a>
                                @else
                                    <a href="{{ asset($reportCard->student->other_supporting_document) }}" target="_blank" class="rc-document-link">
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
                            @if($reportCard->student->nesa_document)
                                @if(str_contains($reportCard->student->nesa_document, 'http'))
                                    <a href="{{ $reportCard->student->nesa_document }}" target="_blank" class="rc-document-link">
                                        <i class="fas fa-file-pdf me-2"></i> View NESA Document
                                    </a>
                                @else
                                    <a href="{{ asset($reportCard->student->nesa_document) }}" target="_blank" class="rc-document-link">
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

    <div class="rc-card mb-4">
        <div class="rc-card__header rc-card__header--muted"><i class="fas fa-comment me-2"></i> Remarks</div>
        <div class="rc-card__body">
            @if($reportCard->remarks)
                <p class="mb-0">{{ $reportCard->remarks }}</p>
            @else
                <p class="text-muted mb-0">No remarks added.</p>
            @endif
        </div>
    </div>

    <div class="rc-actions-bar mb-5">
        @if($reportCard->status !== 'approved')
            <a href="{{ route('admin.report-cards.edit', $reportCard) }}" class="rc-btn rc-btn--warn">
                <i class="fas fa-edit"></i> Edit
            </a>
        @endif
        <form method="POST" action="{{ route('admin.report-cards.update', $reportCard) }}" class="d-inline">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="approved">
            <button type="submit" class="rc-btn rc-btn--success" onclick="return confirm('Approve this report card?')">
                <i class="fas fa-check"></i> Approve
            </button>
        </form>
        @if($hasMultipleTerms)
            <a href="{{ route('admin.report-cards.consolidated-print', $consolidatedQueryForStudent) }}" class="rc-btn rc-btn--primary" target="_blank" rel="noopener">
                <i class="fas fa-print"></i> Print PDF ({{ $allTermIdsForStudent->count() }} terms)
            </a>
            <a href="{{ route('admin.report-cards.print', $reportCard) }}" class="rc-btn rc-btn--ghost" target="_blank" rel="noopener" title="This term only">
                <i class="fas fa-file-alt"></i> This term only
            </a>
        @else
            <a href="{{ route('admin.report-cards.print', $reportCard) }}" class="rc-btn rc-btn--primary" target="_blank" rel="noopener">
                <i class="fas fa-print"></i> Print PDF
            </a>
        @endif
    </div>
</div>

<style>
.rc-page-header__actions { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
.rc-meta-sep { color: #adb5bd; margin: 0 0.2rem; }
.rc-flash { border-radius: 10px; }
.rc-school-banner {
    display: flex; align-items: flex-start; gap: 1rem;
}
.rc-school-logo { width: 64px; height: 64px; object-fit: contain; border-radius: 8px; }
.rc-school-logo--placeholder {
    width: 64px; height: 64px; border-radius: 8px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 1.5rem;
}
.rc-school-name { font-weight: 700; font-size: 1.05rem; color: #1a1a2e; }
.rc-school-meta { font-size: 0.82rem; color: #6c757d; margin-top: 4px; }
.rc-chip-row { gap: 0.5rem !important; }
.rc-chip {
    display: inline-flex; align-items: center;
    padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;
    background: #f1f3f5; color: #495057;
}
.rc-chip--year { background: #e7f1ff; color: #0d6efd; }
.rc-chip--term { background: #e6f4ea; color: #198754; }
.rc-chip--muted { background: #f8f9fa; color: #6c757d; font-weight: 500; }
.rc-chip--ok { background: #d1e7dd; color: #0f5132; }
.rc-chip--info { background: #cff4fc; color: #055160; }
.rc-chip--warn { background: #fff3cd; color: #664d03; }
.rc-siblings { font-size: 0.85rem; color: #495057; }
.rc-siblings__label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #6c757d; font-size: 0.78rem; text-transform: uppercase; letter-spacing: .04em; }
.rc-sibling-link {
    display: inline-flex; align-items: center; gap: 0.35rem;
    margin: 0 0.5rem 0.35rem 0;
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    background: #fff;
    border: 1px solid #dee2e6;
    color: #0d6efd;
    font-weight: 600;
    font-size: 0.85rem;
    text-decoration: none !important;
    transition: background .15s, border-color .15s;
}
.rc-sibling-link:hover { background: #f0f7ff; border-color: #0d6efd; color: #0a58ca; }
.rc-sibling-avg { font-weight: 500; color: #6c757d; font-size: 0.8rem; }
.rc-detail-grid { display: flex; flex-direction: column; gap: 0.5rem; }
.rc-detail-row { display: flex; justify-content: space-between; gap: 1rem; border-bottom: 1px solid #f1f3f5; padding-bottom: 0.45rem; }
.rc-detail-row:last-child { border-bottom: none; }
.rc-detail-k { color: #6c757d; font-size: 0.85rem; }
.rc-detail-v { font-weight: 600; text-align: right; color: #212529; }
.rc-photo { max-width: 140px; max-height: 160px; border-radius: 8px; object-fit: cover; }
.rc-photo--empty { padding: 1rem; }
.rc-card__header--muted { background: linear-gradient(135deg,#6c757d,#495057) !important; color: #fff !important; }
.rc-table-foot { background: #f8f9fa; font-weight: 700; }
.rc-table-foot td { padding: 0.75rem 0.9rem !important; border-top: 2px solid #dee2e6; }
.rc-test-block__title { font-weight: 700; color: #1a1a2e; margin-bottom: 0.5rem; }
.rc-table--compact thead th { padding: 0.45rem 0.65rem; font-size: 0.72rem; }
.rc-table--compact tbody td { padding: 0.4rem 0.65rem; font-size: 0.82rem; }
.rc-actions-bar { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
.rc-btn--ghost {
    background: #fff; color: #495057; border: 1px solid #dee2e6;
    box-shadow: none;
}
.rc-btn--ghost:hover { background: #f8f9fa; color: #212529; }
.rc-btn--warn {
    background: linear-gradient(135deg,#ffc107,#e0a800); color: #212529;
    box-shadow: 0 3px 10px rgba(255,193,7,.35);
}
.rc-btn--success {
    background: linear-gradient(135deg,#198754,#146c43); color: #fff;
    box-shadow: 0 3px 10px rgba(25,135,84,.3); border: none;
}
/* Reuse index table + badges (minimal) */
.rc-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.845rem; }
.rc-table thead tr { background: #f8f9fa; border-bottom: 2px solid #e9ecef; }
.rc-table thead th { padding: 0.7rem 0.9rem; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: .5px; color: #495057; border-bottom: 2px solid #dee2e6; }
.rc-table tbody td { padding: 0.65rem 0.9rem; border-bottom: 1px solid #f1f3f5; vertical-align: middle; }
.rc-table__row:hover { background: #f0f7ff; }
.rc-badge { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.78rem; font-weight: 700; }
.rc-badge--green { background: #d1e7dd; color: #0f5132; }
.rc-badge--blue { background: #cfe2ff; color: #084298; }
.rc-badge--yellow { background: #fff3cd; color: #664d03; }
.rc-badge--red { background: #f8d7da; color: #842029; }
.rc-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 16px rgba(0,0,0,.07); overflow: hidden; border: 1px solid #e9ecef; }
.rc-card__header { display: flex; align-items: center; padding: 0.85rem 1.25rem; font-weight: 600; font-size: 0.92rem; color: #fff; gap: 0.4rem; }
.rc-card__header--blue  { background: linear-gradient(135deg,#0d6efd,#0a58ca); }
.rc-card__header--green { background: linear-gradient(135deg,#198754,#0f5132); }
.rc-card__body { padding: 1.25rem; }
.rc-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.rc-page-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.rc-page-header__left { display: flex; align-items: center; gap: 1rem; }
.rc-page-header__icon {
    width: 48px; height: 48px; border-radius: 12px;
    background: linear-gradient(135deg, #198754 0%, #0f5132 100%);
    display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.25rem;
    flex-shrink: 0; box-shadow: 0 4px 12px rgba(25,135,84,.3);
}
.rc-page-header__title { font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0; }
.rc-page-header__sub { font-size: 0.82rem; color: #6c757d; margin: 0; max-width: 52ch; }
.rc-btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 0.5rem 1.1rem; border-radius: 8px;
    font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; text-decoration: none !important;
    transition: all .2s; white-space: nowrap;
}
.rc-btn--primary {
    background: linear-gradient(135deg,#0d6efd,#0a58ca); color: #fff;
    box-shadow: 0 3px 10px rgba(13,110,253,.3);
}
.rc-btn--primary:hover { color: #fff; transform: translateY(-1px); }

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
