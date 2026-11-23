@php
    $studentName = strtoupper($card->student->full_name ?? 'Unknown Student');
    $regNumber = $card->reg_number ?: ($card->student->reg_number ?? '—');
    $course = $card->course ?: ($card->student->course->course_name ?? '—');
    $branch = $card->branch ?: ($card->student->branch->name ?? '—');
    $academicYear = $card->academic_year ?: ($card->student->academicYear->year ?? '—');
    $shift = strtolower((string) ($card->student->shift ?? ''));
    $shiftLabel = $shift === 'morning' ? 'Day' : ($shift !== '' ? ucfirst($shift) : 'Not recorded');
    $academicYearRecord = $card->student->academicYear ?? null;
    $validUntil = $academicYearRecord && $academicYearRecord->expiry_date
        ? \Carbon\Carbon::parse($academicYearRecord->expiry_date)->format('M d, Y')
        : 'End of Academic Year';
    $photoValue = $card->passport_photo ?: ($card->student->passport_photo ?? null);
    $studentPhotoUrl = null;
    if ($photoValue) {
        if (str_starts_with($photoValue, 'data:image/')) {
            $studentPhotoUrl = $photoValue;
        } elseif (strlen($photoValue) >= 500) {
            $studentPhotoUrl = 'data:image/jpeg;base64,' . $photoValue;
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($photoValue)) {
            $mime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($photoValue) ?: 'image/jpeg';
            $studentPhotoUrl = 'data:' . $mime . ';base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($photoValue));
        } elseif (filter_var($photoValue, FILTER_VALIDATE_URL)) {
            $studentPhotoUrl = $photoValue;
        }
    }
    $signatoryName = $schoolInfo->principal_name ?? 'Academic Registrar';
    $signatureUrl = $schoolInfo->principal_signature_url ?? null;
@endphp

@if($side === 'front')
<div id="{{ $idPrefix }}{{ $card->id }}" class="cr80-card cr80-front">
    <div class="front-header">
        <div>
            <div class="school-title">{{ $schoolName }}</div>
            <div class="school-address">{{ $schoolAddress }}</div>
        </div>
        <div class="logo-badge"><img src="{{ $logoUrl ?: \App\Helpers\LogoHelper::getLogoUrl() }}" onerror="this.onerror=null;this.src='{{ \App\Helpers\LogoHelper::getLogoUrl() }}';" alt="Excellent Servant Academy logo"></div>
    </div>
    <div class="front-body">
        <div class="photo-column">
            <div class="photo-frame">
                @if($studentPhotoUrl)
                    <img src="{{ $studentPhotoUrl }}" alt="{{ $studentName }}">
                @else
                    <div class="photo-placeholder">{{ strtoupper(substr($studentName, 0, 1)) }}</div>
                @endif
            </div>
            <div class="id-badge-pill">Student Identity Card</div>
        </div>
        <div class="details-column">
            <div>
                <div class="student-identity">
                    <div class="student-name">{{ $studentName }}</div>
                    <div class="student-role">Student • Official ID</div>
                </div>
                <div class="meta-grid">
                    <div class="meta-item"><span class="meta-label">Reg. Number</span><span class="meta-value mono">{{ $regNumber }}</span></div>
                    <div class="meta-item"><span class="meta-label">Academic Year</span><span class="meta-value">{{ $academicYear }}</span></div>
                    <div class="meta-item"><span class="meta-label">Course / Combination</span><span class="meta-value">{{ $course }}</span></div>
                    <div class="meta-item"><span class="meta-label">Branch</span><span class="meta-value">{{ $branch }}</span></div>
                    <div class="meta-item"><span class="meta-label">Shift</span><span class="meta-value">{{ $shiftLabel }}</span></div>
                </div>
            </div>
            <div class="front-contact-strip">
                <span><strong>Phone:</strong> {{ $card->student->phone ?: 'Not provided' }}</span>
                <span><strong>Valid Until:</strong> {{ $validUntil }}</span>
            </div>
        </div>
    </div>
</div>
@else
<div id="{{ $idPrefix }}{{ $card->id }}" class="cr80-card cr80-back">
    <div class="back-header">
        <div class="title">EXCELLENT SERVANT ACADEMY — STUDENT CARD</div>
        <div class="sub">{{ $regNumber }}</div>
    </div>
    <div class="back-body">
        <div class="notice-box">
            <div>
                <div class="notice-title">Important Notice</div>
                <div class="notice-text">
                    <p><strong>If Found:</strong> Please return this card to Excellent Servant Academy administration office.</p>
                    <p><strong>Warning:</strong> This card is institutional property. Unauthorized use or modification is prohibited.</p>
                </div>
            </div>
            <div class="qr-box">
                <div class="qr-caption">Verify Card</div>
                @if($card->qr_code)
                    @if(str_starts_with($card->qr_code, 'data:image/svg+xml'))
                        <img src="{{ $card->qr_code }}" alt="Student card verification QR code">
                    @else
                        {!! $card->qr_code !!}
                    @endif
                @else
                    <div class="qr-unavailable">QR unavailable</div>
                @endif
            </div>
        </div>
        <div class="back-footer">
            <div class="office-info">
                <strong>{{ $schoolWebsite }}</strong><br>
                {{ $schoolAddress }}<br>
                {{ $schoolPhone }} • {{ $schoolEmail }}
            </div>
            <div class="signature-block">
                @if($signatureUrl)
                    <img src="{{ $signatureUrl }}" alt="Authorized signature" class="signature-image">
                @else
                    <div class="signature-line"></div>
                @endif
                <div class="signature-label">{{ $signatoryName }}</div>
                <div class="signature-role">Academic Registrar / Director<br>Authorized Signature</div>
            </div>
        </div>
    </div>
</div>
@endif
