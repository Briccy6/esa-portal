<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Assessment Report - {{ $reportCard->student->full_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        
        * {
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
            overflow-x: hidden;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .action-buttons {
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .action-buttons a,
        .action-buttons button {
            padding: 8px 16px;
            font-size: 14px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            background: white;
            color: #495057;
            text-decoration: none;
            transition: all 0.2s;
        }

        .action-buttons a:hover,
        .action-buttons button:hover {
            background: #e2e6ea;
            border-color: #adb5bd;
        }

        .action-buttons .btn-primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .action-buttons .btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
        }

        .print-area {
            width: 100%;
            max-width: 170mm;
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
            border: 2px solid black;
            background: white;
            display: block;
        }
        
        /* Header: logo | details | logo (table layout for DomPDF) */
        .print-header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #000;
            table-layout: fixed;
        }
        .print-header-table td {
            vertical-align: middle;
            padding: 10px 8px;
        }
        .print-header-logo {
            width: 22%;
            text-align: center;
            border-right: 1px solid #000;
        }
        .print-header-logo--right {
            border-right: none;
            border-left: 1px solid #000;
        }
        .print-header-center {
            width: 56%;
            text-align: center;
            padding: 12px 14px !important;
        }
        .print-header-logo img,
        .school-logo-img {
            max-height: 88px;
            max-width: 110px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .print-logo-placeholder {
            width: 88px;
            height: 88px;
            margin: 0 auto;
            background: #ecf0f1;
            border: 2px dashed #bdc3c7;
            text-align: center;
            font-size: 8px;
            color: #7f8c8d;
            font-weight: bold;
            line-height: 84px;
        }
        .hdr-line {
            font-size: 10px;
            line-height: 1.45;
            margin: 0 0 3px 0;
        }
        .hdr-line .lbl {
            font-weight: bold;
        }
        .hdr-school-name {
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0 6px 0;
            letter-spacing: 0.3px;
        }
        .hdr-contact {
            font-size: 9px;
            line-height: 1.5;
            margin: 2px 0;
        }
        
        .title-section {
            background: #f0f0f0;
            border-bottom: 1px solid black;
            padding: 4px;
            text-align: center;
        }
        
        .title-section h2 {
            font-size: 12px;
            font-weight: bold;
            margin: 0;
        }
        
        .qualification-section {
            border-bottom: 1px solid black;
        }
        
        .qualification-row {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            font-size: 10px;
        }
        
        .qualification-cell {
            padding: 4px;
        }
        
        .qualification-cell:not(:last-child) {
            border-right: 1px solid black;
        }
        
        .legend-section {
            border-bottom: 1px solid black;
            padding: 4px;
            font-size: 8px;
        }
        
        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            table-layout: fixed;
        }
        
        .assessment-table th,
        .assessment-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .assessment-table .text-left {
            text-align: left;
        }
        
        .assessment-table .bg-gray {
            background: #f0f0f0;
        }
        
        .assessment-table .bg-blue {
            background: #e6f3ff;
        }
        
        .assessment-table .bg-green {
            background: #e6ffe6;
        }
        
        .assessment-table .bg-yellow {
            background: #ffffe6;
        }
        
        .assessment-table .font-bold {
            font-weight: bold;
        }
        
        .competent-mark {
            color: green;
            font-weight: bold;
        }
        
        .not-competent-mark {
            color: red;
            text-decoration: underline;
            font-weight: bold;
        }
        
        .deliberation-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-top: 1px;
            table-layout: fixed;
        }
        
        .deliberation-table th,
        .deliberation-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .deliberation-table .text-left {
            text-align: left;
        }
        
        .deliberation-table .bg-gray {
            background: #f0f0f0;
        }
        
        .footer {
            background: #f0f0f0;
            padding: 2px;
            text-align: right;
            font-size: 7px;
            border-top: 1px solid black;
        }
        
        .checkbox {
            width: 16px;
            height: 16px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
                background: white;
            }
            
            .action-buttons {
                display: none !important;
            }
            
            .print-area {
                margin: 0 auto;
                max-width: 170mm;
                width: 100%;
                box-sizing: border-box;
                border: none;
                box-shadow: none;
            }
        }
        
        /* Global overflow prevention */
        div, section, article, header, footer, main, nav, ul, ol, li, p, span, a, label, td, th {
            max-width: 100%;
            box-sizing: border-box;
        }
        
        img, svg, canvas, video, iframe {
            max-width: 100%;
            height: auto;
            display: block;
        }
    </style>
