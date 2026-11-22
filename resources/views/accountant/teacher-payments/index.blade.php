@extends('staff.partials.sidebar_sec')

@section('title', 'Teacher Payments')

@section('content')
@php
    $pendingTotal = $payments->sum('remaining_amount');
    $teacherCount = $payments->pluck('teacher_id')->unique()->count();
@endphp

<style>
    .teacher-payments-page {
        --payments-ink: #17243b;
        --payments-muted: #748094;
        --payments-accent: #d26c4f;
        max-width: 1480px;
        margin: 0 auto;
        color: var(--payments-ink);
    }
    .teacher-payments-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5rem;
        padding: clamp(1.35rem, 3.5vw, 2.2rem);
        margin-bottom: 1.25rem;
        border-radius: 20px;
        background:
            radial-gradient(circle at 85% 12%, rgba(210,108,79,.2), transparent 25%),
            linear-gradient(125deg, #14233a, #1b3851 72%, #22545b 120%);
        color: #fff;
        box-shadow: 0 16px 34px rgba(23,36,59,.14);
    }
    .teacher-payments-hero__eyebrow { display: block; margin-bottom: .55rem; color: #efb69f; font-size: .66rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .teacher-payments-hero h1 { margin: 0; color: #fff; font-size: clamp(1.5rem, 3vw, 2.1rem); font-weight: 800; letter-spacing: -.035em; }
    .teacher-payments-hero p { margin: .5rem 0 0; color: rgba(242,246,250,.75); font-size: .84rem; }
    .teacher-payments-hero__stats { display: flex; gap: .6rem; }
    .teacher-payments-stat { min-width: 126px; padding: .75rem .9rem; border: 1px solid rgba(255,255,255,.15); border-radius: 11px; background: rgba(255,255,255,.075); }
    .teacher-payments-stat strong { display: block; color: #fff; font-size: 1.28rem; font-weight: 800; font-variant-numeric: tabular-nums; }
    .teacher-payments-stat span { display: block; margin-top: .2rem; color: rgba(242,246,250,.7); font-size: .66rem; font-weight: 700; }
    .teacher-payments-panel { overflow: hidden; border: 1px solid #e6e9ee; border-radius: 17px; background: #fff; box-shadow: 0 7px 24px rgba(23,36,59,.05); }
    .teacher-payments-panel__header { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: 1rem 1.15rem; border-bottom: 1px solid #edf0f3; }
    .teacher-payments-panel__heading { margin: 0; color: var(--payments-ink); font-size: .9rem; font-weight: 800; }
    .teacher-payments-panel__heading i { margin-right: .4rem; color: var(--payments-accent); }
    .teacher-payments-panel__hint { color: var(--payments-muted); font-size: .72rem; }
    .teacher-payments-list { padding: .45rem .75rem .75rem; }
    .teacher-payments-labels, .teacher-payment-row {
        display: grid;
        grid-template-columns: minmax(175px, 1.8fr) minmax(105px, 1fr) minmax(75px, .7fr) repeat(3, minmax(110px, 1.1fr)) minmax(90px, .85fr) minmax(105px, .9fr);
        align-items: center;
        gap: .7rem;
    }
    .teacher-payments-labels { padding: .65rem .75rem; color: #818b9b; font-size: .62rem; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
    .teacher-payment-row {
        min-height: 76px;
        padding: .75rem;
        border: 1px solid #e9ebef;
        border-radius: 12px;
        background: #fff;
        transition: border-color .16s ease, box-shadow .16s ease;
    }
    .teacher-payment-row + .teacher-payment-row { margin-top: .55rem; }
    .teacher-payment-row:hover { border-color: #d3dade; box-shadow: 0 5px 15px rgba(23,36,59,.05); }
    .teacher-payment-row__teacher { min-width: 0; }
    .teacher-payment-row__name { display: block; overflow: hidden; color: #22314a; font-size: .78rem; font-weight: 800; text-overflow: ellipsis; white-space: nowrap; }
    .teacher-payment-row__phone { display: inline-block; margin-top: .2rem; color: #738096; font-size: .68rem; text-decoration: none; }
    .teacher-payment-row__phone:hover { color: #205f62; text-decoration: underline; }
    .teacher-payment-row__value { color: #48566c; font-size: .74rem; font-weight: 650; }
    .teacher-payment-row__amount { color: #263650; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .teacher-payment-row__amount--due { color: #205f62; font-weight: 850; }
    .teacher-payment-period { display: block; margin-top: .18rem; color: #8993a3; font-size: .64rem; }
    .teacher-payment-status { display: inline-flex; align-items: center; gap: .35rem; padding: .32rem .55rem; border-radius: 999px; font-size: .65rem; font-weight: 800; white-space: nowrap; }
    .teacher-payment-status--approved { background: #fff4dc; color: #97610d; }
    .teacher-payment-status--partial { background: #eaf2ff; color: #315d9c; }
    .teacher-payment-status--paid { background: #e7f5ed; color: #26734b; }
    .teacher-payment-open {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        padding: .48rem .6rem;
        border: 1px solid #d5e4e1;
        border-radius: 8px;
        background: #f3f8f7;
        color: #205f62;
        font-size: .68rem;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }
    .teacher-payment-open:hover { border-color: #205f62; background: #205f62; color: #fff; }
    .teacher-payments-empty { padding: 3.5rem 1.25rem; text-align: center; }
    .teacher-payments-empty__icon { display: grid; width: 54px; height: 54px; place-items: center; margin: 0 auto .8rem; border: 1px solid #dfece7; border-radius: 16px; background: #f1f8f4; color: #37815e; font-size: 1.2rem; }
    .teacher-payments-empty h2 { margin: 0; color: var(--payments-ink); font-size: 1.02rem; font-weight: 800; }
    .teacher-payments-empty p { max-width: 400px; margin: .4rem auto 0; color: var(--payments-muted); font-size: .8rem; }
    @media (max-width: 1100px) {
        .teacher-payments-labels { display: none; }
        .teacher-payment-row { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; padding: .85rem; }
        .teacher-payment-row > * { min-width: 0; }
        .teacher-payment-row__teacher { grid-column: 1 / -1; padding-bottom: .65rem; border-bottom: 1px solid #edf0f3; }
        .teacher-payment-row__value::before { display: block; margin-bottom: .18rem; color: #8993a3; content: attr(data-label); font-size: .6rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
        .teacher-payment-row__action { justify-self: end; }
    }
    @media (max-width: 767.98px) {
        .teacher-payments-hero { align-items: flex-start; flex-direction: column; }
        .teacher-payments-hero__stats { width: 100%; }
        .teacher-payments-stat { flex: 1; min-width: 0; }
    }
    @media (max-width: 520px) {
        .teacher-payments-page { padding-right: .25rem; padding-left: .25rem; }
        .teacher-payments-hero { padding: 1.2rem; border-radius: 16px; }
        .teacher-payments-stat { padding: .65rem; }
        .teacher-payments-stat strong { font-size: 1.1rem; }
        .teacher-payments-panel { border-radius: 14px; }
        .teacher-payments-panel__header { align-items: flex-start; flex-direction: column; }
        .teacher-payments-list { padding: .5rem; }
        .teacher-payment-row { gap: .65rem .45rem; padding: .75rem; }
    }
</style>

<main class="container-fluid py-4 teacher-payments-page">
    <header class="teacher-payments-hero">
        <div>
            <span class="teacher-payments-hero__eyebrow">Finance workspace · Approved work</span>
            <h1>Teacher payments</h1>
            <p>Review approved work records, confirm the remaining amount, and record payments.</p>
        </div>
        <div class="teacher-payments-hero__stats" aria-label="Pending payment summary">
            <div class="teacher-payments-stat">
                <strong>{{ $payments->count() }}</strong>
                <span>Approved records</span>
            </div>
            <div class="teacher-payments-stat">
                <strong>{{ number_format($teacherCount) }}</strong>
                <span>Teachers</span>
            </div>
            <div class="teacher-payments-stat">
                <strong>{{ number_format($pendingTotal, 0) }}</strong>
                <span>RWF remaining</span>
            </div>
        </div>
    </header>

    <section class="teacher-payments-panel" aria-labelledby="pending-payments-heading">
        <header class="teacher-payments-panel__header">
            <h2 class="teacher-payments-panel__heading" id="pending-payments-heading">
                <i class="fa-solid fa-list-check" aria-hidden="true"></i>Payments awaiting processing
            </h2>
            @if(!$payments->isEmpty())
                <span class="teacher-payments-panel__hint">Select a record to review or record a payment.</span>
            @endif
        </header>

        @if($payments->isEmpty())
            <div class="teacher-payments-empty">
                <span class="teacher-payments-empty__icon"><i class="fa-regular fa-circle-check" aria-hidden="true"></i></span>
                <h2>All caught up</h2>
                <p>There are no approved teacher payments waiting to be processed.</p>
            </div>
        @else
            <div class="teacher-payments-list">
                <div class="teacher-payments-labels" aria-hidden="true">
                    <span>Teacher</span>
                    <span>Work date</span>
                    <span>Hours</span>
                    <span>Calculated</span>
                    <span>Paid</span>
                    <span>Remaining</span>
                    <span>Status</span>
                    <span></span>
                </div>
                @foreach($payments as $payment)
                    @php
                        $statusClass = match ($payment->status) {
                            'paid' => 'paid',
                            'partial' => 'partial',
                            default => 'approved',
                        };
                    @endphp
                    <article class="teacher-payment-row">
                        <div class="teacher-payment-row__teacher">
                            <strong class="teacher-payment-row__name">{{ $payment->teacher->teacher_name ?? 'Teacher not listed' }}</strong>
                            @if($payment->teacher?->telephone)
                                <a class="teacher-payment-row__phone" href="tel:{{ $payment->teacher->telephone }}">{{ $payment->teacher->telephone }}</a>
                            @else
                                <span class="teacher-payment-row__phone">Telephone not listed</span>
                            @endif
                            <span class="teacher-payment-period">{{ ucfirst($payment->teacher->payment_period ?? 'monthly') }} pay period</span>
                        </div>
                        <span class="teacher-payment-row__value" data-label="Work date">{{ $payment->work_date?->format('d M Y') ?? 'N/A' }}</span>
                        <span class="teacher-payment-row__value" data-label="Hours">{{ number_format((float) ($payment->teacherDaily->hours_studied ?? 0), 1) }} hours</span>
                        <span class="teacher-payment-row__value teacher-payment-row__amount" data-label="Calculated">RWF {{ number_format($payment->calculated_amount, 0) }}</span>
                        <span class="teacher-payment-row__value teacher-payment-row__amount" data-label="Paid">RWF {{ number_format($payment->amount_paid, 0) }}</span>
                        <strong class="teacher-payment-row__value teacher-payment-row__amount teacher-payment-row__amount--due" data-label="Remaining">RWF {{ number_format($payment->remaining_amount, 0) }}</strong>
                        <span class="teacher-payment-row__value" data-label="Status">
                            <span class="teacher-payment-status teacher-payment-status--{{ $statusClass }}">
                                <i class="fa-solid {{ $payment->status === 'paid' ? 'fa-circle-check' : 'fa-clock' }}" aria-hidden="true"></i>{{ ucfirst($payment->status) }}
                            </span>
                        </span>
                        <span class="teacher-payment-row__action">
                            <a href="{{ route('staff.accountant.teacher-payments.show', $payment->id) }}" class="teacher-payment-open">
                                Review <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </span>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection
