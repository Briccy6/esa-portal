@extends('staff.partials.sidebar_sec')

@section('content')
@php
    $contactInfo = env('ESA_CONTACT_INFO', 'Contact us at: info@excellentservantacademy.com, Tel: +250 788 525 247');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
    $schoolName = 'Excellent Servant Academy';
    $timestamp = now()->format('Y-m-d H:i');
    $generatedBy = auth()->user()->name ?? 'System';
   
    $systemName = $systemName ?? 'ESA E-Learning System';
    $systemLogo = \App\Helpers\LogoHelper::getLogoUrl();
@endphp
<link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getFaviconUrl() }}">
<h2 class="mb-2">Generated Student Cards</h2>
<p class="text-muted mb-3">Manage and view student identification cards</p>

<!-- Search and Filter Section -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label">Search</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" id="searchInput" placeholder="Search by name or reg number...">
        </div>
    </div>
    <div class="col-md-2">
        <label class="form-label">Course</label>
        <select class="form-select" id="courseFilter">
            <option value="">All Courses</option>
            @foreach($cards->unique('course') as $card)
                @if($card->course)
                    <option value="{{ $card->course }}">{{ $card->course }}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Branch</label>
        <select class="form-select" id="branchFilter">
            <option value="">All Branches</option>
            @foreach($cards->unique('branch') as $card)
                @if($card->branch)
                    <option value="{{ $card->branch }}">{{ $card->branch }}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Academic Year</label>
        <select class="form-select" id="yearFilter">
            <option value="">All Years</option>
            @foreach($cards->unique('academic_year') as $card)
                @php
                    $year = $card->academic_year ?? (config('app.academic_year') ?? date('Y'));
                @endphp
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <div class="d-grid">
            <button class="btn btn-outline-secondary" onclick="clearFilters()">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="table-responsive">
    <table class="table table-hover align-middle" id="cardsTable">
        <thead class="table-dark">
            <tr>
                <th width="5%">#</th>
                <th width="8%">Photo</th>
                <th width="20%">Student Reg No</th>
                <th width="15%">Student Name</th>
                <th width="15%">Course</th>
                <th width="10%">Branch</th>
                <th width="10%">Year</th>
                <th width="12%">Generated At</th>
                <th width="15%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cards as $card)
                <tr class="card-row" 
                    data-name="{{ strtolower($card->student->full_name ?? '') }}" 
                    data-reg="{{ strtolower($card->reg_number) }}" 
                    data-course="{{ $card->course }}" 
                    data-branch="{{ $card->branch }}" 
                    data-year="{{ $card->academic_year ?? (config('app.academic_year') ?? date('Y')) }}">
                    <td>
                        <span class="badge bg-primary">
                            {{ $loop->iteration + ($cards->currentPage() - 1) * $cards->perPage() }}
                        </span>
                    </td>
                    <td>
                        <div class="position-relative">
                            @if($card->passport_photo)
                                <img src="data:image/jpeg;base64,{{ $card->passport_photo }}" alt="Passport" class="rounded shadow-sm" style="width:50px;height:50px;object-fit:cover;">
                            @elseif($card->student && $card->student->passport_photo)
                                <img src="data:image/jpeg;base64,{{ $card->student->passport_photo }}" alt="Passport" class="rounded shadow-sm" style="width:50px;height:50px;object-fit:cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-info text-dark fw-bold">{{ $card->reg_number }}</span>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $card->student->full_name ?? '-' }}</div>
                        <small class="text-muted">{{ $card->card_generated_by }}</small>
                    </td>
                    <td>{{ $card->course }}</td>
                    <td>
                        <span class="badge bg-secondary">{{ $card->branch }}</span>
                    </td>
                    <td>{{ $card->academic_year ?? (config('app.academic_year') ?? date('Y')) }}</td>
                    <td>
                        <small class="text-muted">
                            @if($card->generated_at)
                                {{ \Carbon\Carbon::parse($card->generated_at)->format('M d, Y') }}<br>
                                {{ \Carbon\Carbon::parse($card->generated_at)->format('H:i') }}
                            @else
                                -
                            @endif
                        </small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#previewCardModal{{ $card->id }}" title="Preview">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-success" onclick="downloadStudentCardImage({{ $card->id }}, '{{ addslashes($card->student->full_name ?? 'student') }}')" title="Download Image">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="btn btn-outline-primary" onclick="printStudentCard({{ $card->id }})" title="Print">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="noResultsRow">
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-id-card fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No cards generated yet.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        {{ $cards->links() }}
    </div>