</head>
<body>
    <!-- Action Buttons (hidden when printing) -->
    <div class="action-buttons">
        <a href="{{ route('student.report-cards.index') }}" class="btn-back">← Back to Report Cards</a>
        <form action="{{ route('student.report-cards.download', $reportCard) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn-primary">📥 Download PDF</button>
        </form>
        <button onclick="window.print()" class="btn-print">🖨️ Print</button>
    </div>

    <!-- Report Card Content (same as admin print) -->
    <div class="print-area">
        @include('admin.report-cards.partials.print-assessment-inner', compact('reportCard', 'schoolInfo', 'studentPhotoBase64', 'studentPhotoMime'))
    </div>
</body>
</html>

.rc-page-header__left {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.rc-page-header__icon {
    font-size: 2.5rem;
    color: #667eea;
}

.rc-page-header__title {
    font-size: 2rem;
    font-weight: bold;
    margin: 0 0 0.5rem 0;
    color: #333;
}

.rc-page-header__sub {
    color: #666;
    font-size: 1rem;
    margin: 0;
    word-break: break-word;
}

.rc-meta-sep {
    color: #ccc;
    margin: 0 0.5rem;
}

.rc-page-header__actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.rc-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
    font-size: 0.875rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.rc-btn--primary {
    background-color: #667eea;
    color: white;
}

.rc-btn--primary:hover {
    background-color: #5568d3;
    color: white;
    text-decoration: none;
}

.rc-btn--ghost {
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
}

.rc-btn--ghost:hover {
    background-color: #e8e8e8;
    color: #333;
    text-decoration: none;
}

.rc-card {
    background: white;
    border-radius: 0.5rem;
    border: 1px solid #e8e8e8;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.rc-card__header {
    padding: 1rem;
    font-weight: 600;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
}

.rc-card__header--green {
    background-color: #10b981;
}

.rc-card__header--blue {
    background-color: #3b82f6;
}

.rc-card__body {
    padding: 1.5rem;
}

.rc-card p-0 .rc-card__body {
    padding: 0;
}

.rc-school-banner {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.rc-school-banner__logo {
    flex-shrink: 0;
}

.rc-school-logo {
    width: 80px;
    height: 80px;
    object-fit: contain;
}

.rc-school-logo--placeholder {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
    border-radius: 0.5rem;
    color: #999;
    font-size: 2rem;
}

.rc-school-name {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 0.25rem;
}

.rc-school-meta {
    font-size: 0.875rem;
    color: #666;
    line-height: 1.4;
}

.rc-chip-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.rc-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    background-color: #f0f0f0;
    color: #333;
}

.rc-chip--year {
    background-color: #fef3c7;
    color: #92400e;
}

.rc-chip--term {
    background-color: #dbeafe;
    color: #075985;
}

.rc-chip--ok {
    background-color: #dcfce7;
    color: #166534;
}

.rc-chip--info {
    background-color: #e0e7ff;
    color: #3730a3;
}

.rc-chip--warn {
    background-color: #fed7aa;
    color: #92400e;
}

.rc-chip--muted {
    background-color: #f3f4f6;
    color: #6b7280;
}

.rc-siblings {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e8e8e8;
}

.rc-siblings__label {
    font-size: 0.875rem;
    color: #666;
    font-weight: 500;
}

.rc-sibling-link {
    display: inline-block;
    margin: 0.5rem 0.5rem 0 0;
    padding: 0.5rem 0.75rem;
    background-color: #f0f0f0;
    border-radius: 0.375rem;
    text-decoration: none;
    color: #333;
    font-size: 0.875rem;
    border: 1px solid #ddd;
    transition: all 0.2s;
}

.rc-sibling-link:hover {
    background-color: #e8e8e8;
    border-color: #999;
}

.rc-sibling-avg {
    margin-left: 0.5rem;
    color: #667eea;
    font-weight: bold;
}

.rc-detail-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

.rc-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e8e8e8;
}

.rc-detail-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.rc-detail-k {
    font-weight: 600;
    color: #666;
    min-width: 120px;
}

.rc-detail-v {
    color: #333;
    text-align: right;
    word-break: break-word;
}

