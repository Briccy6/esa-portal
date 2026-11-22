@extends('students.app')

@section('title', 'Dashboard')

@section('content')
<style>
    :root {
        --primary: #1e40af;
        --primary-light: #3b82f6;
        --primary-dark: #1e3a8a;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #0ea5e9;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --bg-card: #ffffff;
        --bg-hover: #f8fafc;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --radius-md: 8px;
        --radius-lg: 12px;
    }

    /* Welcome Section */
    .welcome-header {
        margin-bottom: 1rem;
    }

    .welcome-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.375rem;
        line-height: 1.3;
    }

    .welcome-subtitle {
        font-size: 0.9375rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .quick-access-section {
        margin-bottom: 1.75rem;
    }

    .quick-access-heading {
        margin-bottom: 0.85rem;
        color: var(--text-primary);
        font-size: 1rem;
        font-weight: 700;
    }

    .quick-access-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.9rem;
    }

    .quick-access-card {
        display: flex;
        min-width: 0;
        min-height: 96px;
        align-items: center;
        gap: 0.85rem;
        padding: 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        background: var(--bg-card);
        box-shadow: var(--shadow-sm);
        color: var(--text-primary);
        text-decoration: none;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .quick-access-card:hover {
        transform: translateY(-2px);
        border-color: #93b4f7;
        box-shadow: var(--shadow-md);
        color: var(--text-primary);
    }

    .quick-access-card:focus-visible {
        outline: 3px solid rgba(59, 130, 246, .35);
        outline-offset: 2px;
    }

    .quick-access-icon {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        border-radius: 12px;
        background: #eff6ff;
        color: var(--primary);
        font-size: 1.2rem;
    }

    .quick-access-copy {
        min-width: 0;
        flex: 1;
    }

    .quick-access-copy strong,
    .quick-access-copy span {
        display: block;
    }

    .quick-access-copy strong {
        margin-bottom: 0.2rem;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .quick-access-copy span {
        color: var(--text-secondary);
        font-size: 0.76rem;
        line-height: 1.4;
    }

    .quick-access-arrow {
        flex: 0 0 auto;
        color: var(--text-muted);
    }

    .profile-reminder-modal .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 18px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
    }

    .profile-reminder-modal .modal-header {
        align-items: flex-start;
        padding: 1.5rem 1.5rem 0;
        border: 0;
    }

    .profile-reminder-icon {
        display: grid;
        width: 48px;
        height: 48px;
        place-items: center;
        border-radius: 14px;
        background: #fff7e6;
        color: #b7791f;
        font-size: 1.25rem;
    }

    .profile-reminder-modal .modal-body {
        padding: 1rem 1.5rem 1.5rem;
    }

    .profile-reminder-eyebrow {
        margin: 0 0 .45rem;
        color: #b7791f;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .profile-reminder-title {
        margin: 0 0 .6rem;
        color: var(--text-primary);
        font-size: 1.45rem;
        font-weight: 750;
        letter-spacing: -.03em;
    }

    .profile-reminder-copy {
        margin: 0;
        color: var(--text-secondary);
        line-height: 1.65;
    }

    .profile-deadline {
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        margin-top: 1rem;
        padding: .8rem .9rem;
        border: 1px solid #f2d69a;
        border-radius: 10px;
        background: #fffaf0;
        color: #805a18;
        font-size: .86rem;
        font-weight: 700;
    }

    .profile-missing-fields {
        margin-top: 1rem;
        color: var(--text-secondary);
        font-size: .84rem;
    }

    .profile-missing-fields ul {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin: .55rem 0 0;
        padding: 0;
        list-style: none;
    }

    .profile-missing-fields li {
        padding: .3rem .55rem;
        border-radius: 99px;
        background: #f1f5f9;
        color: #475569;
    }

    .profile-countdown-label {
        display: block;
        margin: 1.15rem 0 .55rem;
        color: #64748b;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .profile-countdown {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .55rem;
    }

    .profile-countdown-unit {
        padding: .65rem .35rem;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        text-align: center;
    }

    .profile-countdown-unit strong,
    .profile-countdown-unit span {
        display: block;
    }

    .profile-countdown-unit strong {
        color: #0f172a;
        font-size: 1.2rem;
        font-variant-numeric: tabular-nums;
        line-height: 1.2;
    }

    .profile-countdown-unit span {
        margin-top: .2rem;
        color: #64748b;
        font-size: .64rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .profile-gate {
        display: grid;
        min-height: min(68vh, 640px);
        place-items: center;
        padding: clamp(1rem, 4vw, 3rem);
        border: 1px solid #dbe4ef;
        border-radius: 22px;
        background:
            radial-gradient(ellipse at 12% 0%, rgba(245, 158, 11, .13), transparent 38%),
            linear-gradient(145deg, #f8fafc, #edf3fa);
    }

    .profile-gate-card {
        width: min(100%, 700px);
        padding: clamp(1.5rem, 5vw, 3.25rem);
        border: 1px solid rgba(255, 255, 255, .9);
        border-radius: 20px;
        background: rgba(255, 255, 255, .94);
        box-shadow: 0 24px 70px rgba(15, 23, 42, .12);
    }

    .profile-gate-icon {
        display: grid;
        width: 54px;
        height: 54px;
        margin-bottom: 1.4rem;
        place-items: center;
        border-radius: 16px;
        background: #fff3d6;
        color: #a16207;
        font-size: 1.45rem;
    }

    .profile-gate-title {
        margin: 0 0 .65rem;
        color: #0f172a;
        font-size: clamp(1.5rem, 4vw, 2rem);
        font-weight: 800;
        letter-spacing: -.04em;
    }

    .profile-gate-copy {
        max-width: 560px;
        margin-bottom: 1.25rem;
        color: #475569;
        line-height: 1.7;
    }

    .profile-gate-card .profile-missing-fields {
        margin-bottom: 1.5rem;
    }

    .profile-reminder-modal .modal-footer {
        gap: .5rem;
        padding: 0 1.5rem 1.5rem;
        border: 0;
    }

    .profile-reminder-modal .modal-footer .btn {
        margin: 0;
    }

    @media (max-width: 991.98px) {
        .quick-access-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary);
    }

    /* Info Cards */
    .info-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        height: 100%;
    }

    .info-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--bg-hover);
    }

    .info-card-header h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card-header h4 i {
        color: var(--primary);
    }

    .info-card-body {
        padding: 1.25rem;
    }

    /* Token Balance Display */
    .token-overview {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-md);
        padding: 1.25rem;
        color: #fff;
    }

    .token-balance-display {
        margin-bottom: 1rem;
    }

    .balance-amount {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .balance-amount .token-icon {
        font-size: 2rem;
        opacity: 0.9;
    }

    .balance-amount .balance-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .balance-amount .balance-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .balance-details {
        display: flex;
        gap: 1.5rem;
        font-size: 0.875rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .token-actions .btn {
        margin-bottom: 0.5rem;
        min-height: 40px;
    }

    /* Subscription Cards */
    .subscription-card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.25rem;
        background: var(--bg-card);
        transition: all 0.2s ease;
        height: 100%;
    }

    .subscription-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .subscription-card.recommended {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .recommended-badge {
        background: linear-gradient(135deg, var(--warning), #d97706);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-bottom: 0.75rem;
    }

    .subscription-header h5 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.375rem;
    }

    .subscription-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
    }

    .subscription-price .price-period {
        font-size: 0.875rem;
        font-weight: 400;
        color: var(--text-secondary);
    }

    .subscription-tokens {
        font-size: 0.9375rem;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
    }

    .subscription-tokens i {
        color: var(--warning);
    }

    .feature-item {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Live Class Alert */
    .live-class-alert {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #fff;
    }

    .live-class-icon {
        font-size: 2rem;
        opacity: 0.9;
    }

    .live-class-content {
        flex: 1;
    }

    .live-class-content h5 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .live-class-content p {
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
        opacity: 0.9;
    }

    .live-class-content small {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .btn-join {
        background: #fff;
        color: var(--primary);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.875rem;
        min-height: 40px;
    }

    .btn-join:hover {
        background: var(--bg-hover);
    }

    /* Announcement Items */
    .announcement-item {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .announcement-item:last-child {
        border-bottom: none;
    }

    .announcement-content h6 {
        font-size: 0.9375rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .announcement-body {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
        line-height: 1.5;
        max-height: 100px;
        overflow-y: auto;
    }

    .announcement-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Exam Items */
    .exam-item {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .exam-item:last-child {
        border-bottom: none;
    }

    .exam-content h6 {
        font-size: 0.9375rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .exam-date {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .exam-tokens {
        font-size: 0.8125rem;
        color: var(--text-muted);
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .status-badge.completed {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.insufficient-tokens {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Resource Cards */
    .alx-card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        overflow: hidden;
        background: var(--bg-card);
        transition: all 0.2s ease;
        height: 100%;
    }

    .alx-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .alx-card-header {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        position: relative;
    }

    .alx-card-header.resources {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }

    .alx-share-btn {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 0.375rem;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .alx-share-btn:hover {
        background: #fff;
        color: var(--primary);
    }

    .alx-card-title {
        font-size: 0.9375rem;
        font-weight: 600;
        margin-bottom: 0.375rem;
        color: var(--text-primary);
        padding-right: 2rem;
    }

    .alx-card-subtitle {
        font-size: 0.8125rem;
        color: var(--text-secondary);
    }

    .alx-card-body {
        padding: 1rem;
    }

    .alx-meta-info {
        display: flex;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    .alx-meta-item {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .alx-type-badges {
        display: flex;
        gap: 0.375rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }

    .alx-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .alx-card-price {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .alx-card-price.free {
        color: var(--success);
    }

    .alx-card-price.paid {
        color: var(--primary);
    }

    .alx-card-actions {
        display: flex;
        gap: 0.5rem;
    }

    .alx-btn {
        flex: 1;
        padding: 0.5rem;
        border-radius: var(--radius-md);
        font-size: 0.8125rem;
        font-weight: 500;
        text-align: center;
        text-decoration: none;
        min-height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
    }

    .alx-btn-secondary {
        background: var(--bg-hover);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .alx-btn-secondary:hover {
        background: var(--border-color);
    }

    .alx-btn-success {
        background: var(--success);
        color: #fff;
        border: none;
    }

    .alx-btn-success:hover {
        background: #059669;
    }

    /* Countdown Timer */
    .countdown-timer {
        text-align: center;
    }

    .countdown-display {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin: 1.5rem 0;
        flex-wrap: wrap;
    }

    .countdown-segment {
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.75rem 1rem;
        min-width: 70px;
    }

    .countdown-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .countdown-label {
        font-size: 0.6875rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Payment Modal */
    .payment-modal {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
    }

    .payment-form label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-primary);
    }

    /* Responsive Styles */
    @media (max-width: 1024px) {
        .welcome-title {
            font-size: 1.375rem;
        }

        .balance-amount .balance-number {
            font-size: 2.25rem;
        }
    }

    @media (max-width: 768px) {
        .welcome-header {
            margin-bottom: 1.25rem;
        }

        .welcome-title {
            font-size: 1.25rem;
        }

        .welcome-subtitle {
            font-size: 0.875rem;
        }

        .section-title {
            font-size: 1rem;
        }

        .info-card-header {
            padding: 0.875rem 1rem;
        }

        .info-card-header h4 {
            font-size: 0.9375rem;
        }

        .info-card-body {
            padding: 1rem;
        }

        .token-overview {
            padding: 1rem;
        }

        .balance-amount .balance-number {
            font-size: 2rem;
        }

        .balance-details {
            flex-direction: column;
            gap: 0.5rem;
        }

        .countdown-display {
            gap: 0.5rem;
        }

        .countdown-segment {
            min-width: 55px;
            padding: 0.5rem 0.75rem;
        }

        .countdown-value {
            font-size: 1.25rem;
        }

        .countdown-label {
            font-size: 0.625rem;
        }

        .live-class-alert {
            flex-direction: column;
            text-align: center;
        }

        .live-class-icon {
            font-size: 1.5rem;
        }

        .btn-join {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .welcome-title {
            font-size: 1.125rem;
        }

        .welcome-subtitle {
            font-size: 0.8125rem;
        }

        .section-title {
            font-size: 0.9375rem;
        }

        .info-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .info-card-header .btn {
            width: 100%;
        }

        .token-actions .btn {
            width: 100%;
        }

        .subscription-card {
            padding: 1rem;
        }

        .alx-card-actions {
            flex-direction: column;
        }

        .alx-btn {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .quick-access-grid {
            grid-template-columns: 1fr;
            gap: .65rem;
        }

        .quick-access-card {
            min-height: 82px;
            padding: .8rem;
        }

        .profile-reminder-modal .modal-header {
            padding: 1.1rem 1.1rem 0;
        }

        .profile-reminder-modal .modal-body {
            padding: .9rem 1.1rem 1.1rem;
        }

        .profile-reminder-modal .modal-footer {
            padding: 0 1.1rem 1.1rem;
        }

        .profile-reminder-title {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 360px) {
        .countdown-display {
            gap: 0.375rem;
        }

        .countdown-segment {
            min-width: 45px;
            padding: 0.375rem 0.5rem;
        }

        .countdown-value {
            font-size: 1.125rem;
        }
    }
</style>

<div class="container-fluid p-0">
    @if($profileAccessRestricted)
        <section class="profile-gate" aria-labelledby="profileGateTitle">
            <div class="profile-gate-card">
                <div class="profile-gate-icon" aria-hidden="true">
                    <i class="bi bi-person-lock"></i>
                </div>
                <p class="profile-reminder-eyebrow">Profile completion required</p>
                <h1 class="profile-gate-title" id="profileGateTitle">Complete your profile to continue</h1>
                <p class="profile-gate-copy">
                    The profile deadline has passed. Until your details are complete, access to the student portal is paused.
                    Update your profile to restore access to your courses, test skills, and other student services.
                </p>
                @if(count($profileMissingFields))
                    <div class="profile-missing-fields">
                        <strong>Still needed</strong>
                        <ul>
                            @foreach($profileMissingFields as $field)
                                <li>{{ $field }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <a href="{{ route('students.profile') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-pencil-square me-2" aria-hidden="true"></i>Complete your profile
                </a>
            </div>
        </section>
    @else
    <!-- Welcome Header -->
    <div class="welcome-header">
        <h1 class="welcome-title">Welcome back, {{ $student->full_name ?? 'Student' }}!</h1>
        <p class="welcome-subtitle">Track your academic progress and manage your fees</p>
    </div>

    @if($showProfileReminder)
        <div class="modal fade profile-reminder-modal" id="profileReminderModal" tabindex="-1"
             aria-labelledby="profileReminderTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="profile-reminder-icon" aria-hidden="true">
                            <i class="bi bi-person-vcard"></i>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="profile-reminder-eyebrow">Student profile update</p>
                        <h2 class="profile-reminder-title" id="profileReminderTitle">Please complete your profile</h2>
                        <p class="profile-reminder-copy">
                            Update your personal, contact, and identification information so the school has your correct details.
                        </p>
                        <div class="profile-deadline">
                            <i class="bi bi-clock-history" aria-hidden="true"></i>
                            <span>Please complete this before {{ $profileDeadline->format('l, F j, Y \a\t g:i A') }} (Kigali time).</span>
                        </div>
                        <span class="profile-countdown-label">Time remaining</span>
                        <div class="profile-countdown" id="profileDeadlineCountdown"
                             data-deadline="{{ $profileDeadline->toIso8601String() }}"
                             role="timer" aria-label="Time remaining to complete your profile">
                            <div class="profile-countdown-unit"><strong data-countdown-days>--</strong><span>Days</span></div>
                            <div class="profile-countdown-unit"><strong data-countdown-hours>--</strong><span>Hours</span></div>
                            <div class="profile-countdown-unit"><strong data-countdown-minutes>--</strong><span>Minutes</span></div>
                            <div class="profile-countdown-unit"><strong data-countdown-seconds>--</strong><span>Seconds</span></div>
                        </div>
                        @if(count($profileMissingFields))
                            <div class="profile-missing-fields">
                                <strong>Still needed</strong>
                                <ul>
                                    @foreach($profileMissingFields as $field)
                                        <li>{{ $field }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Remind me later</button>
                        <a href="{{ route('students.profile') }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Complete profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Optional Fees Modal -->
    <div class="modal fade" id="optionalFeesModal" tabindex="-1" aria-labelledby="optionalFeesTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="optionalFeesTitle">
                        <i class="fas fa-info-circle me-2"></i>Optional Activities
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">You have optional activities available. Please indicate whether you would like to participate:</p>
                    <div id="optionalFeesList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Decide Later</button>
                    <button type="button" class="btn btn-primary" id="submitOptionalFees">Submit Responses</button>
                </div>
            </div>
        </div>
    </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var reminderModal = document.getElementById('profileReminderModal');
                if (reminderModal && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(reminderModal).show();
                }

                var countdown = document.getElementById('profileDeadlineCountdown');
                if (!countdown) {
                    return;
                }

                var deadline = new Date(countdown.dataset.deadline).getTime();
                function updateCountdown() {
                    var remainingSeconds = Math.ceil((deadline - Date.now()) / 1000);
                    if (remainingSeconds <= 0) {
                        window.location.reload();
                        return;
                    }

                    var values = {
                        days: Math.floor(remainingSeconds / 86400),
                        hours: Math.floor((remainingSeconds % 86400) / 3600),
                        minutes: Math.floor((remainingSeconds % 3600) / 60),
                        seconds: remainingSeconds % 60
                    };

                    Object.keys(values).forEach(function (unit) {
                        var element = countdown.querySelector('[data-countdown-' + unit + ']');
                        if (element) {
                            element.textContent = String(values[unit]).padStart(2, '0');
                        }
                    });
                }

                updateCountdown();
                window.setInterval(updateCountdown, 1000);

                // Check for optional fees
                fetch('{{ route('student.optional-fees.pending') }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.pending && data.fees && data.fees.length > 0) {
                            const modal = document.getElementById('optionalFeesModal');
                            const feesList = document.getElementById('optionalFeesList');
                            
                            data.fees.forEach(fee => {
                                const feeHtml = `
                                    <div class="card mb-3" data-fee-type="${fee.type}" data-fee-id="${fee.fee_id}">
                                        <div class="card-body">
                                            <h6 class="mb-2">${fee.name}</h6>
                                            <p class="mb-2">Amount: ${fee.amount} RWF</p>
                                            ${fee.due_date ? `<small class="text-muted">Due: ${fee.due_date}</small>` : ''}
                                            <div class="mt-3">
                                                <label class="form-label">Your response:</label>
                                                <select class="form-select fee-response">
                                                    <option value="pending" ${fee.current_response === 'pending' ? 'selected' : ''}>Decide later</option>
                                                    <option value="accepted" ${fee.current_response === 'accepted' ? 'selected' : ''}>I will participate</option>
                                                    <option value="declined" ${fee.current_response === 'declined' ? 'selected' : ''}>I will not participate</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                feesList.innerHTML += feeHtml;
                            });

                            if (modal && window.bootstrap && window.bootstrap.Modal) {
                                window.bootstrap.Modal.getOrCreateInstance(modal).show();
                            }
                        }
                    });

                // Handle optional fees submission
                document.getElementById('submitOptionalFees').addEventListener('click', function() {
                    const feeCards = document.querySelectorAll('#optionalFeesList .card');
                    const responses = [];

                    feeCards.forEach(card => {
                        const feeType = card.dataset.feeType;
                        const feeId = card.dataset.feeId;
                        const response = card.querySelector('.fee-response').value;

                        if (response !== 'pending') {
                            responses.push({
                                course_term_branch_fee_id: feeId,
                                fee_type: feeType,
                                response: response
                            });
                        }
                    });

                    if (responses.length > 0) {
                        responses.forEach(resp => {
                            fetch('{{ route('student.optional-fees.respond') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(resp)
                            });
                        });

                        const modal = document.getElementById('optionalFeesModal');
                        if (modal && window.bootstrap && window.bootstrap.Modal) {
                            window.bootstrap.Modal.getInstance(modal).hide();
                        }

                        // Show success message
                        alert('Your responses have been recorded. Thank you!');
                    }
                });
            });
        </script>
    @endif

    <section class="quick-access-section" aria-labelledby="quickAccessTitle">
        <h2 class="quick-access-heading" id="quickAccessTitle">Quick access</h2>
        <div class="quick-access-grid">
            <a class="quick-access-card" href="{{ route('student.courses') }}">
                <span class="quick-access-icon" aria-hidden="true"><i class="bi bi-journal-text"></i></span>
                <span class="quick-access-copy"><strong>My courses</strong><span>Course materials and details</span></span>
                <i class="bi bi-arrow-up-right quick-access-arrow" aria-hidden="true"></i>
            </a>
            <a class="quick-access-card" href="{{ route('student.timetables.index') }}">
                <span class="quick-access-icon" aria-hidden="true"><i class="bi bi-calendar2-week"></i></span>
                <span class="quick-access-copy"><strong>Timetable</strong><span>See your weekly schedule</span></span>
                <i class="bi bi-arrow-up-right quick-access-arrow" aria-hidden="true"></i>
            </a>
            <a class="quick-access-card" href="{{ route('student.exams') }}">
                <span class="quick-access-icon" aria-hidden="true"><i class="bi bi-patch-question"></i></span>
                <span class="quick-access-copy"><strong>Test Skills</strong><span>Check what you have learned</span></span>
                <i class="bi bi-arrow-up-right quick-access-arrow" aria-hidden="true"></i>
            </a>
            <a class="quick-access-card" href="{{ route('student.report-cards.index') }}">
                <span class="quick-access-icon" aria-hidden="true"><i class="bi bi-award"></i></span>
                <span class="quick-access-copy"><strong>Report cards</strong><span>View your academic results</span></span>
                <i class="bi bi-arrow-up-right quick-access-arrow" aria-hidden="true"></i>
            </a>
            <a class="quick-access-card" href="{{ route('student.resources.index') }}">
                <span class="quick-access-icon" aria-hidden="true"><i class="bi bi-collection"></i></span>
                <span class="quick-access-copy"><strong>Study references</strong><span>Open your learning resources</span></span>
                <i class="bi bi-arrow-up-right quick-access-arrow" aria-hidden="true"></i>
            </a>
            <a class="quick-access-card" href="{{ route('student.payment-schedule') }}">
                <span class="quick-access-icon" aria-hidden="true"><i class="bi bi-credit-card"></i></span>
                <span class="quick-access-copy"><strong>Payments</strong><span>Check your payment schedule</span></span>
                <i class="bi bi-arrow-up-right quick-access-arrow" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <!-- Attendance Details Modal -->
    <div class="modal fade" id="attendanceDetailsModal" tabindex="-1" aria-labelledby="attendanceDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceDetailsModalLabel">
                        <i class="bi bi-calendar-check me-2"></i>Attendance Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Lesson</th>
                                    <th>Teacher</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedAttendances as $attendance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($attendance->marked_at)->format('d M Y H:i') }}</td>
                                        <td>{{ $attendance->lesson->title ?? '-' }}</td>
                                        <td>{{ $attendance->lesson->teacher->teacher_name ?? '-' }}</td>
                                        <td>
                                            @if($attendance->status === 'P')
                                                <span class="badge bg-success">Present</span>
                                            @elseif($attendance->status === 'L')
                                                <span class="badge bg-warning text-dark">Late</span>
                                            @else
                                                <span class="badge bg-danger">Absent</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->status === 'present')
                                                <a href="{{ route('lesson.show', $attendance->lesson_id) }}" class="btn btn-sm btn-primary">View Lesson</a>
                                            @else
                                                @if($attendance->lesson && $attendance->lesson->quiz_link)
                                                    <a href="{{ $attendance->lesson->quiz_link }}" class="btn btn-sm btn-warning" target="_blank">Class Quiz</a>
                                                @endif
                                                @if($attendance->lesson && $attendance->lesson->marking_guide_link)
                                                    <a href="{{ $attendance->lesson->marking_guide_link }}" class="btn btn-sm btn-info" target="_blank">Marking Guide</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $hasStudentCard    = \App\Models\StudentCard::where('student_id', $student->id)->exists();
        $hasPaidStudentCard = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'student_card')
            ->whereIn('status', ['successful', 'paid'])
            ->exists();
    @endphp

    <!-- Student Card Notifications -->
    @if($hasStudentCard)
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-check-circle-fill fs-4 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-1">Your Student Card is Ready!</h6>
                    <p class="mb-2 small">Your student card has been generated and is ready for pickup at the administration office.</p>
                    <small class="text-muted">Visit the administration office during business hours with a valid ID for verification.</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif($hasPaidStudentCard)
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-info-circle-fill fs-4 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-1">Student Card Payment Received</h6>
                    <p class="mb-0 small">Your student card payment has been recorded. The card will be generated soon and you'll be notified when it's ready.</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Attendance Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-primary d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check"></i>
                    <span>Attendance Percentage: <strong>{{ $attendancePercent }}%</strong></span>
                </div>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attendanceDetailsModal">
                    View Details
                </button>
            </div>
        </div>
    </div>

    <!-- Student Information -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-person-circle"></i>Your Information
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Full Name:</strong> {{ $student->full_name }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Registration Number:</strong> {{ $student->reg_number }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Academic Year:</strong> {{ $student->academicYear->year ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Shift:</strong> <span class="badge bg-info">{{ ucfirst($student->shift ?? 'Day') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timetable Quick Access -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-calendar3"></i>Your Timetable
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-body">
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-check display-4 text-primary mb-3"></i>
                        <h5>View Your Class Schedule</h5>
                        <p class="text-muted mb-3">Access your weekly timetable, see class times, rooms, and teacher assignments.</p>
                        <a href="{{ route('student.timetables.index') }}" class="btn btn-primary">
                            <i class="bi bi-calendar3 me-2"></i>Go to Timetables
                        </a>
                    </div>
                    @if(isset($scheduledTests) && $scheduledTests->isNotEmpty())
                        <div class="border-top px-3 pt-3 pb-2">
                            <h5 class="mb-3"><i class="bi bi-shield-check me-2"></i>Scheduled tests &amp; fee eligibility</h5>
                            <div class="list-group">
                                @foreach($scheduledTests as $testSession)
                                    <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <div>
                                            <strong>{{ $testSession->lesson->title ?? 'Scheduled test' }}</strong>
                                            <div class="small text-muted">
                                                {{ $testSession->timetable_title }} · {{ $testSession->term_name }} ·
                                                {{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$testSession->day_of_week] ?? 'Day to be confirmed' }} ·
                                                {{ \Carbon\Carbon::parse($testSession->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($testSession->end_time)->format('H:i') }}
                                            </div>
                                        </div>
                                        @if($testSession->test_eligibility['eligible'])
                                            <span class="badge bg-success">Eligible · {{ number_format($testSession->test_eligibility['paid'], 1) }}% paid</span>
                                        @else
                                            <div class="text-end">
                                                <span class="badge bg-danger">Not eligible · {{ number_format($testSession->test_eligibility['paid'], 1) }}% paid</span>
                                                <div class="small text-danger mt-1">{{ $testSession->test_eligibility['reason'] }} Required: {{ number_format($testSession->test_eligibility['minimum'], 1) }}%.</div>
                                                <a class="small" href="{{ route('student.payment-schedule') }}">View fees and pay the remaining balance</a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Overview Cards - Term Based -->
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h3 class="section-title mb-0">
                <i class="bi bi-credit-card-2-front"></i>Term-Based Fee Overview
            </h3>
            <a href="{{ route('student.payment-transcript') }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-printer me-1"></i> View & print payment transcript
            </a>
        </div>

        @if(empty($termFeesData))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>All Fees Paid!</strong> You have no outstanding fees. Congratulations!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @else
            @foreach($termFeesData as $termData)
                <div class="col-12 mb-4">
                    <div class="card border-primary shadow-sm mb-3">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        {{ $termData['term']->name }}
                                    </h5>
                                    <small class="d-block">
                                        {{ $termData['term']->start_date->format('M d, Y') }} -
                                        {{ $termData['term']->end_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0">{{ number_format($termData['total_fees'], 0) }} RWF</div>
                                    <small>Total Fees</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Payment Progress</span>
                                    <span class="badge bg-info">{{ $termData['payment_percentage'] }}%</span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ $termData['payment_percentage'] }}%"
                                         aria-valuenow="{{ $termData['payment_percentage'] }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                        <small class="text-white fw-bold">
                                            {{ number_format($termData['total_paid'], 0) }} RWF Paid
                                        </small>
                                    </div>
                                </div>
                                <small class="text-muted">Remaining: {{ number_format($termData['total_remaining'], 0) }} RWF</small>
                            </div>

                            <div class="row">
                                @foreach($termData['fees'] as $fee)
                                <div class="col-lg-6 col-md-12 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">{{ $fee['name'] }}</h6>
                                                <span class="badge bg-secondary">{{ $fee['type'] }}</span>
                                            </div>

                                            <div class="mb-2">
                                                <span class="text-muted small">Fee Amount:</span>
                                                <strong> {{ number_format($fee['total'], 0) }} RWF</strong>
                                            </div>
                                            <div class="mb-2 text-success">
                                                <span class="small">Paid:</span>
                                                <strong> {{ number_format($fee['paid'], 0) }} RWF</strong>
                                            </div>
                                            <div class="mb-2 text-danger">
                                                <span class="small">Remaining:</span>
                                                <strong> {{ number_format($fee['remaining'], 0) }} RWF</strong>
                                            </div>

                                            <div class="progress mt-2" style="height: 18px;">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{ $fee['total'] > 0 ? (($fee['paid'] / $fee['total']) * 100) : 0 }}%"
                                                     aria-valuenow="{{ $fee['total'] > 0 ? (($fee['paid'] / $fee['total']) * 100) : 0 }}"
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>

                                            @if($fee['remaining'] > 0)
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary mt-2 w-100"
                                                    onclick="try { selectTermAndFee({{ $termData['term']->id }}, '{{ $fee['type'] }}', {{ $fee['remaining'] }}); } catch(e) { console.error('Payment selection error:', e); alert('Unable to select payment. Please try again.'); }">
                                                <i class="fas fa-money-bill-wave me-1"></i>Pay {{ number_format($fee['remaining'], 0) }} RWF
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <script>
    function selectTermAndFee(termId, feeType, remaining) {
        const feeTypeInput = document.getElementById('feeTypeInput');
        const termIdInput = document.getElementById('termIdInput');
        const selectedFeeLabel = document.getElementById('selectedFeeLabel');
        const selectedTermLabel = document.getElementById('selectedTermLabel');
        const payAmountInput = document.getElementById('payAmountInput');

        if (feeTypeInput) {
            feeTypeInput.value = feeType;
        }
        if (termIdInput) {
            termIdInput.value = termId;
        }
        if (payAmountInput) {
            payAmountInput.value = remaining;
        }

        if (selectedFeeLabel) {
            selectedFeeLabel.textContent = 'Fee: ' + formatFeeType(feeType);
        }

        if (selectedTermLabel) {
            selectedTermLabel.textContent = 'Term: ' + (getTermName(termId) || 'Selected term');
        }

        const modalElement = document.getElementById('payFeeModal');
        if (modalElement) {
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    new bootstrap.Modal(modalElement).show();
                } else {
                    modalElement.classList.add('show');
                    modalElement.style.display = 'block';
                    document.body.classList.add('modal-open');
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
            } catch (error) {
                console.error('Error showing payment modal:', error);
                alert('Error opening payment modal. Please try again.');
            }
        } else {
            console.error('Payment modal not found');
            alert('Payment modal not found. Please refresh the page.');
        }
    }

    function formatFeeType(feeType) {
        switch (feeType) {
            case 'registration_fee': return 'Registration Fee';
            case 'school_fee': return 'Tuition Fee';
            case 'practical_fee': return 'Practical Fee';
            case 'trip_fee': return 'Trip Fee';
            case 'student_card': return 'Student Card Fee';
            default: return feeType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }
    }

    function getTermName(termId) {
        const termElement = document.querySelector('[data-term-id="' + termId + '"]');
        return termElement ? termElement.textContent.trim() : null;
    }
    </script>

    <!-- Token Management Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-coin"></i>Token Management
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4>
                        <i class="bi bi-wallet2"></i>Your Token Balance
                    </h4>
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-gear"></i> Manage Tokens
                    </a>
                </div>
                <div class="info-card-body">
                    @if(!empty($activeSubscription))
                        <div class="alert alert-success mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-crown-fill fs-4"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Active Subscription</h6>
                                    <p class="mb-1 small">
                                        <strong>{{ $activeSubscription->subscription->name }}</strong> —
                                        {{ $activeSubscription->days_remaining }} days remaining
                                    </p>
                                    <small class="text-muted">Valid until: {{ $activeSubscription->ends_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="token-overview">
                        <div class="token-balance-display">
                            <div class="balance-amount">
                                <span class="token-icon"><i class="bi bi-coin"></i></span>
                                <span class="balance-number">{{ $student->token_balance ?? 0 }}</span>
                                <span class="balance-label">tokens</span>
                            </div>
                            <div class="balance-details">
                                <div class="detail-item">
                                    <i class="bi bi-arrow-down-circle"></i>
                                    <span>Total Earned: {{ $student->token ? $student->token->total_earned : 0 }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-arrow-up-circle"></i>
                                    <span>Total Spent: {{ $student->token ? $student->token->total_spent : 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="token-actions">
                            @if(empty($activeSubscription))
                                <a href="{{ route('subscriptions.index') }}" class="btn btn-light w-100">
                                    <i class="bi bi-star me-2"></i>Get Subscription
                                </a>
                            @endif
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-light w-100">
                                <i class="bi bi-coins me-1"></i>Buy Individual Tokens
                            </a>
                            <a href="{{ route('tokens.dashboard') }}" class="btn btn-outline-light w-100">
                                <i class="bi bi-clock-history me-2"></i>View History
                            </a>
                        </div>
                    </div>

                    @if(($student->token_balance ?? 0) < 10 && empty($activeSubscription))
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Low Token Balance!</strong>
                            You have less than 10 tokens. Consider purchasing a subscription for better value.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Report Card Countdown Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-file-earmark-text"></i>Report Card Access
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-clock-history"></i>Report Card Availability</h4>
                    <a href="{{ route('student.report-cards.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> View Report Cards
                    </a>
                </div>
                <div class="info-card-body">
                    <div id="report-card-status">
                        <div class="d-flex align-items-center gap-2">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="small">Checking report card access status...</span>
                        </div>
                    </div>

                    <div id="countdown-container" style="display: none;">
                        <div class="countdown-timer">
                            <h5 class="mb-3">Time Until Report Cards Available</h5>
                            <div class="countdown-display">
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="months">0</div>
                                    <div class="countdown-label">Months</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="weeks">0</div>
                                    <div class="countdown-label">Weeks</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="days">0</div>
                                    <div class="countdown-label">Days</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="hours">0</div>
                                    <div class="countdown-label">Hours</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="minutes">0</div>
                                    <div class="countdown-label">Minutes</div>
                                </div>
                                <div class="countdown-segment">
                                    <div class="countdown-value" id="seconds">0</div>
                                    <div class="countdown-label">Seconds</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted" id="available-date"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Plans Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title">
                <i class="bi bi-star"></i>Subscription Plans
            </h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-crown"></i>Get More Tokens with Subscriptions</h4>
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> View All Plans
                    </a>
                </div>
                <div class="info-card-body">
                    @if(!empty($activeSubscription))
                        <div class="alert alert-success mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Active Subscription</h6>
                                    <p class="mb-1 small">
                                        <strong>{{ $activeSubscription->subscription->name }}</strong> —
                                        {{ $activeSubscription->days_remaining }} days remaining
                                    </p>
                                    <small class="text-muted">Expires: {{ $activeSubscription->ends_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="subscription-preview">
                        <div class="row">
                            @foreach($subscriptions->take(3) as $subscription)
                            <div class="col-md-4 mb-3">
                                <div class="subscription-card {{ $subscription->is_recommended ? 'recommended' : '' }}">
                                    @if($subscription->is_recommended)
                                        <div class="recommended-badge">
                                            <i class="bi bi-star-fill"></i> Recommended
                                        </div>
                                    @endif
                                    <div class="subscription-header">
                                        <h5>{{ $subscription->name }}</h5>
                                        <div class="subscription-price">
                                            <span class="price-amount">{{ number_format($subscription->price, 0) }} RWF</span>
                                            <span class="price-period"> / {{ $subscription->duration_days }} days</span>
                                        </div>
                                    </div>
                                    <div class="subscription-tokens">
                                        <i class="bi bi-coin"></i> {{ $subscription->tokens_included }} Tokens
                                    </div>
                                    <div class="subscription-features">
                                        @if($subscription->features)
                                            @foreach(array_slice($subscription->features, 0, 3) as $feature)
                                                <div class="feature-item">
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                    <span>{{ Str::limit($feature, 30) }}</span>
                                                </div>
                                            @endforeach
                                            @if(count($subscription->features) > 3)
                                                <div class="feature-item">
                                                    <small class="text-muted">+{{ count($subscription->features) - 3 }} more features</small>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="subscription-action">
                                        @if(!empty($activeSubscription))
                                            <button class="btn btn-outline-secondary w-100" disabled>
                                                <i class="bi bi-check-circle me-1"></i>Already Active
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-primary w-100"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#subscriptionModal"
                                                    data-subscription-id="{{ $subscription->id }}"
                                                    data-subscription-name="{{ $subscription->name }}"
                                                    data-subscription-price="{{ $subscription->price }}"
                                                    data-subscription-tokens="{{ $subscription->tokens_included }}">
                                                <i class="bi bi-cart-plus me-1"></i>Choose Plan
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-grid me-1"></i>View All Subscription Plans
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Class Alert -->
    @if($liveClass)
        <div class="row mb-4">
            <div class="col-12">
                <div class="live-class-alert">
                    <div class="live-class-icon"><i class="bi bi-camera-video"></i></div>
                    <div class="live-class-content">
                        <h5>Upcoming Live Class</h5>
                        <p>{{ $liveClass->title }}</p>
                        <small>Starts at: {{ \Carbon\Carbon::parse($liveClass->start_time)->format('d M Y H:i') }}</small>
                    </div>
                    <div class="live-class-action">
                        <button class="btn-join">
                            <i class="bi bi-play-circle me-1"></i>Join Class
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Live Classes Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title"><i class="bi bi-broadcast"></i>Live Classes</h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-camera-video"></i>Available Live Classes</h4>
                    <a href="{{ route('student.live-classes.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> View All
                    </a>
                </div>
                <div class="info-card-body">
                    <div id="live-classes-container">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted small">Loading live classes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Study Reference Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title"><i class="bi bi-book"></i>Study Reference</h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-journal-text"></i>Educational Resources</h4>
                    <a href="{{ route('student.resources.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-grid"></i> View All
                    </a>
                </div>
                <div class="info-card-body">
                    <div class="row">
                        @forelse($resources as $resource)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="alx-card">
                                    <div class="alx-card-header resources">
                                        <button onclick="try { shareResource({{ $resource->id }}, @json(Str::limit($resource->document_name ?: $resource->video_name ?: 'Resource', 30))); } catch(e) { console.error('Share error:', e); alert('Unable to share resource. Please try again.'); }"
                                                class="alx-share-btn" title="Share this resource">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                        <h6 class="alx-card-title">{{ Str::limit($resource->document_name ?: $resource->video_name ?: 'Educational Resource', 40) }}</h6>
                                        <div class="alx-card-subtitle">
                                            <i class="fas fa-graduation-cap mr-2"></i>
                                            {{ $resource->course->course_name ?? 'General Course' }}
                                        </div>
                                    </div>
                                    <div class="alx-card-body">
                                        <div class="alx-meta-info">
                                            <div class="alx-meta-item">
                                                <i class="fas fa-user-tie text-blue-600"></i>
                                                <span>{{ $resource->teacher->teacher_name ?? 'Expert Teacher' }}</span>
                                            </div>
                                            <div class="alx-meta-item">
                                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                                                <span>{{ $resource->branch->name ?? 'Main Campus' }}</span>
                                            </div>
                                        </div>
                                        <div class="alx-type-badges">
                                            @if($resource->document_blob)
                                                <span class="alx-type-badge bg-blue-100 text-blue-800">
                                                    <i class="fas fa-file-alt mr-1"></i>Document
                                                </span>
                                            @endif
                                            @if($resource->video_blob)
                                                <span class="alx-type-badge bg-red-100 text-red-800">
                                                    <i class="fas fa-video mr-1"></i>Video
                                                </span>
                                            @endif
                                            @if($resource->youtube_link)
                                                <span class="alx-type-badge bg-red-100 text-red-800">
                                                    <i class="fab fa-youtube mr-1"></i>YouTube
                                                </span>
                                            @endif
                                        </div>
                                        <div class="alx-card-price {{ $resource->is_free ? 'free' : 'paid' }}">
                                            @if($resource->is_free)
                                                <i class="fas fa-gift mr-2"></i>FREE
                                            @else
                                                <i class="fas fa-tag mr-2"></i>RWF {{ number_format($resource->price, 0) }}
                                            @endif
                                        </div>
                                        <div class="alx-card-actions">
                                            <a href="{{ route('student.resources.show', $resource->id) }}" class="alx-btn alx-btn-secondary">
                                                <i class="fas fa-eye"></i>View Details
                                            </a>
                                            @if($resource->document_blob || $resource->video_blob)
                                                <a href="{{ $resource->is_free ? route('student.resources.download', $resource->id) : route('student.resources.payment', $resource->id) }}"
                                                   class="alx-btn alx-btn-success">
                                                    <i class="fas fa-{{ $resource->is_free ? 'download' : 'shopping-cart' }} mr-2"></i>
                                                    {{ $resource->is_free ? 'Download Now' : 'Purchase Access' }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4">
                                <i class="fas fa-book text-muted fs-1"></i>
                                <p class="text-muted mt-2">No study resources available at the moment. Check back soon!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Skills Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title"><i class="bi bi-patch-question"></i>Test Skills</h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-journal-check"></i>Upcoming Tests</h4>
                    <a href="{{ route('student.exams') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-list"></i> View All
                    </a>
                </div>
                <div class="info-card-body">
                    <div class="exam-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Practice and assessment</h6>
                                <p class="text-muted mb-0 small">Check upcoming tests and review your results</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark">Upcoming</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NESA Registration Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="section-title"><i class="bi bi-shield-check"></i>NESA Registration</h3>
        </div>
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <h4><i class="bi bi-award"></i>Registration Status</h4>
                    <button class="btn btn-sm btn-info" onclick="try { showNesaModal(); } catch(e) { console.error('NESA modal error:', e); alert('Unable to open NESA status. Please try again.'); }">
                        <i class="bi bi-info-circle"></i> Check Status
                    </button>
                </div>
                <div class="info-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1">National Examination and Assessment Authority</h6>
                            <p class="text-muted mb-0 small">Check your NESA registration status and download forms</p>
                        </div>
                        <div>
                            @if($student->nesa_registered)
                                <span id="nesa-status-badge" class="badge bg-success">Registered</span>
                            @else
                                <span id="nesa-status-badge" class="badge bg-warning text-dark">Not Registered</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Announcements -->
        @if($announcements->count())
            <div class="col-lg-6 mb-4">
                <div class="info-card">
                    <div class="info-card-header">
                        <h4><i class="bi bi-megaphone"></i>Latest Announcements</h4>
                    </div>
                    <div class="info-card-body p-0">
                        @foreach($announcements as $announcement)
                            <div class="announcement-item">
                                <div class="announcement-content">
                                    <h6>{{ $announcement->title }}</h6>
                                    <div class="announcement-body">{!! $announcement->content !!}</div>
                                    @if($announcement->attachment)
                                        <div class="mt-2">
                                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ asset('storage/' . $announcement->attachment) }}">
                                                <i class="bi bi-paperclip me-1"></i>View attachment
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="announcement-meta">
                                    <span>
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($announcement->created_at)->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Test Skills & Results -->
        @if($exams->count())
            <div class="col-lg-6 mb-4">
                <div class="info-card">
                    <div class="info-card-header">
                        <h4><i class="bi bi-clipboard-check"></i>Test Skills & Results</h4>
                    </div>
                    <div class="info-card-body p-0">
                        @foreach($exams as $exam)
                            <div class="exam-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="exam-content">
                                            <h6>{{ $exam->title }}</h6>
                                            <p class="exam-date">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ \Carbon\Carbon::parse($exam->class_date)->format('d M Y') }}
                                            </p>
                                            @if($exam->requiresTokens())
                                                <p class="exam-tokens">
                                                    <i class="bi bi-coins me-1"></i>
                                                    {{ $exam->formatted_token_cost }}
                                                    @if(auth('student')->user()->token)
                                                        | Your balance: {{ auth('student')->user()->token->balance }} tokens
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="exam-status ms-3">
                                        @if(\Carbon\Carbon::parse($exam->class_date)->isPast())
                                            @if(isset($marks[$exam->id]))
                                                <span class="status-badge completed">
                                                    <i class="bi bi-check-circle me-1"></i>Mark: {{ $marks[$exam->id] }}
                                                </span>
                                            @else
                                                <span class="status-badge pending">
                                                    <i class="bi bi-clock me-1"></i>No mark yet
                                                </span>
                                            @endif
                                        @else
                                            @if($exam->requiresTokens() && (!auth('student')->user()->token || !auth('student')->user()->token->canAfford($exam->getCalculatedTokenCost())))
                                                <span class="status-badge insufficient-tokens">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Insufficient tokens
                                                </span>
                                            @else
                                                <a href="{{ route('student.exams.take', $exam->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-play-circle me-1"></i>Start test
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="payFeeModal" tabindex="-1" aria-labelledby="payFeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content payment-modal">
                <form id="payFeeForm" action="{{ route('student.fees.pay.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fee_type" id="feeTypeInput">
                    <input type="hidden" name="term_id" id="termIdInput">
                    <div class="modal-header">
                        <h5 class="modal-title" id="payFeeModalLabel">
                            <i class="bi bi-credit-card me-2"></i>Pay Fee
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2 text-muted small" id="payFeeModalMeta">
                            <span id="selectedFeeLabel">Fee: </span>
                            <span id="selectedTermLabel">Term: </span>
                        </div>
                        <div class="payment-form">
                            <div class="mb-3">
                                <label for="payAmountInput" class="form-label">
                                    <i class="bi bi-currency-exchange me-1"></i>Amount (Rwf)
                                </label>
                                <input type="number" class="form-control form-control-lg" id="payAmountInput" name="pay_amount" min="1" required>
                            </div>
                            <div id="payFeeError" class="alert alert-danger d-none"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-credit-card me-1"></i>Process Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@if(!$profileAccessRestricted)
<script>
function showPaymentModal(button) {
    const feeTypeInput = document.getElementById('feeTypeInput');
    const amountInput  = document.getElementById('payAmountInput');
    if (feeTypeInput) feeTypeInput.value = button.getAttribute('data-feetype');
    if (amountInput)  amountInput.value  = button.getAttribute('data-amount');

    const modalElement = document.getElementById('payFeeModal');
    if (modalElement) {
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                new bootstrap.Modal(modalElement).show();
            } else {
                modalElement.classList.add('show');
                modalElement.style.display = 'block';
                document.body.classList.add('modal-open');
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        } catch (error) {
            alert('Error showing payment modal: ' + error.message);
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {

    // Load optional fees popup
    fetch('{{ route('student.optional-fees.pending') }}')
        .then(response => response.json())
        .then(data => {
            if (data.pending && data.fees && data.fees.length > 0) {
                showOptionalFeesModal(data.fees);
            }
        })
        .catch(error => {
            console.error('Error loading optional fees:', error);
        });

    document.querySelectorAll('.pay-fee-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            showPaymentModal(btn);
        });
    });

    document.getElementById('payFeeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var form      = this;
        var errorDiv  = document.getElementById('payFeeError');
        var submitBtn = form.querySelector('button[type="submit"]');

        submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Processing...';
        submitBtn.disabled  = true;
        errorDiv.classList.add('d-none');
        errorDiv.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':  form.querySelector('[name=_token]').value,
                'Accept':        'application/json',
                'Content-Type':  'application/json'
            },
            body: JSON.stringify({
                student_id: {{ auth('student')->user()->id }},
                fee_type:   document.getElementById('feeTypeInput').value,
                pay_amount: document.getElementById('payAmountInput').value,
                term_id:    document.getElementById('termIdInput').value || null
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.payment_link) {
                window.location.href = data.payment_link;
            } else {
                errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' + (data.message || 'Payment failed.');
                errorDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Payment failed. Please try again.';
            errorDiv.classList.remove('d-none');
        })
        .finally(() => {
            submitBtn.innerHTML = '<i class="bi bi-credit-card me-1"></i>Process Payment';
            submitBtn.disabled  = false;
        });
    });

    // Subscription modal population
    document.querySelectorAll('[data-bs-target="#subscriptionModal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            const id     = this.getAttribute('data-subscription-id');
            const name   = this.getAttribute('data-subscription-name');
            const price  = this.getAttribute('data-subscription-price');
            const tokens = this.getAttribute('data-subscription-tokens');
            const idEl   = document.getElementById('modalSubscriptionId');
            if (idEl) idEl.value = id;
            const nameEl = document.getElementById('modalPlanName');
            if (nameEl) nameEl.textContent = name + ' Plan';
            const priceEl = document.getElementById('modalPlanPrice');
            if (priceEl) priceEl.textContent = parseFloat(price).toLocaleString();
            const tokensEl = document.getElementById('modalPlanTokens');
            if (tokensEl) tokensEl.textContent = tokens + ' Tokens Included';
            const summaryEl = document.getElementById('summaryPrice');
            if (summaryEl) summaryEl.textContent = parseFloat(price).toLocaleString() + ' RWF';
            const totalEl = document.getElementById('summaryTotal');
            if (totalEl) totalEl.textContent = parseFloat(price).toLocaleString() + ' RWF';
        });
    });

    const subscriptionForm = document.getElementById('subscriptionForm');
    if (subscriptionForm) {
        subscriptionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const errorDiv  = document.getElementById('subscriptionError');
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Processing...';
            submitBtn.disabled  = true;
            if (errorDiv) { errorDiv.classList.add('d-none'); errorDiv.innerHTML = ''; }

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.querySelector('[name=_token]').value,
                    'Accept':       'application/json',
                    'Content-Type':  'application/json'
                },
                body: JSON.stringify({ subscription_id: document.getElementById('modalSubscriptionId').value })
            })
            .then(r => r.json())
            .then(data => {
                if (data.payment_link) {
                    window.location.href = data.payment_link;
                } else if (errorDiv) {
                    errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' + (data.message || 'Payment failed.');
                    errorDiv.classList.remove('d-none');
                }
            })
            .catch(() => {
                if (errorDiv) {
                    errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Payment failed. Please try again.';
                    errorDiv.classList.remove('d-none');
                }
            })
            .finally(() => {
                submitBtn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Complete Purchase';
                submitBtn.disabled  = false;
            });
        });
    }

    loadLiveClasses();
});

function showNesaModal() {
    const modal = new bootstrap.Modal(document.getElementById('nesaModal'));
    modal.show();
    setTimeout(() => {
        const nesaContent = document.getElementById('nesa-content');
        const nesaLoading = document.getElementById('nesa-loading');
        const nesaFooter  = document.getElementById('nesa-modal-footer');
        const nesaBadge   = document.getElementById('nesa-status-badge');

        const isRegistered = @json($student->nesa_registered);
        const regNumber = @json($student->nesa_reg_number ?? 'N/A');
        const sdmsCode = @json($student->nesa_sdms_code ?? 'N/A');
        const regDate = @json($student->nesa_registration_date ? $student->nesa_registration_date->format('M d, Y') : 'N/A');
        const remarks = @json($student->nesa_remarks ?? 'N/A');

        if (isRegistered) {
            nesaContent.innerHTML = `
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>NESA Registration Complete</strong><br>
                    <small class="d-block mt-1">You are registered for the current academic year.</small>
                </div>
                <div class="mt-3">
                    <h6>Registration Details:</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Registration Number:</strong></td>
                            <td>${regNumber}</td>
                        </tr>
                        <tr>
                            <td><strong>SDMS Code:</strong></td>
                            <td>${sdmsCode}</td>
                        </tr>
                        <tr>
                            <td><strong>Registration Date:</strong></td>
                            <td>${regDate}</td>
                        </tr>
                        <tr>
                            <td><strong>Remarks:</strong></td>
                            <td>${remarks}</td>
                        </tr>
                    </table>
                </div>
            `;
            if (nesaFooter) nesaFooter.innerHTML = `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-success" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print Certificate</button>`;
            if (nesaBadge) { nesaBadge.className = 'badge bg-success'; nesaBadge.textContent = 'Registered'; }
        } else {
            nesaContent.innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>NESA Registration Required</strong><br>
                    <small class="d-block mt-1">You are not currently registered for this academic year. Please complete your NESA registration.</small>
                </div>
                <div class="mt-3">
                    <h6>Registration Requirements:</h6>
                    <ul class="small mb-0">
                        <li>Complete student identification form</li>
                        <li>Submit required documents (ID, passport photo)</li>
                        <li>Pay registration fees</li>
                        <li>Receive NESA registration number</li>
                    </ul>
                </div>
            `;
            if (nesaFooter) nesaFooter.innerHTML = `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-warning"><i class="bi bi-envelope me-1"></i> Contact Support</button>`;
            if (nesaBadge) { nesaBadge.className = 'badge bg-warning text-dark'; nesaBadge.textContent = 'Not Registered'; }
        }
        if (nesaLoading) nesaLoading.classList.add('d-none');
        if (nesaContent) nesaContent.classList.remove('d-none');
    }, 1500);
}

function loadLiveClasses() {
    fetch('/api/live-classes/homepage')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('live-classes-container');
            if (data.liveClasses && data.liveClasses.length > 0) {
                container.innerHTML = data.liveClasses.map(lc => `
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1">${lc.title}</h6>
                            <p class="text-muted mb-0 small"><i class="bi bi-calendar me-1"></i>${new Date(lc.start_time).toLocaleString()}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">Active</span><br>
                            <button class="btn btn-sm btn-primary mt-1" onclick="try { joinLiveClass(${lc.id}); } catch(e) { console.error('Live class join error:', e); alert('Unable to join live class. Please try again.'); }">
                                <i class="bi bi-camera-video me-1"></i>Join
                            </button>
                        </div>
                    </div>`).join('');
            } else {
                container.innerHTML = `<div class="text-center py-3"><i class="bi bi-camera-video-off fs-1 text-muted"></i><p class="text-muted mt-2 small">No live classes available right now</p></div>`;
            }
        })
        .catch(() => {
            document.getElementById('live-classes-container').innerHTML = `<div class="text-center py-3"><p class="text-danger small">Error loading live classes</p></div>`;
        });
}

function joinLiveClass(classId) {
    window.location.href = `/student/live-classes/${classId}/join`;
}

function shareResource(resourceId, resourceName) {
    const url = `${window.location.origin}/student/resources/${resourceId}`;
    if (navigator.share) {
        navigator.share({ title: 'Educational Resource', text: `Check out: ${resourceName}`, url });
    } else {
        navigator.clipboard.writeText(url).then(() => alert('Resource link copied to clipboard!'));
    }
}

// Report Card Countdown Functionality
let countdownInterval;
let targetDate;
let serverTimeOffset = 0;

function updateReportCardStatus() {
    fetch('{{ route("student.report-cards.countdown") }}')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('report-card-status');
            const countdownContainer = document.getElementById('countdown-container');

            if (data.server_time) {
                serverTimeOffset = new Date(data.server_time).getTime() - Date.now();
            }

            if (data.can_access) {
                statusDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Report Cards Available!</strong><br>
                        <small class="text-muted">You can now download and view your report cards.</small>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('student.report-cards.index') }}" class="btn btn-success">
                            <i class="bi bi-download me-2"></i>Download Report Cards
                        </a>
                    </div>
                `;
                countdownContainer.style.display = 'none';
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
            } else {
                statusDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-clock me-2"></i>
                        <strong>Report Cards Not Yet Available</strong><br>
                        <small class="text-muted">${data.reason}</small>
                    </div>
                `;

                if (data.countdown_data && data.countdown_data.total_seconds > 0) {
                    targetDate = new Date(data.countdown_data.target_date);
                    countdownContainer.style.display = 'block';
                    startCountdown(data.countdown_data);

                    const availableDateElement = document.getElementById('available-date');
                    if (availableDateElement && data.available_date) {
                        const date = new Date(data.available_date);
                        availableDateElement.textContent = `Available on: ${date.toLocaleDateString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}`;
                    }
                } else {
                    countdownContainer.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching report card status:', error);
            const statusDiv = document.getElementById('report-card-status');
            statusDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error</strong><br>
                    <small class="text-muted">Unable to check report card status. Please refresh the page.</small>
                </div>
            `;
        });
}

function startCountdown(initialData) {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }

    function updateCountdownDisplay() {
        const now = new Date(Date.now() + serverTimeOffset);
        const difference = targetDate - now;

        if (difference <= 0) {
            updateReportCardStatus();
            return;
        }

        const months = Math.floor(difference / (1000 * 60 * 60 * 24 * 30));
        const weeks = Math.floor((difference % (1000 * 60 * 60 * 24 * 30)) / (1000 * 60 * 60 * 24 * 7));
        const days = Math.floor((difference % (1000 * 60 * 60 * 24 * 7)) / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

        updateCountdownSegment('months', months);
        updateCountdownSegment('weeks', weeks);
        updateCountdownSegment('days', days);
        updateCountdownSegment('hours', hours);
        updateCountdownSegment('minutes', minutes);
        updateCountdownSegment('seconds', seconds);
    }

    function updateCountdownSegment(id, value) {
        const element = document.getElementById(id);
        if (element && element.textContent !== value.toString()) {
            element.textContent = value;
            if (id === 'seconds') {
                element.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    element.style.transform = 'scale(1)';
                }, 100);
            }
        }
    }

    updateCountdownDisplay();
    countdownInterval = setInterval(updateCountdownDisplay, 1000);
}

function showOptionalFeesModal(fees) {
    const modalHtml = `
        <div class="modal fade" id="optionalFeesModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header ${fees.some(f => f.mandatory) ? 'bg-warning' : 'bg-info'} text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-${fees.some(f => f.mandatory) ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                            ${fees.some(f => f.mandatory) ? 'Mandatory Fee Notification' : 'Optional Fees Available'}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${fees.some(f => f.mandatory) ? `
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle me-2"></i>
                                You have mandatory fees that have been added to your account.
                            </div>
                        ` : `
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                You have optional fees available. Please review and accept or decline them.
                            </div>
                        `}
                        <div class="list-group">
                            ${fees.map(fee => `
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">${fee.name}</h6>
                                            <p class="mb-1 text-muted small">Amount: <strong>RWF ${number_format(fee.amount, 2)}</strong></p>
                                            ${fee.due_date ? `<p class="mb-0 text-muted small">Due: ${fee.due_date}</p>` : ''}
                                            ${fee.mandatory ? `
                                                <span class="badge bg-warning text-dark mt-2">Mandatory</span>
                                            ` : ''}
                                        </div>
                                        ${!fee.mandatory ? `
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-success" onclick="respondToFee(${fee.fee_id}, '${fee.type}', 'accepted')">
                                                    <i class="bi bi-check-circle me-1"></i> Accept
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="respondToFee(${fee.fee_id}, '${fee.type}', 'declined')">
                                                    <i class="bi bi-x-circle me-1"></i> Decline
                                                </button>
                                            </div>
                                        ` : `
                                            <span class="text-success"><i class="bi bi-check-circle me-1"></i> Added to balance</span>
                                        `}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('optionalFeesModal'));
    modal.show();
}

function respondToFee(feeId, feeType, response) {
    fetch('{{ route('student.optional-fees.respond') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            course_term_branch_fee_id: feeId,
            fee_type: feeType,
            response: response
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updateReportCardStatus();
    setInterval(updateReportCardStatus, 30000);

    // Load notifications on page load
    loadNotifications();
});

// Notification System
async function loadNotifications() {
    try {
        const response = await fetch('{{ route('student.notifications.index') }}');
        const data = await response.json();

        if (data.announcements && data.announcements.length > 0) {
            showAnnouncementNotifications(data.announcements);
        }

        if (data.mandatory_fees && data.mandatory_fees.length > 0) {
            showMandatoryFeeNotifications(data.mandatory_fees);
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
    }
}

function showAnnouncementNotifications(announcements) {
    announcements.forEach(announcement => {
        const modalHtml = `
            <div class="modal fade announcement-modal" id="announcement-modal-${announcement.id}" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-megaphone me-2"></i> ${announcement.title}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="announcement-content mb-3">
                                ${announcement.content}
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i> ${announcement.created_at}
                                ${announcement.sender ? `<span class="ms-3"><i class="bi bi-person me-1"></i> ${announcement.sender}</span>` : ''}
                            </div>
                            ${announcement.attachment ? `
                                <div class="mt-3">
                                    <a href="${announcement.attachment}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-paperclip me-1"></i> View Attachment
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="markAnnouncementAsRead(${announcement.id})">
                                <i class="bi bi-check-circle me-1"></i> Mark as Read
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById(`announcement-modal-${announcement.id}`));
        modal.show();
    });
}

function showMandatoryFeeNotifications(fees) {
    fees.forEach(fee => {
        const modalHtml = `
            <div class="modal fade fee-modal" id="fee-modal-${fee.id}" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">
                                <i class="bi bi-exclamation-triangle me-2"></i> Mandatory ${fee.fee_type === 'practical' ? 'Practical' : 'Trip'} Fee
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle me-2"></i>
                                You have a mandatory ${fee.fee_type === 'practical' ? 'practical' : 'trip'} fee to pay.
                            </div>
                            <div class="mb-3">
                                <strong>Course:</strong> ${fee.course_name}
                            </div>
                            <div class="mb-3">
                                <strong>Term:</strong> ${fee.term_name}
                            </div>
                            <div class="mb-3">
                                <strong>Amount:</strong> RWF ${fee.amount.toLocaleString()}
                            </div>
                            ${fee.due_date ? `
                                <div class="mb-3">
                                    <strong>Due Date:</strong> ${fee.due_date}
                                </div>
                            ` : ''}
                            <div class="text-muted small">
                                Please contact the finance department to arrange payment.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById(`fee-modal-${fee.id}`));
        modal.show();
    });
}

async function markAnnouncementAsRead(announcementId) {
    try {
        const response = await fetch('{{ route('student.notifications.mark-read') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ announcement_id: announcementId })
        });

        if (response.ok) {
            const modal = bootstrap.Modal.getInstance(document.getElementById(`announcement-modal-${announcementId}`));
            modal.hide();
        }
    } catch (error) {
        console.error('Error marking announcement as read:', error);
    }
}
</script>
@endif
@endsection
