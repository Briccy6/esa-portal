<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Card Verification | Excellent Servant Academy</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 24px; font-family: "Segoe UI", Arial, sans-serif; background: #eef4f8; color: #0f172a; }
        .page { width: min(1040px, 100%); margin: 0 auto; }
        .brand { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
        .brand img { width: 64px; height: 64px; object-fit: contain; }
        .brand h1 { margin: 0; font-size: 20px; }
        .brand p { margin: 3px 0 0; color: #64748b; }
        .brand-student-name { color: #1d4ed8; font-weight: 700; }
        .panel { background: #fff; border: 1px solid #dbe4ea; border-radius: 18px; padding: 22px; box-shadow: 0 16px 40px rgba(15,23,42,.10); }
        .badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 9px 14px; font-size: 13px; font-weight: 800; letter-spacing: .5px; }
        .badge.valid { background: #dcfce7; color: #166534; }
        .badge.invalid { background: #fee2e2; color: #991b1b; }
        .preview-wrap { margin: 18px 0; display: flex; justify-content: center; }
        .id-card { width: 680px; height: 428px; max-width: 100%; border: 1px solid #dbe3ea; border-radius: 18px; overflow: hidden; background: #fff; box-shadow: 0 12px 28px rgba(15,23,42,.12); }
        .front-header { height: 80px; background: linear-gradient(90deg,#1d4ed8,#2563eb,#3b82f6); color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 14px 22px; }
        .front-header .title { font-size: 20px; font-weight: 800; }
        .front-header .address { font-size: 12px; margin-top: 3px; }
        .logo-badge { width: 52px; height: 52px; padding: 4px; background: #fff; border-radius: 10px; }
        .logo-badge img { width: 100%; height: 100%; object-fit: contain; }
        .front-body { height: 348px; display: grid; grid-template-columns: 168px 1fr; gap: 20px; padding: 18px 22px 16px; }
        .photo-frame { width: 168px; height: 205px; border: 2px solid #dbeafe; border-radius: 12px; overflow: hidden; background: #f8fafc; }
        .photo-frame img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
        .details { display: flex; flex-direction: column; justify-content: space-between; }
        .student-name { font-size: 22px; font-weight: 800; text-transform: uppercase; border-bottom: 2px solid #2563eb; padding-bottom: 8px; }
        .student-role { color: #2563eb; font-size: 11px; font-weight: 800; letter-spacing: 1px; margin-top: 4px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 14px; margin-top: 12px; }
        .meta-label { display: block; color: #64748b; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .meta-value { display: block; margin-top: 3px; font-size: 13px; font-weight: 800; }
        .contact-strip { background: #f8fafc; border-left: 4px solid #2563eb; border-radius: 6px; padding: 9px 12px; display: flex; justify-content: space-between; font-size: 11px; }
        .verification-details { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; }
        .detail { border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; }
        .detail span { display: block; color: #64748b; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .detail strong { display: block; margin-top: 5px; }
        .invalid-message { padding: 24px 0 6px; }
        .invalid-message h1 { margin: 14px 0 8px; }
        .invalid-message p { color: #64748b; }
        @media(max-width:700px) {
            body { padding: 12px; }
            .id-card { height: auto; aspect-ratio: 680/428; }
            .front-header { height: 19%; padding: 3%; }
            .front-header .title { font-size: 13px; }
            .front-header .address { font-size: 8px; }
            .logo-badge { width: 38px; height: 38px; }
            .front-body { height: 81%; grid-template-columns: 26% 1fr; gap: 3%; padding: 3%; }
            .photo-frame { width: 100%; height: 68%; }
            .student-name { font-size: 14px; }
            .meta-grid { margin-top: 4px; }
            .meta-value { font-size: 8px; }
            .meta-label { font-size: 7px; }
            .contact-strip { font-size: 7px; }
            .verification-details { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
@php
    $schoolInfo = \App\Models\SchoolInformation::getActive();
    $logoUrl = $schoolInfo ? ($schoolInfo->logo_url ?: \App\Helpers\LogoHelper::getLogoUrl()) : \App\Helpers\LogoHelper::getLogoUrl();
    $photoValue = $verified ? ($card->passport_photo ?: ($student->passport_photo ?? null)) : null;
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
    $validUntil = $verified && $student->academicYear && $student->academicYear->expiry_date
        ? \Carbon\Carbon::parse($student->academicYear->expiry_date)->format('M d, Y')
        : 'Unknown';
    $shift = $verified ? strtolower((string) ($student->shift ?? '')) : '';
    $shiftLabel = $shift === 'morning' ? 'Day' : ($shift !== '' ? ucfirst($shift) : 'Not recorded');
    $fallbackLogoUrl = \App\Helpers\LogoHelper::getLogoUrl();
@endphp
<main class="page">
    <header class="brand">
        <img src="{{ $logoUrl ?: $fallbackLogoUrl }}" onerror="this.onerror=null;this.src='{{ $fallbackLogoUrl }}';" alt="Excellent Servant Academy logo">
        <div>
            <h1>Excellent Servant Academy</h1>
            <p>Official student identity verification</p>
            @if($verified)<p class="brand-student-name">{{ $student->full_name }}</p>@endif
        </div>
    </header>
    @if($verified)
        <section class="panel">
            <span class="badge valid">VERIFIED STUDENT IDENTITY</span>
            <div class="preview-wrap">
                <article class="id-card">
                    <div class="front-header">
                        <div><div class="title">Excellent Servant Academy</div><div class="address">Kigali, Ndamage House, 2nd Floor</div></div>
                        <div class="logo-badge"><img src="{{ $fallbackLogoUrl }}" alt="School logo"></div>
                    </div>
                    <div class="front-body">
                        <div>
                            <div class="photo-frame">@if($studentPhotoUrl)<img src="{{ $studentPhotoUrl }}" alt="{{ $student->full_name }}">@endif</div>
                        </div>
                        <div class="details">
                            <div>
                                <div class="student-name">{{ $student->full_name }}</div>
                                <div class="student-role">STUDENT • OFFICIAL ID</div>
                                <div class="meta-grid">
                                    <div><span class="meta-label">Reg. Number</span><strong class="meta-value">{{ $card->reg_number }}</strong></div>
                                    <div><span class="meta-label">Academic Year</span><strong class="meta-value">{{ $card->academic_year ?: ($student->academicYear->year ?? '—') }}</strong></div>
                                    <div><span class="meta-label">Course / Combination</span><strong class="meta-value">{{ $card->course ?: ($student->course->course_name ?? '—') }}</strong></div>
                                    <div><span class="meta-label">Branch</span><strong class="meta-value">{{ $card->branch ?: ($student->branch->name ?? '—') }}</strong></div>
                                    <div><span class="meta-label">Shift</span><strong class="meta-value">{{ $shiftLabel }}</strong></div>
                                </div>
                            </div>
                            <div class="contact-strip"><span><strong>Phone:</strong> {{ $student->phone ?: 'Not provided' }}</span><span><strong>Valid Until:</strong> {{ $validUntil }}</span></div>
                        </div>
                    </div>
                </article>
            </div>
            <div class="verification-details">
                <div class="detail"><span>Full Name</span><strong>{{ $student->full_name }}</strong></div>
                <div class="detail"><span>Registration Number</span><strong>{{ $card->reg_number }}</strong></div>
                <div class="detail"><span>Status</span><strong>Active / Valid</strong></div>
                <div class="detail"><span>Course / Combination</span><strong>{{ $card->course ?: ($student->course->course_name ?? 'Not recorded') }}</strong></div>
                <div class="detail"><span>Branch</span><strong>{{ $card->branch ?: ($student->branch->name ?? 'Not recorded') }}</strong></div>
                <div class="detail"><span>Shift</span><strong>{{ $shiftLabel }}</strong></div>
                <div class="detail"><span>Valid Until</span><strong>{{ $validUntil }}</strong></div>
            </div>
        </section>
    @else
        <section class="panel invalid-message">
            <span class="badge invalid">INVALID OR EXPIRED STUDENT CARD</span>
            <h1>Card verification failed</h1>
            <p>{{ $reason ?? 'This student card could not be verified.' }}</p>
        </section>
    @endif
</main>
</body>
</html>