.rc-photo {
    width: 150px;
    height: 180px;
    object-fit: cover;
    border-radius: 0.375rem;
    border: 1px solid #ddd;
}

.rc-photo--empty {
    width: 150px;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
    border-radius: 0.375rem;
    border: 1px solid #ddd;
    color: #999;
}

.rc-table-wrap {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.rc-table {
    width: 100%;
    border-collapse: collapse;
}

.rc-table th {
    background-color: #f3f4f6;
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.875rem;
    color: #333;
    border-bottom: 2px solid #e8e8e8;
}

.rc-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #e8e8e8;
    color: #333;
}

.rc-table tbody tr:hover {
    background-color: #f9f9f9;
}

.rc-table__row td:first-child {
    font-weight: 600;
}

.rc-table-foot td {
    font-weight: 600;
    background-color: #f9f9f9;
    border-top: 2px solid #e8e8e8;
}

.rc-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.rc-badge--green {
    background-color: #d1fae5;
    color: #065f46;
}

.rc-badge--blue {
    background-color: #dbeafe;
    color: #075985;
}

.rc-badge--yellow {
    background-color: #fef3c7;
    color: #92400e;
}

.rc-badge--red {
    background-color: #fee2e2;
    color: #991b1b;
}

.rc-text-center {
    text-align: center;
}

.rc-text-muted {
    color: #999;
    font-size: 0.875rem;
}

@media (min-width: 768px) {
    .rc-page-header {
        flex-wrap: nowrap;
    }
    
    .rc-page-header__actions {
        justify-content: flex-start;
    }
}

