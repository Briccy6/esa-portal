@extends('students.app')

@section('title', 'Report Card - ' . $reportCard->student->full_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <div>
            <h1 class="h4 mb-1">
                <i class="bi bi-file-earmark-text me-2"></i>Report Card
            </h1>
            <p class="text-muted mb-0">
                {{ $reportCard->student->full_name }} -
                {{ $reportCard->academicYear->year ?? 'N/A' }} -
                {{ $reportCard->term->name ?? 'N/A' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('student.report-cards.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
            <a href="{{ route('student.report-cards.download', $reportCard) }}" class="btn btn-success">
                <i class="bi bi-download me-1"></i>Download PDF
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>

    <div class="print-area">
        @include('admin.report-cards.partials.print-assessment-inner', compact('reportCard', 'schoolInfo', 'studentPhotoBase64', 'studentPhotoMime'))
    </div>
</div>

<style>
    .print-area {
        width: 100%;
        max-width: 170mm;
        margin: 0 auto;
        padding: 0;
        box-sizing: border-box;
        border: 2px solid #000;
        background: #fff;
    }

    @media print {
        .no-print,
        .top-navbar,
        .sidebar {
            display: none !important;
        }

        .main-content {
            padding: 0 !important;
        }

        .content-wrapper {
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }

        .print-area {
            margin: 0 auto;
        }
    }
</style>
@endsection