</div>

<!-- Student Card Preview Modals -->
@foreach($cards as $card)
<div class="modal fade" id="previewCardModal{{ $card->id }}" tabindex="-1" aria-labelledby="previewCardModalLabel{{ $card->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewCardModalLabel{{ $card->id }}">
                    <i class="fas fa-id-card me-2"></i>Student ID Card Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                <div id="studentCardContent{{ $card->id }}" class="card-print-area">
                    <div class="student-card" style="
                        width: 540px; 
                        height: 340px; 
                        margin: 0 auto;
                        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                        border-radius: 20px;
                        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                        position: relative;
                        overflow: hidden;
                        border: 2px solid #e2e8f0;
                    ">
                        <!-- Header Section -->
                        <div style="
                            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
                            color: white;
                            padding: 15px 25px;
                            position: relative;
                        ">
                            @if(\App\Helpers\LogoHelper::hasLogo())
                                <img src="{{ $logoUrl }}" alt="Logo" style="
                                    position: absolute;
                                    right: 15px;
                                    top: 50%;
                                    transform: translateY(-50%);
                                    width: 50px;
                                    height: 50px;
                                   ">
                            @endif
                            <div style="display: flex; align-items: center;">
                                <div>
                                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; letter-spacing: 0.5px;">
                                        {{ config('app.name') }}
                                    </h3>
                                    <p style="margin: 2px 0 0 0; font-size: 12px; opacity: 0.9;">
                                        {{ config('app.school_address') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Main Content Area -->
                        <div style="
                            display: flex;
                            padding: 25px;
                            height: calc(100% - 80px);
                            gap: 25px;
                        ">
                            <!-- Left Side - Photo -->
                            <div style="flex-shrink: 0;">
                                <div style="
                                    width: 120px;
                                    height: 150px;
                                    border-radius: 15px;
                                    overflow: hidden;
                                    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                                    border: 3px solid #e2e8f0;
                                    background: #f8fafc;
                                ">
                                    @if($card->passport_photo)
                                        <img src="data:image/jpeg;base64,{{ $card->passport_photo }}" alt="Student Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                    @elseif($card->student && $card->student->passport_photo)
                                        <img src="data:image/jpeg;base64,{{ $card->student->passport_photo }}" alt="Student Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- Right Side - Student Information -->
                            <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="margin-bottom: 20px;">
                                        <h2 style="
                                            margin: 0 0 5px 0;
                                            font-size: 24px;
                                            font-weight: 700;
                                            color: #1e293b;
                                            letter-spacing: 0.5px;
                                        ">
                                            {{ $card->student->full_name ?? 'N/A' }}
                                        </h2>
                                        <div style="
                                            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
                                            height: 3px;
                                            width: 60px;
                                            border-radius: 2px;
                                        "></div>
                                    </div>
                                    <div style="space-y: 8px;">
                                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                            <div style="
                                                width: 35px;
                                                height: 35px;
                                                background: linear-gradient(135deg, #dbeafe, #bfdbfe);
                                                border-radius: 8px;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                margin-right: 12px;
                                            ">
                                                <i class="fas fa-hashtag" style="color: #3b82f6; font-size: 14px;"></i>
                                            </div>
                                            <div>
                                                <div style="font-size: 11px; color: #64748b; font-weight: 500;">REG. NUMBER</div>
                                                <div style="font-size: 16px; font-weight: 700; color: #1e293b;">{{ $card->reg_number }}</div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                            <div style="
                                                width: 35px;
                                                height: 35px;
                                                background: linear-gradient(135deg, #ecfdf5, #d1fae5);
                                                border-radius: 8px;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                margin-right: 12px;
                                            ">
                                                <i class="fas fa-graduation-cap" style="color: #059669; font-size: 14px;"></i>
                                            </div>
                                            <div>
                                                <div style="font-size: 11px; color: #64748b; font-weight: 500;">COURSE</div>
                                                <div style="font-size: 14px; font-weight: 600; color: #1e293b;">{{ $card->course }}</div>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 20px;">
                                            <div style="display: flex; align-items: center;">
                                                <div style="
                                                    width: 30px;
                                                    height: 30px;
                                                    background: linear-gradient(135deg, #fef3c7, #fde68a);
                                                    border-radius: 6px;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    margin-right: 8px;
                                                ">
                                                    <i class="fas fa-map-marker-alt" style="color: #d97706; font-size: 12px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 10px; color: #64748b; font-weight: 500;">BRANCH</div>
                                                    <div style="font-size: 13px; font-weight: 600; color: #1e293b;">{{ $card->branch }}</div>
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center;">
                                                <div style="
                                                    width: 30px;
                                                    height: 30px;
                                                    background: linear-gradient(135deg, #fce7f3, #fbcfe8);
                                                    border-radius: 6px;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    margin-right: 8px;
                                                ">
                                                    <i class="fas fa-calendar" style="color: #be185d; font-size: 12px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 10px; color: #64748b; font-weight: 500;">YEAR</div>
                                                    <div style="font-size: 13px; font-weight: 600; color: #1e293b;">{{ $card->academic_year ?? (config('app.academic_year') ?? date('Y')) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Contact Info -->
                                <div style="
                                    background: rgba(59, 130, 246, 0.05);
                                    padding: 12px;
                                    border-radius: 10px;
                                    border-left: 4px solid #3b82f6;
                                ">
                                    <div style="font-size: 11px; color: #3b82f6; font-weight: 600; margin-bottom: 6px;">CONTACT INFORMATION</div>
                                    <div style="font-size: 12px; color: #475569; line-height: 1.4;">
                                        <div><i class="fas fa-envelope" style="width: 14px;"></i> {{ config('app.school_email') }}</div>
                                        <div><i class="fas fa-phone" style="width: 14px;"></i> {{ config('app.school_contact') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Decorative Elements -->
                        <div style="
                            position: absolute;
                            top: -50px;
                            right: -50px;
                            width: 100px;
                            height: 100px;
                            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
                            border-radius: 50%;
                        "></div>
                        <div style="
                            position: absolute;
                            bottom: -30px;
                            left: -30px;
                            width: 60px;
                            height: 60px;
                            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(59, 130, 246, 0.1));
                            border-radius: 50%;
                        "></div>
                    </div>
                    <!-- QR Code Section -->
                    <div style="
                        position: absolute;
                        bottom: 25px;
                        right: 25px;
                        background: white;
                        border-radius: 10px;
                        padding: 10px;
                        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    ">
                        <div style="text-align: center;">
                            <div style="font-size: 10px; color: #64748b; font-weight: 600; margin-bottom: 5px;">SCAN FOR VERIFICATION</div>
                            @if($card->qr_code)
                                <div style="width: 80px; height: 80px; border: 1px solid #e2e8f0; border-radius: 4px; overflow: hidden;">
                                    {!! $card->qr_code !!}
                                </div>
                            @else
                                <div style="width: 80px; height: 80px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; color: #64748b;">
                                    <i class="fas fa-qrcode" style="font-size: 40px;"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Important Notice -->
                    <div style="
                        margin-top: 20px;
                        background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%);
                        border: 2px solid #e879f9;
                        border-radius: 15px;
                        padding: 20px;
                        box-shadow: 0 10px 25px rgba(232, 121, 249, 0.1);
                    ">
                        <div style="display: flex; align-items: flex-start; gap: 15px;">
                            <div style="
                                width: 40px;
                                height: 40px;
                                background: linear-gradient(135deg, #e879f9, #c084fc);
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-shrink: 0;
                            ">
                                <i class="fas fa-exclamation-triangle" style="color: white; font-size: 18px;"></i>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 8px 0; color: #7c3aed; font-size: 16px; font-weight: 700;">Important Notice</h4>
                                <p style="margin: 0 0 10px 0; color: #6b21a8; font-size: 14px; line-height: 1.5;">
                                    <strong>If Found:</strong> Please return this card to {{ config('app.name') }} administration office immediately.
                                </p>
                                <p style="margin: 0; color: #6b21a8; font-size: 14px; line-height: 1.5;">
                                    <strong>Warning:</strong> This card is institutional property. Unauthorized use, modification, or lending is strictly prohibited and may result in disciplinary action.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printStudentCard({{ $card->id }})">
                    <i class="fas fa-print me-1"></i> Print Card
                </button>
                <button type="button" class="btn btn-success" onclick="downloadStudentCardImage({{ $card->id }}, '{{ addslashes($card->student->full_name ?? 'student') }}')">
                    <i class="fas fa-download me-1"></i> Download Image
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
    .student-card {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card-row {
        transition: all 0.3s ease;
    }
    .card-row:hover {
        background-color: rgba(59, 130, 246, 0.05);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .btn-group-sm .btn {
        border-radius: 6px;
        margin: 0 1px;
    }
    .table-dark th {
        border: none;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .badge {
        font-size: 11px;
        padding: 6px 10px;
    }
    @media print {
        .student-card {
            width: 540px !important;
            height: 340px !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: 2px solid #000 !important;
            page-break-inside: avoid;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const courseFilter = document.getElementById('courseFilter');
    const branchFilter = document.getElementById('branchFilter');
    const yearFilter = document.getElementById('yearFilter');
    const table = document.getElementById('cardsTable');
    const rows = table.querySelectorAll('.card-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const courseValue = courseFilter.value;
        const branchValue = branchFilter.value;
        const yearValue = yearFilter.value;
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.dataset.name;
            const reg = row.dataset.reg;
            const course = row.dataset.course;
            const branch = row.dataset.branch;
            const year = row.dataset.year;

            const matchesSearch = name.includes(searchTerm) || reg.includes(searchTerm);
            const matchesCourse = !courseValue || course === courseValue;
            const matchesBranch = !branchValue || branch === branchValue;
            const matchesYear = !yearValue || year === yearValue;

            const shouldShow = matchesSearch && matchesCourse && matchesBranch && matchesYear;
            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleCount++;
        });

        const noResultsRow = document.getElementById('noResultsRow');
        if (noResultsRow) {
            if (visibleCount === 0 && rows.length > 0) {
                const tbody = table.querySelector('tbody');
                const tempRow = document.createElement('tr');
                tempRow.id = 'tempNoResults';
                tempRow.innerHTML = '<td colspan="9" class="text-center py-4"><i class="fas fa-search text-muted"></i><br>No results found matching your criteria.</td>';
                tbody.appendChild(tempRow);
            } else {
                const tempRow = document.getElementById('tempNoResults');
                if (tempRow) tempRow.remove();
            }
        }
    }

    searchInput.addEventListener('input', filterTable);
    courseFilter.addEventListener('change', filterTable);
    branchFilter.addEventListener('change', filterTable);
    yearFilter.addEventListener('change', filterTable);

    const previewCardId = new URLSearchParams(window.location.search).get('preview');
    const previewModal = previewCardId && document.getElementById(`previewCardModal${previewCardId}`);
    if (previewModal) {
        bootstrap.Modal.getOrCreateInstance(previewModal).show();
        const url = new URL(window.location.href);
        url.searchParams.delete('preview');
        window.history.replaceState({}, '', url);
    }
});

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('courseFilter').value = '';
    document.getElementById('branchFilter').value = '';
    document.getElementById('yearFilter').value = '';
    const event = new Event('input');
    document.getElementById('searchInput').dispatchEvent(event);
}

function printStudentCard(cardId) {
    const content = document.getElementById('studentCardContent' + cardId).innerHTML;
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Student ID Card - Print</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: white; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
                .student-card { width: 540px !important; height: 340px !important; margin: 0 !important; box-shadow: none !important; border: 2px solid #333 !important; page-break-inside: avoid; }
                @media print { body { padding: 0; } .student-card { border: 2px solid #000 !important; } }
            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 1000);
}

function downloadStudentCardImage(cardId, studentName) {
    const element = document.getElementById('studentCardContent' + cardId);
    
    html2canvas(element, {
        scale: 2,
        useCORS: true,
        allowTaint: true,
        backgroundColor: '#ffffff',
        logging: false
    }).then(canvas => {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = 'student_card_' + studentName.replace(/\s+/g, '_') + '.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    }).catch(err => {
        console.error('Error generating image:', err);
        alert('Error generating image. Please try again.');
    });
}
</script>
@endpush