@media print {
    .rc-page-header__actions,
    .rc-btn {
        display: none !important;
    }
    
    .rc-card {
        box-shadow: none;
        page-break-inside: avoid;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <!-- Page Header -->
        <div class="rc-page-header">
            <div class="rc-page-header__left">
                <div class="rc-page-header__icon">
                    <i class="bi bi-id-card"></i>
                </div>
                <div>
                    <h1 class="rc-page-header__title">Report Card</h1>
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
                <a href="{{ route('student.report-cards.index') }}" class="rc-btn rc-btn--ghost">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <a href="{{ route('student.report-cards.download', $reportCard) }}" class="rc-btn rc-btn--primary">
                    <i class="bi bi-download"></i> Download PDF
                </a>
                <button onclick="window.print()" class="rc-btn rc-btn--ghost">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>

    <!-- School Banner -->
    @if($schoolInfo)
    <div class="rc-card mb-4">
        <div class="rc-card__body rc-school-banner">
            <div class="rc-school-banner__logo">
                @if($schoolInfo->logo_url)
                    <img src="{{ $schoolInfo->logo_url }}" alt="" class="rc-school-logo">
                @else
                    <div class="rc-school-logo--placeholder"><i class="bi bi-building"></i></div>
                @endif
            </div>
            <div class="rc-school-banner__text">
                <div class="rc-school-name">{{ $schoolInfo->school_name }}</div>
                <div class="rc-school-meta">
                    {{ $schoolInfo->address }}
                    @if($schoolInfo->phone) · Tel: {{ $schoolInfo->phone }} @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Year / Term Info -->
    <div class="rc-card mb-4">
        <div class="rc-card__body py-3">
            <div class="rc-chip-row">
                <span class="rc-chip rc-chip--year">
                    <i class="bi bi-calendar-alt"></i> {{ $reportCard->academicYear->year }}
                </span>
                <span class="rc-chip rc-chip--term">
                    <i class="bi bi-bookmark"></i> {{ $reportCard->term?->name ?? 'Term' }}
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
        </div>
    </div>

    <!-- Two Column: Student & Academic Info -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="rc-card h-100">
                <div class="rc-card__header rc-card__header--green">
                    <i class="bi bi-person"></i> Student Information
                </div>
                <div class="rc-card__body">
                    <div class="rc-detail-grid">
                        <div class="rc-detail-row"><span class="rc-detail-k">Name</span><span class="rc-detail-v">{{ $reportCard->student->full_name }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Registration</span><span class="rc-detail-v">{{ $reportCard->student->reg_number ?? 'N/A' }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Course</span><span class="rc-detail-v">{{ $reportCard->course->course_name }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="rc-card h-100">
                <div class="rc-card__header rc-card__header--blue">
                    <i class="bi bi-graph-up"></i> Academic Summary
                </div>
                <div class="rc-card__body">
                    <div class="rc-detail-grid">
                        <div class="rc-detail-row"><span class="rc-detail-k">Academic Year</span><span class="rc-detail-v">{{ $reportCard->academicYear->year }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Term</span><span class="rc-detail-v">{{ $reportCard->term?->name ?? '—' }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Class Position</span><span class="rc-detail-v">{{ $reportCard->position_display }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Average Marks</span><span class="rc-detail-v"><strong>{{ number_format($reportCard->average_marks, 1) }}%</strong></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Performance Table -->
    <div class="rc-card mb-4">
        <div class="rc-card__header rc-card__header--green">
            <i class="bi bi-table"></i> Subject Performance
        </div>
        <div class="rc-card__body p-0">
            <div class="rc-table-wrap">
                <table class="rc-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th class="rc-text-center">Test Avg</th>
                            <th class="rc-text-center">Exam</th>
                            <th class="rc-text-center">Total</th>
                            <th class="rc-text-center">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportCard->subjects as $subject)
                            <tr class="rc-table__row">
                                <td><strong>{{ $subject->subject_name }}</strong></td>
                                <td class="rc-text-center">{{ number_format($subject->test_average, 1) }}</td>
                                <td class="rc-text-center">{{ number_format($subject->exam_mark, 1) }}</td>
                                <td class="rc-text-center"><strong>{{ number_format($subject->total_mark, 1) }}</strong></td>
                                <td class="rc-text-center">
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="rc-text-center py-5 rc-text-muted">No subjects recorded for this report card.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($reportCard->subjects->isNotEmpty())
                        <tfoot>
                            <tr class="rc-table-foot">
                                <td><strong>Total / Average</strong></td>
                                <td class="rc-text-center">{{ number_format($reportCard->subjects->avg('test_average'), 1) }}</td>
                                <td class="rc-text-center">{{ number_format($reportCard->subjects->avg('exam_mark'), 1) }}</td>
                                <td class="rc-text-center"><strong>{{ number_format($reportCard->average_marks, 1) }}</strong></td>
                                <td class="rc-text-center">
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
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="rc-card h-100">
                <div class="rc-card__header rc-card__header--green"><i class="bi bi-bar-chart"></i> Performance Summary</div>
                <div class="rc-card__body">
                    <div class="rc-detail-grid">
                        <div class="rc-detail-row"><span class="rc-detail-k">Subjects</span><span class="rc-detail-v">{{ $reportCard->subjects->count() }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Average</span><span class="rc-detail-v"><strong>{{ number_format($reportCard->average_marks, 1) }}%</strong></span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Total Marks</span><span class="rc-detail-v">{{ number_format($reportCard->total_marks, 1) }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Final Grade</span><span class="rc-detail-v">
                            @php $fg = $reportCard->grade; $fgc = match(true) { in_array($fg, ['A+','A']) => 'rc-badge--green', in_array($fg, ['B+','B']) => 'rc-badge--blue', $fg === 'C' => 'rc-badge--yellow', default => 'rc-badge--red' }; @endphp
                            <span class="rc-badge {{ $fgc }}">{{ $fg }}</span>
                        </span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="rc-card h-100">
                <div class="rc-card__header rc-card__header--blue"><i class="bi bi-trophy"></i> Class Standing</div>
                <div class="rc-card__body">
                    <div class="rc-detail-grid">
                        <div class="rc-detail-row"><span class="rc-detail-k">Position</span><span class="rc-detail-v">{{ $reportCard->position_display ?? 'N/A' }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Total Students</span><span class="rc-detail-v">{{ $reportCard->total_students ?? '—' }}</span></div>
                        <div class="rc-detail-row"><span class="rc-detail-k">Year / Term</span><span class="rc-detail-v">{{ $reportCard->academicYear->year }} · {{ $reportCard->term?->name ?? '—' }}</span></div>
                        @if($reportCard->approvedBy)
                            <div class="rc-detail-row"><span class="rc-detail-k">Approved By</span><span class="rc-detail-v">{{ $reportCard->approvedBy->name ?? '—' }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remarks -->
    @if($reportCard->remarks)
    <div class="rc-card mb-4">
        <div class="rc-card__header rc-card__header--blue"><i class="bi bi-chat-left-text"></i> Remarks</div>
        <div class="rc-card__body">
            {{ $reportCard->remarks }}
        </div>
    </div>
    @endif

</div>
@endsection
