@extends('students.app')

@section('title', 'Student Profile')

@section('content')
@php
    $missingProfileFields = $student->missingProfileFields();
    $requiredProfileFieldCount = count(\App\Models\Student::requiredProfileFields());
    $completedProfileFields = $requiredProfileFieldCount - count($missingProfileFields);
    $profileCompletionPercent = $requiredProfileFieldCount
        ? (int) round(($completedProfileFields / $requiredProfileFieldCount) * 100)
        : 100;
    $selectedIdentificationType = old('identification_type', $student->identification_type);
    $selectedEducationLevel = old('education_level', $student->education_level);

    $profileImageSource = function ($value) {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'data:image/')) {
            return $value;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        $path = ltrim($value, '/');
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (strlen($value) < 500 && \Illuminate\Support\Facades\Storage::disk('public')->exists($value)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
        }

        $mimeType = str_starts_with($value, 'iVBORw0KGgo') ? 'image/png' : 'image/jpeg';
        return 'data:' . $mimeType . ';base64,' . $value;
    };

    $passportPhotoSource = $profileImageSource($student->passport_photo);
    $idPhotoSource = $profileImageSource($student->id_photo);
@endphp

<style>
    .student-profile-page {
        --profile-ink: #12233f;
        --profile-muted: #64748b;
        --profile-line: #dce5f0;
        --profile-blue: #1e40af;
        --profile-blue-soft: #eff5ff;
        width: min(100%, 1240px);
        margin: 0 auto;
        padding: clamp(.5rem, 2vw, 1.35rem);
        color: var(--profile-ink);
    }

    .profile-back-link {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin: 0 0 1rem .15rem;
        color: #536783;
        font-size: .85rem;
        font-weight: 700;
        text-decoration: none;
    }

    .profile-back-link:hover {
        color: var(--profile-blue);
    }

    .profile-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 1.25rem;
        padding: clamp(1.25rem, 3vw, 2rem);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 20px;
        background:
            radial-gradient(circle at 90% 10%, rgba(116, 167, 255, .32), transparent 30%),
            linear-gradient(125deg, #102447, #1d4288 72%, #2755a3);
        box-shadow: 0 16px 36px rgba(20, 46, 89, .17);
        color: #fff;
    }

    .profile-hero-identity {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 1.1rem;
    }

    .profile-hero-avatar {
        display: grid;
        width: 76px;
        height: 76px;
        flex: 0 0 76px;
        place-items: center;
        overflow: hidden;
        border: 3px solid rgba(255, 255, 255, .78);
        border-radius: 22px;
        background: rgba(255, 255, 255, .15);
        color: #fff;
        font-size: 1.75rem;
    }

    .profile-hero-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-hero-eyebrow {
        margin: 0 0 .35rem;
        color: #c4d7ff;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .profile-hero-title {
        margin: 0;
        color: #fff;
        font-size: clamp(1.45rem, 3.2vw, 2rem);
        font-weight: 800;
        letter-spacing: -.04em;
    }

    .profile-hero-copy {
        margin: .4rem 0 0;
        color: #dbe7fb;
        font-size: .9rem;
    }

    .profile-completion {
        width: min(100%, 270px);
        flex: 0 0 270px;
        padding: 1rem 1.1rem;
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: 14px;
        background: rgba(8, 24, 52, .28);
        backdrop-filter: blur(8px);
    }

    .profile-completion-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: .65rem;
        color: #fff;
        font-size: .82rem;
        font-weight: 700;
    }

    .profile-completion-top strong {
        font-size: 1rem;
        font-variant-numeric: tabular-nums;
    }

    .profile-completion-track {
        height: 7px;
        overflow: hidden;
        border-radius: 99px;
        background: rgba(255, 255, 255, .22);
    }

    .profile-completion-track span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: #f5c46b;
        transition: width .25s ease;
    }

    .profile-completion-note {
        display: block;
        margin-top: .55rem;
        color: #dbe7fb;
        font-size: .72rem;
        line-height: 1.45;
    }

    .profile-alerts {
        margin-bottom: 1rem;
    }

    .profile-alerts .alert {
        margin-bottom: .65rem;
        border-radius: 12px;
    }

    .profile-editor {
        display: grid;
        grid-template-columns: minmax(210px, .75fr) minmax(0, 2fr);
        align-items: start;
        gap: 1.1rem;
    }

    .profile-stepper,
    .profile-form-card {
        border: 1px solid var(--profile-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 8px 26px rgba(17, 39, 73, .055);
    }

    .profile-stepper {
        position: sticky;
        top: 84px;
        padding: 1rem;
    }

    .profile-stepper-title {
        margin: .15rem .55rem .8rem;
        color: #7a8aa2;
        font-size: .67rem;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .profile-step-list {
        display: grid;
        gap: .35rem;
    }

    .profile-step-button {
        display: flex;
        width: 100%;
        align-items: center;
        gap: .75rem;
        padding: .8rem .7rem;
        border: 1px solid transparent;
        border-radius: 11px;
        background: transparent;
        color: #536783;
        text-align: left;
        transition: background .16s ease, border-color .16s ease, color .16s ease;
    }

    .profile-step-button:hover {
        background: #f5f8fc;
        color: var(--profile-ink);
    }

    .profile-step-button.is-active {
        border-color: #dce8ff;
        background: var(--profile-blue-soft);
        color: var(--profile-blue);
    }

    .profile-step-number {
        display: grid;
        width: 31px;
        height: 31px;
        flex: 0 0 31px;
        place-items: center;
        border: 1px solid #dce5f0;
        border-radius: 10px;
        background: #fff;
        color: #64748b;
        font-size: .72rem;
        font-weight: 800;
    }

    .profile-step-button.is-active .profile-step-number {
        border-color: var(--profile-blue);
        background: var(--profile-blue);
        color: #fff;
    }

    .profile-step-name {
        min-width: 0;
        font-size: .82rem;
        font-weight: 750;
        line-height: 1.35;
    }

    .profile-step-caption {
        display: block;
        margin-top: .15rem;
        color: #8a9ab0;
        font-size: .68rem;
        font-weight: 500;
    }

    .profile-missing-note {
        margin-top: 1rem;
        padding: .85rem;
        border: 1px solid #f4dfb2;
        border-radius: 11px;
        background: #fffaf0;
        color: #77551c;
        font-size: .76rem;
        line-height: 1.55;
    }

    .profile-form-card {
        min-width: 0;
        overflow: hidden;
    }

    .profile-form-progress {
        height: 4px;
        background: #edf2f8;
    }

    .profile-form-progress span {
        display: block;
        width: 25%;
        height: 100%;
        background: linear-gradient(90deg, #1e40af, #5c8ce4);
        transition: width .25s ease;
    }

    .profile-form-content {
        padding: clamp(1.1rem, 3vw, 2rem);
    }

    .profile-form-heading {
        margin-bottom: 1.4rem;
    }

    .profile-form-kicker {
        margin: 0 0 .35rem;
        color: var(--profile-blue);
        font-size: .68rem;
        font-weight: 850;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .profile-form-heading h2 {
        margin: 0;
        color: var(--profile-ink);
        font-size: clamp(1.25rem, 2.5vw, 1.6rem);
        font-weight: 800;
        letter-spacing: -.035em;
    }

    .profile-form-heading p {
        margin: .4rem 0 0;
        color: var(--profile-muted);
        font-size: .86rem;
    }

    .profile-step-panel[hidden] {
        display: none !important;
    }

    .profile-fields-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .profile-field {
        min-width: 0;
    }

    .profile-field--wide {
        grid-column: 1 / -1;
    }

    .profile-field label {
        display: block;
        margin-bottom: .42rem;
        color: #263a57;
        font-size: .79rem;
        font-weight: 750;
    }

    .profile-required-badge {
        margin-left: .35rem;
        color: #99620c;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .profile-field .form-control,
    .profile-field .form-select {
        min-height: 46px;
        border-color: #d6e0ec;
        border-radius: 9px;
        color: #182b47;
        font-size: .9rem;
    }

    .profile-field .form-control:focus,
    .profile-field .form-select:focus {
        border-color: #6a94e8;
        box-shadow: 0 0 0 3px rgba(51, 101, 194, .12);
    }

    .profile-field-hint {
        display: block;
        margin-top: .4rem;
        color: #77879d;
        font-size: .73rem;
        line-height: 1.5;
    }

    .profile-upload-card {
        display: grid;
        grid-template-columns: 152px minmax(0, 1fr);
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid #e0e8f2;
        border-radius: 13px;
        background: #f9fbfe;
    }

    .profile-image-frame {
        display: grid;
        width: 152px;
        height: 152px;
        place-items: center;
        overflow: hidden;
        border: 1px dashed #bdcce0;
        border-radius: 13px;
        background:
            linear-gradient(45deg, #f1f5fa 25%, transparent 25%),
            linear-gradient(-45deg, #f1f5fa 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, #f1f5fa 75%),
            linear-gradient(-45deg, transparent 75%, #f1f5fa 75%);
        background-color: #fff;
        background-size: 16px 16px;
        background-position: 0 0, 0 8px, 8px -8px, -8px 0;
        color: #8a9bb2;
    }

    .profile-image-frame--passport {
        height: 174px;
    }

    .profile-image-frame--landscape {
        width: 100%;
        height: 152px;
    }

    .profile-image-frame img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: #fff;
    }

    .profile-image-frame img[hidden] {
        display: none !important;
    }

    .profile-image-empty {
        display: grid;
        gap: .4rem;
        justify-items: center;
        padding: .75rem;
        color: #8192aa;
        font-size: .72rem;
        text-align: center;
    }

    .profile-image-empty[hidden] {
        display: none !important;
    }

    .profile-image-empty i {
        color: #7085a2;
        font-size: 1.45rem;
    }

    .profile-upload-details .form-control {
        min-height: 44px;
        padding: .45rem;
        background: #fff;
        font-size: .8rem;
    }

    .profile-upload-status {
        min-height: 1.3rem;
        margin: .5rem 0 0;
        color: #687b94;
        font-size: .73rem;
        overflow-wrap: anywhere;
    }

    .profile-file-error {
        display: block;
        min-height: 1.1rem;
        margin-top: .3rem;
        color: #b42318;
        font-size: .73rem;
        font-weight: 650;
    }

    .profile-conditional[hidden] {
        display: none !important;
    }

    .profile-security-note {
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        margin-bottom: 1.1rem;
        padding: .9rem 1rem;
        border: 1px solid #dce8ff;
        border-radius: 11px;
        background: #f5f8ff;
        color: #49617f;
        font-size: .8rem;
        line-height: 1.55;
    }

    .profile-security-note i {
        margin-top: .1rem;
        color: var(--profile-blue);
    }

    .profile-form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .8rem;
        padding: 1rem clamp(1.1rem, 3vw, 2rem);
        border-top: 1px solid #e7edf5;
        background: #fbfcfe;
    }

    .profile-footer-right {
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .profile-form-footer .btn {
        min-height: 42px;
        border-radius: 9px;
        font-size: .82rem;
        font-weight: 750;
    }

    .profile-form-footer .btn-primary {
        background: #1e40af;
        border-color: #1e40af;
    }

    @media (max-width: 900px) {
        .profile-editor {
            grid-template-columns: 1fr;
        }

        .profile-stepper {
            position: static;
            padding: .75rem;
        }

        .profile-stepper-title {
            margin-bottom: .55rem;
        }

        .profile-step-list {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .35rem;
        }

        .profile-step-button {
            min-height: 62px;
            justify-content: center;
            padding: .5rem .3rem;
            text-align: center;
        }

        .profile-step-number {
            display: none;
        }

        .profile-step-name {
            font-size: .7rem;
        }

        .profile-step-caption {
            display: none;
        }

        .profile-missing-note {
            margin-top: .7rem;
        }
    }

    @media (max-width: 640px) {
        .student-profile-page {
            padding: .25rem 0;
        }

        .profile-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 1rem;
            border-radius: 14px;
        }

        .profile-hero-avatar {
            width: 60px;
            height: 60px;
            flex-basis: 60px;
            border-radius: 17px;
        }

        .profile-completion {
            width: 100%;
            flex-basis: auto;
        }

        .profile-step-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .profile-step-button {
            min-height: 50px;
        }

        .profile-form-content {
            padding: 1.1rem;
        }

        .profile-fields-grid {
            grid-template-columns: 1fr;
            gap: .85rem;
        }

        .profile-field--wide {
            grid-column: auto;
        }

        .profile-upload-card {
            grid-template-columns: 1fr;
        }

        .profile-image-frame,
        .profile-image-frame--passport {
            width: min(100%, 190px);
            height: 190px;
            margin: 0 auto;
        }

        .profile-image-frame--landscape {
            width: 100%;
            height: 155px;
        }

        .profile-form-footer {
            position: sticky;
            bottom: 0;
            z-index: 3;
            padding: .75rem 1rem;
            box-shadow: 0 -8px 20px rgba(20, 40, 70, .07);
        }

        .profile-form-footer .btn {
            min-height: 40px;
            padding: .45rem .7rem;
            font-size: .76rem;
        }
    }

    @media (max-width: 380px) {
        .profile-step-name {
            font-size: .64rem;
        }

        .profile-footer-right {
            gap: .35rem;
        }
    }
</style>

<main class="student-profile-page">
    <a class="profile-back-link" href="{{ route('student.dashboard') }}">
        <i class="bi bi-arrow-left" aria-hidden="true"></i>Back to dashboard
    </a>

    <header class="profile-hero">
        <div class="profile-hero-identity">
            <div class="profile-hero-avatar">
                @if($passportPhotoSource)
                    <img src="{{ $passportPhotoSource }}" alt="Current student profile photo">
                @else
                    <i class="bi bi-person" aria-hidden="true"></i>
                @endif
            </div>
            <div>
                <p class="profile-hero-eyebrow">Student account</p>
                <h1 class="profile-hero-title">Your profile</h1>
                <p class="profile-hero-copy">Review and update your personal, identification, and education details.</p>
            </div>
        </div>
        <div class="profile-completion" aria-label="Profile completeness: {{ $profileCompletionPercent }} percent">
            <div class="profile-completion-top">
                <span>Profile completeness</span>
                <strong>{{ $profileCompletionPercent }}%</strong>
            </div>
            <div class="profile-completion-track" role="progressbar"
                 aria-valuenow="{{ $profileCompletionPercent }}" aria-valuemin="0" aria-valuemax="100">
                <span style="width: {{ $profileCompletionPercent }}%"></span>
            </div>
            <small class="profile-completion-note">
                {{ $completedProfileFields }} of {{ $requiredProfileFieldCount }} required profile details are complete.
            </small>
        </div>
    </header>

    <div class="profile-alerts">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="status">
                <i class="bi bi-check-circle me-2" aria-hidden="true"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss message"></button>
            </div>
        @endif
        @if(session('updated_fields'))
            <div class="alert alert-info alert-dismissible fade show" role="status">
                <strong>Updated:</strong> {{ implode(', ', session('updated_fields')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss message"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please review the highlighted fields.</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="profile-editor">
        <aside class="profile-stepper" aria-label="Profile sections">
            <p class="profile-stepper-title">Profile sections</p>
            <nav class="profile-step-list">
                <button class="profile-step-button is-active" type="button" data-step-target="0" aria-current="step">
                    <span class="profile-step-number">01</span>
                    <span class="profile-step-name">Personal details<span class="profile-step-caption">Photo and contact</span></span>
                </button>
                <button class="profile-step-button" type="button" data-step-target="1">
                    <span class="profile-step-number">02</span>
                    <span class="profile-step-name">Identification<span class="profile-step-caption">ID or passport</span></span>
                </button>
                <button class="profile-step-button" type="button" data-step-target="2">
                    <span class="profile-step-number">03</span>
                    <span class="profile-step-name">Education<span class="profile-step-caption">Level and documents</span></span>
                </button>
                <button class="profile-step-button" type="button" data-step-target="3">
                    <span class="profile-step-number">04</span>
                    <span class="profile-step-name">Security<span class="profile-step-caption">Optional password update</span></span>
                </button>
            </nav>
            @if(count($missingProfileFields))
                <div class="profile-missing-note">
                    <strong><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Still needed</strong><br>
                    {{ implode(', ', $missingProfileFields) }}
                </div>
            @endif
        </aside>

        <form id="profileForm" class="profile-form-card" action="{{ route('students.profile.update') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            <div class="profile-form-progress" aria-hidden="true">
                <span id="profileStepProgress"></span>
            </div>

            <div class="profile-form-content">
                <section class="profile-step-panel" data-step-panel="0" role="group" aria-labelledby="personalStepTitle">
                    <div class="profile-form-heading">
                        <p class="profile-form-kicker" data-step-kicker>Step 1 of 4</p>
                        <h2 id="personalStepTitle" tabindex="-1">Personal details</h2>
                        <p>Use your official name and current contact information.</p>
                    </div>

                    <div class="profile-field profile-field--wide">
                        <label for="passportPhotoInput">Student passport photograph <span class="profile-required-badge">Required to complete</span></label>

                        <!-- Accepted passport photo example -->
                        <div class="mb-3 p-3 rounded-2" style="background: #f5f8ff; border: 1px solid #dce8ff;">
                            <p class="mb-2 fw-bold text-primary" style="font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Accepted passport photo example</p>
                            <div class="d-flex align-items-start gap-3">
                                <div style="width: 100px; height: 130px; background: #fff; border: 2px solid #1e40af; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <div style="text-align: center; color: #64748b; font-size: 0.7rem; padding: 8px;">
                                        <i class="bi bi-person" style="font-size: 2rem; display: block; margin-bottom: 4px;"></i>
                                        <span>Sample Portrait</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-1 fw-semibold" style="font-size: 0.85rem; color: #1e40af;">Required appearance</p>
                                    <p style="font-size: 0.75rem; color: #475569; margin-bottom: 8px;">One person, centered face, white background and clear portrait.</p>
                                    <ul style="font-size: 0.7rem; color: #475569; margin: 0; padding-left: 1.2rem;">
                                        <li>White background</li>
                                        <li>One person only</li>
                                        <li>Face centered</li>
                                        <li>Looking directly at the camera</li>
                                        <li>Clear photograph</li>
                                        <li>Maximum 200 KB</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Photos we do not accept -->
                        <div class="mb-3 p-3 rounded-2" style="background: #fff5f5; border: 1px solid #fed7d7;">
                            <p class="mb-2 fw-bold text-danger" style="font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Photos we do not accept</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <div style="width: 80px; height: 80px; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <div style="text-align: center; color: #94a3b8; font-size: 0.65rem;">
                                        <i class="bi bi-camera" style="font-size: 1.2rem; display: block;"></i>
                                        <span>Selfies</span>
                                    </div>
                                </div>
                                <div style="width: 80px; height: 80px; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <div style="text-align: center; color: #94a3b8; font-size: 0.65rem;">
                                        <i class="bi bi-palette" style="font-size: 1.2rem; display: block;"></i>
                                        <span>Colored background</span>
                                    </div>
                                </div>
                                <div style="width: 80px; height: 80px; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <div style="text-align: center; color: #94a3b8; font-size: 0.65rem;">
                                        <i class="bi bi-people" style="font-size: 1.2rem; display: block;"></i>
                                        <span>Group photos</span>
                                    </div>
                                </div>
                                <div style="width: 80px; height: 80px; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <div style="text-align: center; color: #94a3b8; font-size: 0.65rem;">
                                        <i class="bi bi-file-earmark-image" style="font-size: 1.2rem; display: block;"></i>
                                        <span>Document scans</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload area -->
                        <div class="profile-upload-card">
                            <div class="profile-image-frame profile-image-frame--passport">
                                <img id="passportPhotoPreview" data-preview-image
                                     src="{{ $passportPhotoSource ?? '' }}"
                                     data-original-src="{{ $passportPhotoSource ?? '' }}"
                                     alt="Preview of your profile photo"
                                     @if(!$passportPhotoSource) hidden @endif>
                                <div class="profile-image-empty" data-preview-empty @if($passportPhotoSource) hidden @endif>
                                    <i class="bi bi-person-bounding-box" aria-hidden="true"></i>
                                    <span>Photo preview</span>
                                </div>
                            </div>
                            <div class="profile-upload-details">
                                <input type="file" name="passport_photo" id="passportPhotoInput"
                                       class="form-control @error('passport_photo') is-invalid @enderror"
                                       accept="image/jpeg,image/png,.jpg,.jpeg,.png"
                                       data-preview-target="passportPhotoPreview"
                                       data-name-target="passportPhotoName"
                                       data-error-target="passportPhotoError"
                                       data-original-status="{{ $passportPhotoSource ? 'Current photo is on file. Choose a new photo to replace it.' : 'No photo uploaded yet.' }}"
                                       data-max-bytes="204800" data-preview-kind="image">
                                <span class="profile-field-hint">JPG, JPEG, or PNG, up to 200 KB. Your image appears here before you save. <strong>AI verification required.</strong></span>
                                <p class="profile-upload-status" id="passportPhotoName">
                                    {{ $passportPhotoSource ? 'Current photo is on file. Choose a new photo to replace it.' : 'No photo uploaded yet.' }}
                                </p>
                                <span class="profile-file-error" id="passportPhotoError" role="alert"></span>
                                @error('passport_photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                                <!-- Verification status -->
                                <div id="passportVerificationStatus" class="mt-2" style="display: none;">
                                    <div id="passportVerificationLoading" style="display: none;">
                                        <div class="d-flex align-items-center gap-2" style="color: #64748b; font-size: 0.75rem;">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                            <span>Checking your passport photograph...</span>
                                        </div>
                                    </div>
                                    <div id="passportVerificationSuccess" class="alert alert-success py-2 px-3" style="display: none; font-size: 0.75rem;">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <span>Passport photograph approved! Your photograph meets Excellent Servant Academy requirements.</span>
                                    </div>
                                    <div id="passportVerificationError" class="alert alert-danger py-2 px-3" style="display: none; font-size: 0.75rem;">
                                        <i class="bi bi-x-circle me-1"></i>
                                        <span id="passportVerificationErrorMessage"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-fields-grid mt-3">
                        <div class="profile-field">
                            <label for="fullNameInput">Full name <span class="profile-required-badge">Required to complete</span></label>
                            <input type="text" name="full_name" id="fullNameInput" autocomplete="name"
                                   class="form-control @error('full_name') is-invalid @enderror"
                                   value="{{ old('full_name', $student->full_name) }}">
                            @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="profile-field">
                            <label for="telephoneInput">Telephone <span class="profile-required-badge">Required to complete</span></label>
                            <input type="tel" name="telephone" id="telephoneInput" autocomplete="tel"
                                   class="form-control @error('telephone') is-invalid @enderror"
                                   value="{{ old('telephone', $student->phone) }}">
                            @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="profile-field">
                            <label for="emailInput">Email address <span class="profile-required-badge">Required to complete</span></label>
                            <input type="email" name="email" id="emailInput" autocomplete="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $student->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="profile-field">
                            <label for="sexSelect">Sex <span class="profile-required-badge">Required to complete</span></label>
                            <select name="sex" id="sexSelect" class="form-select @error('sex') is-invalid @enderror">
                                <option value="">Select an option</option>
                                <option value="Male" {{ old('sex', $student->sex) === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex', $student->sex) === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('sex', $student->sex) === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </section>

                <section class="profile-step-panel" data-step-panel="1" role="group" aria-labelledby="identificationStepTitle" hidden>
                    <div class="profile-form-heading">
                        <p class="profile-form-kicker">Step 2 of 4</p>
                        <h2 id="identificationStepTitle" tabindex="-1">Identification</h2>
                        <p>Select the document you will provide, then enter the matching number and image.</p>
                    </div>

                    <div class="profile-fields-grid">
                        <div class="profile-field profile-field--wide">
                            <label for="identificationTypeSelect">Document type <span class="profile-required-badge">Required to complete</span></label>
                            <select name="identification_type" id="identificationTypeSelect"
                                    class="form-select @error('identification_type') is-invalid @enderror">
                                <option value="">Choose ID or passport</option>
                                <option value="ID" {{ $selectedIdentificationType === 'ID' ? 'selected' : '' }}>National ID</option>
                                <option value="Passport" {{ $selectedIdentificationType === 'Passport' ? 'selected' : '' }}>Passport</option>
                            </select>
                            <span class="profile-field-hint">Choose the type that matches the document image you upload below.</span>
                            @error('identification_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="profile-field profile-field--wide profile-conditional"
                             id="idNumberDiv" @if(!in_array($selectedIdentificationType, ['ID', 'Passport'], true)) hidden @endif>
                            <label for="idNumberInput">
                                <span id="idNumberLabel">
                                {{ $selectedIdentificationType === 'Passport' ? 'Passport number' : 'National ID number' }}
                                </span>
                                <span class="profile-required-badge">Required to complete</span>
                            </label>
                            <input type="text" name="id_number" id="idNumberInput" autocomplete="off"
                                   class="form-control @error('id_number') is-invalid @enderror"
                                   value="{{ old('id_number', $student->id_number) }}">
                            <span class="profile-field-hint">Enter the number exactly as it appears on the selected document.</span>
                            @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="profile-field profile-field--wide profile-conditional"
                             id="idPhotoDiv" @if(!in_array($selectedIdentificationType, ['ID', 'Passport'], true)) hidden @endif>
                            <label for="idPhotoInput">
                                <span id="idPhotoLabel">
                                {{ $selectedIdentificationType === 'Passport' ? 'Passport image' : 'National ID image' }}
                                </span>
                                <span class="profile-required-badge">Required to complete</span>
                            </label>
                            <div class="profile-upload-card">
                                <div class="profile-image-frame profile-image-frame--landscape">
                                    <img id="idPhotoPreview" data-preview-image
                                         src="{{ $idPhotoSource ?? '' }}"
                                         data-original-src="{{ $idPhotoSource ?? '' }}"
                                         alt="Preview of your identification document"
                                         @if(!$idPhotoSource) hidden @endif>
                                    <div class="profile-image-empty" data-preview-empty @if($idPhotoSource) hidden @endif>
                                        <i class="bi bi-card-image" aria-hidden="true"></i>
                                        <span>Document preview</span>
                                    </div>
                                </div>
                                <div class="profile-upload-details">
                                    <input type="file" name="id_photo" id="idPhotoInput"
                                           class="form-control @error('id_photo') is-invalid @enderror"
                                           accept="image/jpeg,image/png,.jpg,.jpeg,.png"
                                           data-preview-target="idPhotoPreview"
                                           data-name-target="idPhotoName"
                                           data-error-target="idPhotoError"
                                           data-original-status="{{ $idPhotoSource ? 'Current document image is on file. Choose a new image to replace it.' : 'No document image uploaded yet.' }}"
                                           data-max-bytes="204800" data-preview-kind="image">
                                    <span class="profile-field-hint">Clear, readable JPG, JPEG, or PNG, up to 200 KB.</span>
                                    <p class="profile-upload-status" id="idPhotoName">
                                        {{ $idPhotoSource ? 'Current document image is on file. Choose a new image to replace it.' : 'No document image uploaded yet.' }}
                                    </p>
                                    <span class="profile-file-error" id="idPhotoError" role="alert"></span>
                                    @error('id_photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="profile-step-panel" data-step-panel="2" role="group" aria-labelledby="educationStepTitle" hidden>
                    <div class="profile-form-heading">
                        <p class="profile-form-kicker">Step 3 of 4</p>
                        <h2 id="educationStepTitle" tabindex="-1">Education details</h2>
                        <p>Choose your current education level. Only the matching document field will be shown.</p>
                    </div>

                    <div class="profile-fields-grid">
                        <div class="profile-field profile-field--wide">
                            <label for="educationLevelSelect">Education level <span class="profile-required-badge">Required to complete</span></label>
                            <select name="education_level" id="educationLevelSelect"
                                    class="form-select @error('education_level') is-invalid @enderror">
                                <option value="">Select your level</option>
                                <option value="Senior 3" {{ $selectedEducationLevel === 'Senior 3' ? 'selected' : '' }}>Senior 3</option>
                                <option value="Senior 6" {{ $selectedEducationLevel === 'Senior 6' ? 'selected' : '' }}>Senior 6</option>
                                <option value="Other" {{ $selectedEducationLevel === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <span class="profile-field-hint">Selecting a level reveals the matching supporting-document upload.</span>
                            @error('education_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="profile-field profile-field--wide">
                            <label for="shiftSelect">Shift</label>
                            @if($student->shift_changed_at)
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Your shift is set to <strong>{{ ucfirst($student->shift ?? 'day') }}</strong>. You can only change your shift once, and you have already changed it on {{ $student->shift_changed_at ? $student->shift_changed_at->format('F j, Y \a\t g:i A') : 'a previous date' }}.
                                </div>
                                <input type="hidden" name="shift" value="{{ $student->shift }}">
                            @else
                                <select name="shift" id="shiftSelect" class="form-select @error('shift') is-invalid @enderror">
                                    <option value="">Select your shift</option>
                                    <option value="day" {{ old('shift', $student->shift) === 'day' ? 'selected' : '' }}>Day</option>
                                    <option value="evening" {{ old('shift', $student->shift) === 'evening' ? 'selected' : '' }}>Evening</option>
                                    <option value="weekend" {{ old('shift', $student->shift) === 'weekend' ? 'selected' : '' }}>Weekend</option>
                                </select>
                                <span class="profile-field-hint">Select your preferred study shift. <strong>Note:</strong> You can only change your shift once.</span>
                                @error('shift')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>

                        <div class="profile-field profile-field--wide profile-conditional"
                             id="senior3DocumentDiv" @if($selectedEducationLevel !== 'Senior 3') hidden @endif>
                            <label for="senior3DocumentInput">Senior 3 document</label>
                            <div class="profile-upload-card">
                                <div class="profile-image-frame profile-image-frame--landscape">
                                    <img id="senior3DocumentPreview" data-preview-image alt="Senior 3 document image preview" hidden>
                                    <div class="profile-image-empty" data-preview-empty>
                                        <i class="bi bi-file-earmark" data-preview-icon aria-hidden="true"></i>
                                        <span data-preview-placeholder>Document preview</span>
                                    </div>
                                </div>
                                <div class="profile-upload-details">
                                    <input type="file" name="senior3_document" id="senior3DocumentInput"
                                           class="form-control @error('senior3_document') is-invalid @enderror"
                                           accept="application/pdf,image/jpeg,image/png,.pdf,.jpg,.jpeg,.png"
                                           data-preview-target="senior3DocumentPreview"
                                           data-name-target="senior3DocumentName"
                                           data-error-target="senior3DocumentError"
                                           data-original-status="{{ $student->senior3_document ? 'A document is already on file. Choose a new one to replace it.' : 'No document uploaded yet.' }}"
                                           data-max-bytes="204800" data-preview-kind="document">
                                    <span class="profile-field-hint">PDF, JPG, JPEG, or PNG, up to 200 KB.</span>
                                    <p class="profile-upload-status" id="senior3DocumentName">
                                        {{ $student->senior3_document ? 'A document is already on file. Choose a new one to replace it.' : 'No document uploaded yet.' }}
                                    </p>
                                    <span class="profile-file-error" id="senior3DocumentError" role="alert"></span>
                                    @error('senior3_document')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="profile-field profile-field--wide profile-conditional"
                             id="senior6DocumentDiv" @if($selectedEducationLevel !== 'Senior 6') hidden @endif>
                            <label for="senior6DocumentInput">Senior 6 document</label>
                            <div class="profile-upload-card">
                                <div class="profile-image-frame profile-image-frame--landscape">
                                    <img id="senior6DocumentPreview" data-preview-image alt="Senior 6 document image preview" hidden>
                                    <div class="profile-image-empty" data-preview-empty>
                                        <i class="bi bi-file-earmark" data-preview-icon aria-hidden="true"></i>
                                        <span data-preview-placeholder>Document preview</span>
                                    </div>
                                </div>
                                <div class="profile-upload-details">
                                    <input type="file" name="senior6_document" id="senior6DocumentInput"
                                           class="form-control @error('senior6_document') is-invalid @enderror"
                                           accept="application/pdf,image/jpeg,image/png,.pdf,.jpg,.jpeg,.png"
                                           data-preview-target="senior6DocumentPreview"
                                           data-name-target="senior6DocumentName"
                                           data-error-target="senior6DocumentError"
                                           data-original-status="{{ $student->senior6_document ? 'A document is already on file. Choose a new one to replace it.' : 'No document uploaded yet.' }}"
                                           data-max-bytes="204800" data-preview-kind="document">
                                    <span class="profile-field-hint">PDF, JPG, JPEG, or PNG, up to 200 KB.</span>
                                    <p class="profile-upload-status" id="senior6DocumentName">
                                        {{ $student->senior6_document ? 'A document is already on file. Choose a new one to replace it.' : 'No document uploaded yet.' }}
                                    </p>
                                    <span class="profile-file-error" id="senior6DocumentError" role="alert"></span>
                                    @error('senior6_document')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="profile-field profile-field--wide profile-conditional"
                             id="otherSupportingDocumentDiv" @if($selectedEducationLevel !== 'Other') hidden @endif>
                            <label for="otherSupportingDocumentInput">Other supporting document</label>
                            <div class="profile-upload-card">
                                <div class="profile-image-frame profile-image-frame--landscape">
                                    <img id="otherSupportingDocumentPreview" data-preview-image alt="Supporting document image preview" hidden>
                                    <div class="profile-image-empty" data-preview-empty>
                                        <i class="bi bi-file-earmark" data-preview-icon aria-hidden="true"></i>
                                        <span data-preview-placeholder>Document preview</span>
                                    </div>
                                </div>
                                <div class="profile-upload-details">
                                    <input type="file" name="other_supporting_document" id="otherSupportingDocumentInput"
                                           class="form-control @error('other_supporting_document') is-invalid @enderror"
                                           accept="application/pdf,image/jpeg,image/png,.pdf,.jpg,.jpeg,.png"
                                           data-preview-target="otherSupportingDocumentPreview"
                                           data-name-target="otherSupportingDocumentName"
                                           data-error-target="otherSupportingDocumentError"
                                           data-original-status="{{ $student->other_supporting_document ? 'A document is already on file. Choose a new one to replace it.' : 'No document uploaded yet.' }}"
                                           data-max-bytes="204800" data-preview-kind="document">
                                    <span class="profile-field-hint">PDF, JPG, JPEG, or PNG, up to 200 KB.</span>
                                    <p class="profile-upload-status" id="otherSupportingDocumentName">
                                        {{ $student->other_supporting_document ? 'A document is already on file. Choose a new one to replace it.' : 'No document uploaded yet.' }}
                                    </p>
                                    <span class="profile-file-error" id="otherSupportingDocumentError" role="alert"></span>
                                    @error('other_supporting_document')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="profile-step-panel" data-step-panel="3" role="group" aria-labelledby="securityStepTitle" hidden>
                    <div class="profile-form-heading">
                        <p class="profile-form-kicker">Step 4 of 4</p>
                        <h2 id="securityStepTitle" tabindex="-1">Account security</h2>
                        <p>Update your password here, or leave both password fields blank to keep it unchanged.</p>
                    </div>

                    <div class="profile-security-note">
                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                        <span>For security, choose at least 8 characters with uppercase and lowercase letters, a number, and a symbol.</span>
                    </div>
                    <div class="profile-fields-grid">
                        <div class="profile-field">
                            <label for="passwordInput">New password</label>
                            <input type="password" name="password" id="passwordInput" autocomplete="new-password"
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="profile-field">
                            <label for="passwordConfirmInput">Confirm new password</label>
                            <input type="password" name="password_confirmation" id="passwordConfirmInput" autocomplete="new-password"
                                   class="form-control">
                        </div>
                        <div class="profile-field profile-field--wide">
                            <span class="profile-file-error" id="passwordHelp" role="alert"></span>
                        </div>
                    </div>
                </section>
            </div>

            <footer class="profile-form-footer">
                <button type="button" class="btn btn-outline-secondary" id="previousProfileStep" disabled>
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Previous
                </button>
                <div class="profile-footer-right">
                    <button type="button" class="btn btn-outline-primary" id="nextProfileStep">
                        Continue<i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Save profile
                    </button>
                </div>
            </footer>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('profileForm');
    const panels = Array.from(form.querySelectorAll('[data-step-panel]'));
    const stepButtons = Array.from(document.querySelectorAll('[data-step-target]'));
    const previousButton = document.getElementById('previousProfileStep');
    const nextButton = document.getElementById('nextProfileStep');
    const progress = document.getElementById('profileStepProgress');
    let activeStep = 0;

    function showStep(index, moveFocus) {
        activeStep = Math.max(0, Math.min(index, panels.length - 1));

        panels.forEach(function (panel, panelIndex) {
            panel.hidden = panelIndex !== activeStep;
        });

        stepButtons.forEach(function (button, buttonIndex) {
            const isActive = buttonIndex === activeStep;
            button.classList.toggle('is-active', isActive);
            if (isActive) {
                button.setAttribute('aria-current', 'step');
            } else {
                button.removeAttribute('aria-current');
            }
        });

        previousButton.disabled = activeStep === 0;
        nextButton.hidden = activeStep === panels.length - 1;
        progress.style.width = ((activeStep + 1) / panels.length * 100) + '%';

        if (moveFocus) {
            const heading = panels[activeStep].querySelector('h2');
            if (heading) {
                heading.focus();
            }
        }
    }

    stepButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            showStep(Number(button.dataset.stepTarget), true);
        });
    });

    previousButton.addEventListener('click', function () {
        showStep(activeStep - 1, true);
    });

    nextButton.addEventListener('click', function () {
        showStep(activeStep + 1, true);
    });

    const firstInvalidField = form.querySelector('.is-invalid');
    if (firstInvalidField) {
        const invalidPanel = firstInvalidField.closest('[data-step-panel]');
        if (invalidPanel) {
            showStep(Number(invalidPanel.dataset.stepPanel), false);
        }
    }

    function restoreFilePreview(input, message) {
        const image = document.getElementById(input.dataset.previewTarget);
        const name = document.getElementById(input.dataset.nameTarget);
        const error = document.getElementById(input.dataset.errorTarget);
        const frame = image.closest('.profile-image-frame');
        const placeholder = frame.querySelector('[data-preview-empty]');
        const icon = placeholder.querySelector('[data-preview-icon]');
        const placeholderText = placeholder.querySelector('[data-preview-placeholder]');

        image.src = image.dataset.originalSrc || '';
        image.hidden = !image.src;
        placeholder.hidden = Boolean(image.src);
        if (icon) {
            icon.className = 'bi bi-file-earmark';
        }
        if (placeholderText) {
            placeholderText.textContent = 'Document preview';
        }
        name.textContent = input.dataset.originalStatus;
        error.textContent = message || '';
    }

    document.querySelectorAll('[data-preview-target]').forEach(function (input) {
        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            const image = document.getElementById(input.dataset.previewTarget);
            const name = document.getElementById(input.dataset.nameTarget);
            const error = document.getElementById(input.dataset.errorTarget);
            const frame = image.closest('.profile-image-frame');
            const placeholder = frame.querySelector('[data-preview-empty]');

            if (!file) {
                restoreFilePreview(input);
                return;
            }

            const maxBytes = Number(input.dataset.maxBytes || 204800);
            if (file.size > maxBytes) {
                input.value = '';
                restoreFilePreview(input, 'File must be 200 KB or smaller.');
                return;
            }

            const isImage = file.type.startsWith('image/') || /\.(jpe?g|png)$/i.test(file.name);
            const isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
            if (!isImage && !(input.dataset.previewKind === 'document' && isPdf)) {
                input.value = '';
                restoreFilePreview(input, 'Choose a JPG, JPEG, PNG, or PDF file as indicated.');
                return;
            }

            error.textContent = '';
            name.textContent = 'Selected: ' + file.name + ' (' + Math.max(1, Math.ceil(file.size / 1024)) + ' KB)';

            if (isImage) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    image.src = event.target.result;
                    image.hidden = false;
                    placeholder.hidden = true;

                    // Trigger AI verification for passport photo
                    if (input.id === 'passportPhotoInput') {
                        verifyPassportPhoto(input);
                    }
                };
                reader.onerror = function () {
                    input.value = '';
                    restoreFilePreview(input, 'The image preview could not be loaded. Please choose another file.');
                };
                reader.readAsDataURL(file);
                return;
            }

            image.hidden = true;
            placeholder.hidden = false;
            const icon = placeholder.querySelector('[data-preview-icon]');
            const placeholderText = placeholder.querySelector('[data-preview-placeholder]');
            if (icon) {
                icon.className = 'bi bi-file-earmark-pdf';
            }
            if (placeholderText) {
                placeholderText.textContent = 'PDF selected';
            }
        });
    });

    // Passport photo AI verification
    function verifyPassportPhoto(input) {
        const verificationStatus = document.getElementById('passportVerificationStatus');
        const loadingDiv = document.getElementById('passportVerificationLoading');
        const successDiv = document.getElementById('passportVerificationSuccess');
        const errorDiv = document.getElementById('passportVerificationError');
        const errorMessage = document.getElementById('passportVerificationErrorMessage');

        if (!verificationStatus) return;

        // Show loading state
        verificationStatus.style.display = 'block';
        loadingDiv.style.display = 'block';
        successDiv.style.display = 'none';
        errorDiv.style.display = 'none';

        const formData = new FormData();
        formData.append('passport_photo', input.files[0]);

        fetch('{{ route("students.profile.verify-passport-photo") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';

            if (data.success) {
                successDiv.style.display = 'block';
                errorDiv.style.display = 'none';
            } else {
                successDiv.style.display = 'none';
                errorDiv.style.display = 'block';
                errorMessage.textContent = data.message || 'Verification failed. Please try again.';
            }
        })
        .catch(error => {
            loadingDiv.style.display = 'none';
            successDiv.style.display = 'none';
            errorDiv.style.display = 'block';
            errorMessage.textContent = 'Verification service temporarily unavailable. Please try again later.';
            console.error('Verification error:', error);
        });
    }

    function updateIdentificationFields() {
        const type = document.getElementById('identificationTypeSelect').value;
        const isSelected = type === 'ID' || type === 'Passport';
        document.getElementById('idNumberDiv').hidden = !isSelected;
        document.getElementById('idPhotoDiv').hidden = !isSelected;
        document.getElementById('idNumberLabel').textContent = type === 'Passport' ? 'Passport number' : 'National ID number';
        document.getElementById('idPhotoLabel').textContent = type === 'Passport' ? 'Passport image' : 'National ID image';
    }

    function updateEducationFields() {
        const level = document.getElementById('educationLevelSelect').value;
        document.getElementById('senior3DocumentDiv').hidden = level !== 'Senior 3';
        document.getElementById('senior6DocumentDiv').hidden = level !== 'Senior 6';
        document.getElementById('otherSupportingDocumentDiv').hidden = level !== 'Other';
    }

    document.getElementById('identificationTypeSelect').addEventListener('change', updateIdentificationFields);
    document.getElementById('educationLevelSelect').addEventListener('change', updateEducationFields);
    updateIdentificationFields();
    updateEducationFields();
    showStep(activeStep, false);

    function passwordIsStrong(password) {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(password);
    }

    const passwordInput = document.getElementById('passwordInput');
    const passwordConfirmInput = document.getElementById('passwordConfirmInput');
    const passwordHelp = document.getElementById('passwordHelp');

    function checkPassword() {
        const password = passwordInput.value;
        const confirmation = passwordConfirmInput.value;

        if (password && !passwordIsStrong(password)) {
            passwordHelp.textContent = 'Use at least 8 characters with uppercase and lowercase letters, a number, and a symbol.';
            return false;
        }

        if (password && confirmation && password !== confirmation) {
            passwordHelp.textContent = 'The passwords do not match.';
            return false;
        }

        passwordHelp.textContent = '';
        return true;
    }

    passwordInput.addEventListener('input', checkPassword);
    passwordConfirmInput.addEventListener('input', checkPassword);
    form.addEventListener('submit', function (event) {
        if (!checkPassword()) {
            event.preventDefault();
            showStep(3, true);
        }
    });
});
</script>
@endsection
