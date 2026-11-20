@extends('staff.partials.sidebar_sec')

@section('title', 'Generated Student Cards')

@section('content')
<div class="container-fluid py-4 student-cards-page">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="eyebrow mb-1">Student records</p>
            <h1 class="page-title mb-1">Generated Student Cards</h1>
            <p class="page-subtitle mb-0">Review, print, and download student ID cards.</p>
        </div>
        <a href="{{ route('staff.students.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2" aria-hidden="true"></i>Back to Students
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="summary-card">
                <span class="summary-icon"><i class="fas fa-id-card" aria-hidden="true"></i></span>
                <div><div class="summary-label">Total cards</div><div class="summary-value">{{ $cards->total() }}</div></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="summary-card">
                <span class="summary-icon summary-icon-alt"><i class="fas fa-qrcode" aria-hidden="true"></i></span>
                <div><div class="summary-label">QR codes ready on this page</div><div class="summary-value">{{ $cards->getCollection()->whereNotNull('qr_code')->whereNotNull('verification_url')->count() }}</div></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="summary-card">
                <span class="summary-icon summary-icon-slate"><i class="fas fa-calendar" aria-hidden="true"></i></span>
                <div><div class="summary-label">Showing page</div><div class="summary-value">{{ $cards->currentPage() }} <small>of {{ $cards->lastPage() }}</small></div></div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3"><h2 class="h6 fw-semibold mb-0"><i class="fas fa-filter me-2 text-primary" aria-hidden="true"></i>Find cards</h2></div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="searchInput" class="form-label">Search</label>
                    <input type="search" class="form-control" id="searchInput" placeholder="Student name or registration number">
                </div>
                <div class="col-12 col-md-2">
                    <label for="courseFilter" class="form-label">Course</label>
                    <select class="form-select" id="courseFilter">
                        <option value="">All courses</option>
                        @foreach($cards->getCollection()->unique('course')->sortBy('course') as $card)
                            @if($card->course)<option value="{{ $card->course }}">{{ $card->course }}</option>@endif
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="branchFilter" class="form-label">Branch</label>
                    <select class="form-select" id="branchFilter">
                        <option value="">All branches</option>
                        @foreach($cards->getCollection()->unique('branch')->sortBy('branch') as $card)
                            @if($card->branch)<option value="{{ $card->branch }}">{{ $card->branch }}</option>@endif
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="yearFilter" class="form-label">Academic Year</label>
                    <select class="form-select" id="yearFilter">
                        <option value="">All years</option>
                        @foreach($cards->getCollection()->unique('academic_year')->sortBy('academic_year') as $card)
                            @if($card->academic_year)<option value="{{ $card->academic_year }}">{{ $card->academic_year }}</option>@endif
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                        <i class="fas fa-rotate-left me-1" aria-hidden="true"></i>Clear filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-semibold mb-0">Student cards</h2>
            <span class="text-muted small">{{ $cards->count() }} shown / {{ $cards->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="cardsTable">
                <thead class="table-light">
                    <tr><th>Student</th><th>Registration Number</th><th>Course</th><th>Branch</th><th>Academic Year</th><th>Issued</th><th>QR</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($cards as $card)
                        <tr class="card-row"
                            data-name="{{ strtolower($card->student->full_name ?? '') }}"
                            data-reg="{{ strtolower($card->reg_number ?? '') }}"
                            data-course="{{ $card->course ?? '' }}"
                            data-branch="{{ $card->branch ?? '' }}"
                            data-year="{{ $card->academic_year ?? '' }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="student-avatar">{{ strtoupper(substr($card->student->full_name ?? '?', 0, 1)) }}</span>
                                    <span class="fw-semibold">{{ $card->student->full_name ?? 'Unknown student' }}</span>
                                </div>
                            </td>
                            <td class="font-monospace">{{ $card->reg_number ?? '—' }}</td>
                            <td>{{ $card->course ?: '—' }}</td>
                            <td>{{ $card->branch ?: '—' }}</td>
                            <td>{{ $card->academic_year ?: '—' }}</td>
                            <td class="text-nowrap">{{ $card->generated_at ? \Carbon\Carbon::parse($card->generated_at)->format('M d, Y') : '—' }}</td>
                            <td>
                                @if($card->qr_code && $card->verification_url)
                                    <span class="badge rounded-pill text-bg-success">Ready</span>
                                @else
                                    <span class="badge rounded-pill text-bg-secondary">Unavailable</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Student card actions">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#previewCardModal{{ $card->id }}" aria-label="Preview card" title="Preview"><i class="fas fa-eye" aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-outline-primary" onclick="downloadStudentCardImage({{ $card->id }}, this.dataset.studentName)" data-student-name="{{ $card->student->full_name ?? 'student' }}" aria-label="Download card" title="Download"><i class="fas fa-download" aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-outline-primary" onclick="printStudentCard({{ $card->id }})" aria-label="Print card" title="Print"><i class="fas fa-print" aria-hidden="true"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-id-card fa-2x d-block mb-2" aria-hidden="true"></i><div class="fw-semibold">No student cards yet</div><small>Generate a card from the student record after the card fee is paid.</small></td></tr>
                    @endforelse
                    <tr id="noFilterResults" class="d-none"><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-magnifying-glass fa-2x d-block mb-2" aria-hidden="true"></i>No cards match these filters.</td></tr>
                </tbody>
            </table>
        </div>
        @if($cards->hasPages())
            <div class="card-footer bg-white">{{ $cards->links() }}</div>
        @endif
    </div>
</div>

@foreach($cards as $card)
    <div class="modal fade" id="previewCardModal{{ $card->id }}" tabindex="-1" aria-labelledby="previewCardModalLabel{{ $card->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <div><h2 class="modal-title h5 fw-semibold" id="previewCardModalLabel{{ $card->id }}">Student ID Card</h2><p class="text-muted small mb-0">Review the card before printing or downloading.</p></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <div id="studentCardContent{{ $card->id }}" class="card-canvas">
                        <article class="student-id-card">
                            <header class="id-card-header">
                                <img src="{{ $logoUrl }}" alt="" class="id-card-logo">
                                <div><div class="id-card-school">{{ $schoolName }}</div><div class="id-card-type">Student identity card</div></div>
                                <span class="id-card-year">{{ $card->academic_year ?: 'Student' }}</span>
                            </header>
                            <div class="id-card-body">
                                <div class="id-card-photo">
                                    @if($card->passport_photo)
                                        <img src="data:image/jpeg;base64,{{ $card->passport_photo }}" alt="Student photo">
                                    @elseif($card->student && $card->student->passport_photo)
                                        <img src="data:image/jpeg;base64,{{ $card->student->passport_photo }}" alt="Student photo">
                                    @else
                                        <span>{{ strtoupper(substr($card->student->full_name ?? '?', 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="id-card-info">
                                    <h3>{{ $card->student->full_name ?? 'Unknown student' }}</h3>
                                    <div class="id-card-number">{{ $card->reg_number ?? '—' }}</div>
                                    <dl>
                                        <div><dt>Course</dt><dd>{{ $card->course ?: '—' }}</dd></div>
                                        <div><dt>Branch</dt><dd>{{ $card->branch ?: '—' }}</dd></div>
                                    </dl>
                                </div>
                                <aside class="id-card-qr">
                                    @if($card->qr_code && $card->verification_url)
                                        {!! $card->qr_code !!}
                                        <span>Scan to verify</span>
                                    @else
                                        <span class="qr-unavailable">QR unavailable</span>
                                    @endif
                                </aside>
                            </div>
                            <footer class="id-card-footer">
                                <span>Issued {{ $card->generated_at ? \Carbon\Carbon::parse($card->generated_at)->format('M Y') : '—' }}</span>
                                <span>Non-transferable</span>
                            </footer>
                        </article>
                    </div>
                    @if($card->verification_url)
                        <div class="verification-link mt-3">
                            <span class="small text-muted">Verification link</span>
                            <a href="{{ $card->verification_url }}" target="_blank" rel="noopener noreferrer" class="text-break">{{ $card->verification_url }}</a>
                        </div>
                    @else
                        <div class="alert alert-warning mt-3 mb-0">This card does not have a verification link yet. Reopen or regenerate the card to refresh its QR code.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary" onclick="printStudentCard({{ $card->id }})"><i class="fas fa-print me-2" aria-hidden="true"></i>Print</button>
                    <button type="button" class="btn btn-primary" onclick="downloadStudentCardImage({{ $card->id }}, this.dataset.studentName)" data-student-name="{{ $card->student->full_name ?? 'student' }}"><i class="fas fa-download me-2" aria-hidden="true"></i>Download</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .student-cards-page {
        --cards-teal: #0f766e;
        --cards-ink: #173247;
        --cards-muted: #64748b;
        --cards-border: #e2e8f0;
        font-family: 'Poppins', sans-serif;
        color: var(--cards-ink);
    }
    .student-cards-page .eyebrow { color: var(--cards-teal); font-size: .75rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
    .student-cards-page .page-title { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; }
    .student-cards-page .page-subtitle, .student-cards-page .summary-label { color: var(--cards-muted); }
    .student-cards-page .card { border-radius: 14px; border-color: var(--cards-border); box-shadow: 0 5px 18px rgba(15, 23, 42, .05) !important; }
    .student-cards-page .card-header { border-color: var(--cards-border); }
    .student-cards-page .btn-primary { background: var(--cards-teal); border-color: var(--cards-teal); }
    .student-cards-page .btn-primary:hover { background: #115e59; border-color: #115e59; }
    .student-cards-page .btn-outline-primary { color: var(--cards-teal); border-color: var(--cards-teal); }
    .student-cards-page .btn-outline-primary:hover { color: #fff; background: var(--cards-teal); }
    .student-cards-page .form-control:focus, .student-cards-page .form-select:focus { border-color: #5aa69f; box-shadow: 0 0 0 .2rem rgba(15, 118, 110, .15); }
    .student-cards-page .summary-card { display: flex; align-items: center; gap: 14px; height: 100%; padding: 18px; border: 1px solid var(--cards-border); border-radius: 14px; background: #fff; }
    .student-cards-page .summary-icon { display: grid; place-items: center; width: 44px; height: 44px; border-radius: 12px; color: var(--cards-teal); background: #e9f5f3; font-size: 1.15rem; }
    .student-cards-page .summary-icon-alt { color: #27669a; background: #edf4fa; }
    .student-cards-page .summary-icon-slate { color: #475569; background: #f1f5f9; }
    .student-cards-page .summary-value { color: var(--cards-ink); font-size: 1.35rem; font-weight: 700; }
    .student-cards-page .summary-value small { color: var(--cards-muted); font-size: .8rem; font-weight: 500; }
    .student-cards-page .student-avatar { display: grid; place-items: center; width: 34px; height: 34px; flex: 0 0 34px; border-radius: 50%; color: #fff; background: var(--cards-teal); font-size: .9rem; font-weight: 700; }
    .student-cards-page .table thead th { color: #475569; font-size: .75rem; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
    .card-canvas { max-width: 100%; }
    .student-id-card {
        position: relative;
        display: flex;
        flex-direction: column;
        width: min(100%, 540px);
        aspect-ratio: 540 / 340;
        margin: 0 auto;
        overflow: hidden;
        border: 1px solid #d5e0e3;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .1);
        color: #173247;
    }
    .id-card-header { display: flex; align-items: center; gap: 12px; min-height: 70px; padding: 12px 18px; color: #fff; background: #124e55; }
    .id-card-logo { width: 42px; height: 42px; flex: 0 0 42px; padding: 3px; border-radius: 8px; background: #fff; object-fit: contain; }
    .id-card-school { max-width: 300px; font-size: clamp(.8rem, 2.5vw, 1rem); font-weight: 700; line-height: 1.2; }
    .id-card-type { margin-top: 4px; color: #d7edeb; font-size: .68rem; letter-spacing: .08em; text-transform: uppercase; }
    .id-card-year { margin-left: auto; padding: 5px 8px; border: 1px solid rgba(255,255,255,.35); border-radius: 6px; font-size: .7rem; white-space: nowrap; }
    .id-card-body { display: flex; align-items: center; gap: 14px; flex: 1; min-height: 0; padding: 14px 16px; }
    .id-card-photo { display: grid; place-items: center; width: 100px; height: 128px; flex: 0 0 100px; overflow: hidden; border: 1px solid #dce5e8; border-radius: 8px; background: #f1f5f7; color: var(--cards-teal); font-size: 2rem; font-weight: 700; }
    .id-card-photo img { width: 100%; height: 100%; object-fit: cover; }
    .id-card-info { min-width: 0; flex: 1; }
    .id-card-info h3 { overflow: hidden; margin: 0 0 6px; font-size: clamp(.8rem, 2.6vw, 1.1rem); font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
    .id-card-number { margin-bottom: 12px; color: var(--cards-teal); font-size: .78rem; font-weight: 700; }
    .id-card-info dl { display: grid; gap: 8px; margin: 0; }
    .id-card-info dl div { min-width: 0; }
    .id-card-info dt { color: var(--cards-muted); font-size: .6rem; letter-spacing: .06em; text-transform: uppercase; }
    .id-card-info dd { overflow: hidden; margin: 1px 0 0; font-size: .72rem; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
    .id-card-qr { display: flex; flex: 0 0 82px; flex-direction: column; align-items: center; gap: 4px; }
    .id-card-qr svg { display: block; width: 72px; height: 72px; }
    .id-card-qr span { color: var(--cards-muted); font-size: .56rem; text-align: center; }
    .id-card-qr .qr-unavailable { display: grid; place-items: center; width: 72px; height: 72px; border: 1px dashed #cbd5e1; }
    .id-card-footer { display: flex; justify-content: space-between; padding: 7px 16px; border-top: 1px solid #e8eef0; color: var(--cards-muted); font-size: .6rem; }
    .verification-link { display: grid; gap: 4px; }
    @media (max-width: 575.98px) {
        .student-cards-page .table { min-width: 980px; }
        .student-id-card { border-radius: 10px; }
        .id-card-header { min-height: 54px; padding: 8px 10px; gap: 7px; }
        .id-card-logo { width: 30px; height: 30px; flex-basis: 30px; }
        .id-card-body { gap: 8px; padding: 8px; }
        .id-card-photo { width: 68px; height: 92px; flex-basis: 68px; }
        .id-card-qr { flex-basis: 60px; }
        .id-card-qr svg, .id-card-qr .qr-unavailable { width: 54px; height: 54px; }
        .id-card-footer { padding: 5px 8px; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('searchInput');
    const course = document.getElementById('courseFilter');
    const branch = document.getElementById('branchFilter');
    const year = document.getElementById('yearFilter');
    const rows = Array.from(document.querySelectorAll('#cardsTable .card-row'));
    const noResults = document.getElementById('noFilterResults');

    function filterRows() {
        const term = search.value.trim().toLowerCase();
        let visibleCount = 0;
        rows.forEach(function (row) {
            const visible = (!term || row.dataset.name.includes(term) || row.dataset.reg.includes(term))
                && (!course.value || row.dataset.course === course.value)
                && (!branch.value || row.dataset.branch === branch.value)
                && (!year.value || row.dataset.year === year.value);
            row.classList.toggle('d-none', !visible);
            if (visible) visibleCount++;
        });
        noResults.classList.toggle('d-none', visibleCount > 0 || rows.length === 0);
    }

    search.addEventListener('input', filterRows);
    [course, branch, year].forEach(function (select) {
        select.addEventListener('change', filterRows);
    });
    document.getElementById('clearFilters').addEventListener('click', function () {
        search.value = '';
        course.value = '';
        branch.value = '';
        year.value = '';
        filterRows();
    });

    const previewId = new URLSearchParams(window.location.search).get('preview');
    const previewModal = previewId && document.getElementById('previewCardModal' + previewId);
    if (previewModal) {
        bootstrap.Modal.getOrCreateInstance(previewModal).show();
        const cleanUrl = new URL(window.location.href);
        cleanUrl.searchParams.delete('preview');
        window.history.replaceState({}, '', cleanUrl);
    }
});

function printStudentCard(cardId) {
    const content = document.getElementById('studentCardContent' + cardId);
    const printWindow = window.open('', '_blank', 'width=850,height=650');
    if (!printWindow) {
        alert('Allow pop-ups to print the student card.');
        return;
    }

    printWindow.document.write(`<!doctype html>
        <html lang="en"><head><meta charset="utf-8"><title>Student ID Card</title>
        <style>
            * { box-sizing: border-box; }
            body { margin: 0; padding: 18mm; font-family: Arial, sans-serif; }
            .student-id-card { width: 85.6mm; height: 54mm; overflow: hidden; border: .3mm solid #cbd5e1; border-radius: 3mm; color: #173247; }
            .id-card-header { display: flex; align-items: center; gap: 2mm; height: 11mm; padding: 2mm 3mm; color: #fff; background: #124e55; }
            .id-card-logo { width: 7mm; height: 7mm; object-fit: contain; background: #fff; border-radius: 1mm; }
            .id-card-school { font-size: 8pt; font-weight: bold; }
            .id-card-type { margin-top: 1mm; color: #d7edeb; font-size: 5pt; text-transform: uppercase; }
            .id-card-year { margin-left: auto; font-size: 5pt; }
            .id-card-body { display: flex; align-items: center; gap: 2mm; height: 37mm; padding: 2mm 3mm; }
            .id-card-photo { display: grid; place-items: center; width: 18mm; height: 25mm; flex: 0 0 18mm; overflow: hidden; border: .3mm solid #dce5e8; font-size: 18pt; font-weight: bold; }
            .id-card-photo img { width: 100%; height: 100%; object-fit: cover; }
            .id-card-info { min-width: 0; flex: 1; }
            .id-card-info h3 { overflow: hidden; margin: 0 0 1mm; font-size: 9pt; text-overflow: ellipsis; white-space: nowrap; }
            .id-card-number { margin-bottom: 2mm; color: #0f766e; font-size: 6pt; font-weight: bold; }
            .id-card-info dl { display: grid; gap: 1mm; margin: 0; }
            .id-card-info dt { color: #64748b; font-size: 4.5pt; text-transform: uppercase; }
            .id-card-info dd { overflow: hidden; margin: 0; font-size: 6pt; font-weight: bold; text-overflow: ellipsis; white-space: nowrap; }
            .id-card-qr { display: flex; width: 13mm; flex: 0 0 13mm; flex-direction: column; align-items: center; }
            .id-card-qr svg { width: 12mm; height: 12mm; }
            .id-card-qr span { font-size: 4pt; text-align: center; }
            .id-card-footer { display: flex; justify-content: space-between; height: 6mm; padding: 1.5mm 3mm; border-top: .2mm solid #e8eef0; color: #64748b; font-size: 5pt; }
            @page { size: 95mm 70mm; margin: 0; }
            @media print { body { padding: 5mm; } }
        </style></head><body>${content.innerHTML}</body></html>`);
    printWindow.document.close();
    printWindow.focus();
    printWindow.addEventListener('afterprint', function () { printWindow.close(); }, { once: true });
    printWindow.setTimeout(function () { printWindow.print(); }, 500);
}

function downloadStudentCardImage(cardId, studentName) {
    const element = document.getElementById('studentCardContent' + cardId);
    html2canvas(element, { scale: 2, useCORS: true, backgroundColor: '#ffffff' })
        .then(function (canvas) {
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'student_card_' + String(studentName || 'student').trim().replace(/[^a-z0-9_-]+/gi, '_') + '.png';
            link.click();
        })
        .catch(function (error) {
            console.error('Student card image generation failed:', error);
            alert('Unable to download this student card. Please try again.');
        });
}
</script>
@endpush
