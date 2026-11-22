@extends('staff.partials.sidebar_sec')

@section('title', 'Payment Details')

@section('content')
@php
    $statusClass = match ($payment->status) {
        'paid' => 'paid',
        'partial' => 'partial',
        default => 'approved',
    };
@endphp

<style>
    .teacher-payment-detail {
        --detail-ink: #17243b;
        --detail-muted: #748094;
        --detail-accent: #d26c4f;
        max-width: 1280px;
        margin: 0 auto;
        color: var(--detail-ink);
    }
    .teacher-payment-detail__back {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: .85rem;
        color: #647287;
        font-size: .75rem;
        font-weight: 750;
        text-decoration: none;
    }
    .teacher-payment-detail__back:hover { color: #205f62; }
    .teacher-payment-detail__hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: clamp(1.25rem, 3vw, 2rem);
        margin-bottom: 1.1rem;
        border-radius: 18px;
        background:
            radial-gradient(circle at 85% 12%, rgba(210,108,79,.2), transparent 25%),
            linear-gradient(125deg, #14233a, #1b3851 72%, #22545b 120%);
        color: #fff;
        box-shadow: 0 15px 32px rgba(23,36,59,.14);
    }
    .teacher-payment-detail__eyebrow { display: block; margin-bottom: .45rem; color: #efb69f; font-size: .65rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .teacher-payment-detail__hero h1 { margin: 0; color: #fff; font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 800; letter-spacing: -.035em; }
    .teacher-payment-detail__hero p { margin: .4rem 0 0; color: rgba(242,246,250,.73); font-size: .8rem; }
    .teacher-payment-status {
        display: inline-flex;
        align-items: center;
        gap: .42rem;
        padding: .43rem .7rem;
        border-radius: 999px;
        font-size: .69rem;
        font-weight: 800;
        white-space: nowrap;
    }
    .teacher-payment-status--approved { background: #fff2d6; color: #8d5c0b; }
    .teacher-payment-status--partial { background: #e7f0ff; color: #315d9c; }
    .teacher-payment-status--paid { background: #e2f4e9; color: #26734b; }
    .teacher-payment-profile {
        display: flex;
        align-items: center;
        gap: .95rem;
        padding: 1.1rem 1.2rem;
        margin-bottom: 1rem;
        border: 1px solid #e6e9ee;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 6px 19px rgba(23,36,59,.04);
    }
    .teacher-payment-profile__avatar {
        display: grid;
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        place-items: center;
        border: 1px solid #dfece8;
        border-radius: 15px;
        background: #f0f7f5;
        color: #276663;
        font-size: 1.2rem;
    }
    .teacher-payment-profile__identity { min-width: 0; flex: 1; }
    .teacher-payment-profile__name { margin: 0; color: var(--detail-ink); font-size: 1rem; font-weight: 850; overflow-wrap: anywhere; }
    .teacher-payment-profile__contact { display: flex; flex-wrap: wrap; gap: .4rem 1rem; margin-top: .35rem; color: var(--detail-muted); font-size: .74rem; }
    .teacher-payment-profile__contact a { color: #276663; font-weight: 700; text-decoration: none; }
    .teacher-payment-profile__contact a:hover { text-decoration: underline; }
    .teacher-payment-section {
        padding: 1.15rem;
        margin-bottom: 1rem;
        border: 1px solid #e6e9ee;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 6px 19px rgba(23,36,59,.04);
    }
    .teacher-payment-section__heading { display: flex; align-items: center; gap: .5rem; margin: 0 0 .9rem; color: var(--detail-ink); font-size: .88rem; font-weight: 850; }
    .teacher-payment-section__heading i { color: var(--detail-accent); }
    .teacher-payment-facts {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .65rem;
    }
    .teacher-payment-fact { min-width: 0; padding: .8rem; border: 1px solid #eceef2; border-radius: 10px; background: #f9fafb; }
    .teacher-payment-fact__label { display: block; color: #7e899a; font-size: .62rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .teacher-payment-fact__value { display: block; margin-top: .3rem; color: #304058; font-size: .8rem; font-weight: 750; overflow-wrap: anywhere; }
    .teacher-payment-balances { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .7rem; }
    .teacher-payment-balance { padding: .95rem 1rem; border: 1px solid #e8ebef; border-radius: 11px; background: #fafbfc; }
    .teacher-payment-balance--due { border-color: #d8e8e4; background: #f1f8f6; }
    .teacher-payment-balance__label { display: block; color: #748094; font-size: .67rem; font-weight: 750; }
    .teacher-payment-balance__value { display: block; margin-top: .3rem; color: #263650; font-size: clamp(1rem, 2vw, 1.35rem); font-weight: 850; font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }
    .teacher-payment-balance--due .teacher-payment-balance__value { color: #205f62; }
    .teacher-payment-form { padding-top: 1rem; margin-top: 1rem; border-top: 1px solid #edf0f3; }
    .teacher-payment-form .form-label { color: #4a5870; font-size: .72rem; font-weight: 750; }
    .teacher-payment-form .form-control { min-height: 43px; border-color: #dfe4eb; border-radius: 8px; font-size: .8rem; }
    .teacher-payment-form .form-control:focus { border-color: #9db9ba; box-shadow: 0 0 0 .2rem rgba(45,111,110,.12); }
    .teacher-payment-form__submit {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .65rem .95rem;
        border: 1px solid #205f62;
        border-radius: 9px;
        background: #205f62;
        color: #fff;
        font-size: .76rem;
        font-weight: 800;
    }
    .teacher-payment-form__submit:hover { border-color: #174b4d; background: #174b4d; color: #fff; }
    .teacher-payment-paid {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        padding: .9rem 1rem;
        margin-top: 1rem;
        border: 1px solid #d5e9dd;
        border-radius: 10px;
        background: #f0f8f3;
        color: #286547;
        font-size: .78rem;
    }
    @media (max-width: 767.98px) {
        .teacher-payment-facts { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .teacher-payment-profile { align-items: flex-start; flex-wrap: wrap; }
        .teacher-payment-profile > .teacher-payment-status { margin-left: 3.8rem; }
    }
    @media (max-width: 520px) {
        .teacher-payment-detail { padding-right: .25rem; padding-left: .25rem; }
        .teacher-payment-detail__hero { align-items: flex-start; flex-direction: column; border-radius: 15px; }
        .teacher-payment-profile { gap: .7rem; padding: .9rem; }
        .teacher-payment-profile__avatar { width: 44px; height: 44px; flex-basis: 44px; }
        .teacher-payment-profile > .teacher-payment-status { margin-left: 0; }
        .teacher-payment-section { padding: .9rem; }
        .teacher-payment-balances { grid-template-columns: 1fr; }
        .teacher-payment-form__submit { width: 100%; }
    }
</style>

<main class="container-fluid py-4 teacher-payment-detail">
    <a href="{{ route('staff.accountant.teacher-payments.index') }}" class="teacher-payment-detail__back">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Back to teacher payments
    </a>

    <header class="teacher-payment-detail__hero">
        <div>
            <span class="teacher-payment-detail__eyebrow">Payment review · {{ $payment->work_date?->format('d M Y') ?? 'Work date not listed' }}</span>
            <h1>Teacher payment details</h1>
            <p>Review the approved work record and remaining balance before recording payment.</p>
        </div>
        <span class="teacher-payment-status teacher-payment-status--{{ $statusClass }}">
            <i class="fa-solid {{ $payment->status === 'paid' ? 'fa-circle-check' : 'fa-clock' }}" aria-hidden="true"></i>{{ ucfirst($payment->status) }}
        </span>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="status">
            <i class="fa-solid fa-circle-check me-2" aria-hidden="true"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Payment was not recorded.</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="teacher-payment-profile" aria-label="Teacher contact">
        <span class="teacher-payment-profile__avatar" aria-hidden="true"><i class="fa-solid fa-user-tie"></i></span>
        <div class="teacher-payment-profile__identity">
            <h2 class="teacher-payment-profile__name">{{ $payment->teacher->teacher_name ?? 'Teacher not listed' }}</h2>
            <div class="teacher-payment-profile__contact">
                @if($payment->teacher?->telephone)
                    <a href="tel:{{ $payment->teacher->telephone }}"><i class="fa-solid fa-phone me-1" aria-hidden="true"></i>{{ $payment->teacher->telephone }}</a>
                @else
                    <span><i class="fa-solid fa-phone me-1" aria-hidden="true"></i>Telephone not listed</span>
                @endif
                <span><i class="fa-regular fa-calendar me-1" aria-hidden="true"></i>{{ ucfirst($payment->teacher->payment_period ?? 'monthly') }} pay period</span>
            </div>
        </div>
        <span class="teacher-payment-status teacher-payment-status--{{ $statusClass }}">
            {{ ucfirst($payment->status) }}
        </span>
    </section>

    <section class="teacher-payment-section" aria-labelledby="work-record-heading">
        <h2 class="teacher-payment-section__heading" id="work-record-heading">
            <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>Approved work record
        </h2>
        <div class="teacher-payment-facts">
            <div class="teacher-payment-fact">
                <span class="teacher-payment-fact__label">Work date</span>
                <span class="teacher-payment-fact__value">{{ $payment->work_date?->format('d M Y') ?? 'N/A' }}</span>
            </div>
            <div class="teacher-payment-fact">
                <span class="teacher-payment-fact__label">Hours worked</span>
                <span class="teacher-payment-fact__value">{{ number_format((float) ($payment->teacherDaily->hours_studied ?? 0), 1) }} hours</span>
            </div>
            <div class="teacher-payment-fact">
                <span class="teacher-payment-fact__label">Course</span>
                <span class="teacher-payment-fact__value">{{ $payment->teacherDaily->course->course_name ?? 'Not specified' }}</span>
            </div>
            <div class="teacher-payment-fact">
                <span class="teacher-payment-fact__label">Lesson</span>
                <span class="teacher-payment-fact__value">{{ $payment->teacherDaily->lesson->title ?? 'Not specified' }}</span>
            </div>
        </div>
    </section>

    <section class="teacher-payment-section" aria-labelledby="payment-amounts-heading">
        <h2 class="teacher-payment-section__heading" id="payment-amounts-heading">
            <i class="fa-solid fa-coins" aria-hidden="true"></i>Payment summary
        </h2>
        <div class="teacher-payment-balances">
            <div class="teacher-payment-balance">
                <span class="teacher-payment-balance__label">Calculated amount</span>
                <strong class="teacher-payment-balance__value">{{ number_format($payment->calculated_amount, 0) }} RWF</strong>
            </div>
            <div class="teacher-payment-balance">
                <span class="teacher-payment-balance__label">Amount paid</span>
                <strong class="teacher-payment-balance__value">{{ number_format($payment->amount_paid, 0) }} RWF</strong>
            </div>
            <div class="teacher-payment-balance teacher-payment-balance--due">
                <span class="teacher-payment-balance__label">Remaining balance</span>
                <strong class="teacher-payment-balance__value">{{ number_format($payment->remaining_amount, 0) }} RWF</strong>
            </div>
        </div>

        @if($payment->status !== 'paid')
            <form method="POST" action="{{ route('staff.accountant.teacher-payments.record', $payment->id) }}" class="teacher-payment-form">
                @csrf
                <h3 class="teacher-payment-section__heading">Record a payment</h3>
                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="paymentAmount">Payment amount (RWF)</label>
                        <input
                            type="number"
                            id="paymentAmount"
                            name="amount"
                            class="form-control"
                            value="{{ old('amount', $payment->remaining_amount) }}"
                            min="0"
                            max="{{ $payment->remaining_amount }}"
                            step="0.01"
                            inputmode="decimal"
                            required
                            aria-describedby="paymentAmountHelp"
                        >
                        <small class="form-text text-muted" id="paymentAmountHelp">Up to {{ number_format($payment->remaining_amount, 0) }} RWF remaining.</small>
                        @error('amount')
                            <span class="d-block text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-12 col-md-7">
                        <label class="form-label" for="paymentNotes">Notes <span class="fw-normal text-muted">(optional)</span></label>
                        <input
                            type="text"
                            id="paymentNotes"
                            name="notes"
                            class="form-control"
                            value="{{ old('notes') }}"
                            maxlength="1000"
                            placeholder="Add a payment reference or note"
                        >
                        @error('notes')
                            <span class="d-block text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="teacher-payment-form__submit">
                            <i class="fa-solid fa-money-bill-transfer" aria-hidden="true"></i>Record payment
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="teacher-payment-paid" role="status">
                <i class="fa-solid fa-circle-check mt-1" aria-hidden="true"></i>
                <span>This payment was fully paid{{ $payment->paid_at ? ' on ' . $payment->paid_at->format('d M Y, H:i') : '' }}.</span>
            </div>
        @endif
    </section>
</main>
@endsection
