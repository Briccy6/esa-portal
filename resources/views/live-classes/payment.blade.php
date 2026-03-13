@extends('admin.layout')

@section('title', 'Payment for Live Class')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --bg-base: #f0f2f7;
        --bg-card: #ffffff;
        --bg-input: #f7f8fc;
        --bg-input-focus: #ffffff;
        --border-default: #e2e6f0;
        --border-focus: #63b3ed;
        --border-focus-glow: rgba(99,179,237,0.22);
        --text-primary: #1a1f2e;
        --text-secondary: #5a6478;
        --text-muted: #9aa3b5;
        --text-label: #374151;
        --accent-blue: #3b82f6;
        --accent-blue-dark: #2563eb;
        --accent-blue-light: #eff6ff;
        --accent-green: #10b981;
        --accent-green-dark: #059669;
        --accent-green-light: #ecfdf5;
        --accent-amber: #f59e0b;
        --accent-amber-light: #fffbeb;
        --accent-red: #ef4444;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 2px 8px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 20px rgba(0,0,0,0.07);
        --radius: 12px;
        --radius-sm: 8px;
        --radius-input: 9px;
    }

    body, .content-wrapper { background: var(--bg-base) !important; }

    .pay-wrap {
        font-family: 'Sora', sans-serif;
        padding: 36px 20px 60px;
        max-width: 860px;
        margin: 0 auto;
    }

    /* ── Header ── */
    .pay-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .pay-header-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--accent-blue-light);
        border: 1px solid #bfdbfe;
        color: var(--accent-blue-dark);
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        padding: 5px 14px;
        margin-bottom: 12px;
    }

    .pay-header-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        margin-bottom: 6px;
    }

    .pay-header-sub {
        font-size: 13px;
        color: var(--text-muted);
    }

    /* ── Class info card ── */
    .pay-class-card {
        background: var(--bg-card);
        border: 1px solid var(--border-default);
        border-radius: var(--radius);
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .pay-class-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(59,130,246,0.3);
    }

    .pay-class-body { flex: 1; min-width: 0; }

    .pay-class-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .pay-class-desc {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 14px;
        line-height: 1.5;
    }

    .pay-class-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pay-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--bg-base);
        border: 1px solid var(--border-default);
        border-radius: 100px;
        padding: 4px 12px;
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .pay-meta-chip i { color: var(--accent-blue); font-size: 10px; }

    /* ── Errors ── */
    .pay-alert-error {
        display: flex;
        gap: 12px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: var(--radius-input);
        padding: 14px 16px;
        margin-bottom: 20px;
    }

    .pay-alert-error-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        background: #fee2e2;
        display: flex; align-items: center; justify-content: center;
        color: var(--accent-red); font-size: 13px; flex-shrink: 0;
    }

    .pay-alert-error ul { margin: 0; padding-left: 16px; }
    .pay-alert-error li { font-size: 12px; color: #b91c1c; line-height: 1.6; }
    .pay-alert-error-title { font-size: 13px; font-weight: 600; color: #991b1b; margin-bottom: 4px; }

    /* ── Main layout ── */
    .pay-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 700px) {
        .pay-layout { grid-template-columns: 1fr; }
    }

    /* ── Shared card shell ── */
    .pay-card {
        background: var(--bg-card);
        border: 1px solid var(--border-default);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .pay-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-default);
        background: #fafbfd;
    }

    .pay-card-header-icon {
        width: 30px; height: 30px;
        border-radius: 8px;
        background: var(--accent-blue-light);
        display: flex; align-items: center; justify-content: center;
        color: var(--accent-blue); font-size: 12px; flex-shrink: 0;
    }

    .pay-card-header-title {
        font-size: 12px; font-weight: 700;
        color: var(--text-primary);
        letter-spacing: 0.04em; text-transform: uppercase;
    }

    .pay-card-body { padding: 20px; }

    /* ── Form fields ── */
    .pay-field { margin-bottom: 18px; }
    .pay-field:last-child { margin-bottom: 0; }

    .pay-label {
        display: flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 600;
        color: var(--text-label);
        letter-spacing: 0.03em; text-transform: uppercase;
        margin-bottom: 7px;
    }

    .pay-required { color: var(--accent-red); font-size: 13px; }

    .pay-optional {
        font-size: 10px; font-weight: 500;
        color: var(--text-muted);
        background: var(--bg-base);
        border: 1px solid var(--border-default);
        border-radius: 4px;
        padding: 1px 6px;
        text-transform: uppercase; letter-spacing: 0.05em;
    }

    .pay-input, .pay-select {
        width: 100%;
        background: var(--bg-input);
        border: 1.5px solid var(--border-default);
        border-radius: var(--radius-input);
        color: var(--text-primary);
        font-family: 'Sora', sans-serif;
        font-size: 13px; font-weight: 400;
        padding: 10px 13px;
        outline: none;
        transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
        appearance: none; -webkit-appearance: none;
    }

    .pay-input:focus, .pay-select:focus {
        border-color: var(--border-focus);
        background: var(--bg-input-focus);
        box-shadow: 0 0 0 3px var(--border-focus-glow);
    }

    .pay-input::placeholder { color: var(--text-muted); }

    .pay-select-wrap { position: relative; }
    .pay-select-wrap::after {
        content: '\f078';
        font-family: 'Font Awesome 5 Free'; font-weight: 900;
        font-size: 10px; color: var(--text-muted);
        position: absolute; right: 13px; top: 50%;
        transform: translateY(-50%); pointer-events: none;
    }

    .pay-hint {
        font-size: 11px; color: var(--text-muted);
        margin-top: 5px; line-height: 1.5;
    }

    /* ── Payment method cards ── */
    .pay-methods { display: flex; flex-direction: column; gap: 8px; }

    .pay-method-opt {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 14px;
        background: var(--bg-input);
        border: 1.5px solid var(--border-default);
        border-radius: var(--radius-input);
        cursor: pointer;
        transition: all 0.15s;
        position: relative;
    }

    .pay-method-opt:hover { border-color: var(--border-focus); background: var(--bg-card); }

    .pay-method-opt input[type="radio"] {
        position: absolute;
        opacity: 0; pointer-events: none;
    }

    .pay-method-opt.selected {
        border-color: var(--accent-blue);
        background: var(--accent-blue-light);
        box-shadow: 0 0 0 3px var(--border-focus-glow);
    }

    .pay-method-icon {
        width: 36px; height: 36px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }

    .pay-method-icon.card-icon     { background: #eff6ff; color: #2563eb; }
    .pay-method-icon.mobile-icon   { background: #ecfdf5; color: #059669; }
    .pay-method-icon.bank-icon     { background: #fffbeb; color: #b45309; }

    .pay-method-label { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .pay-method-sub   { font-size: 11px; color: var(--text-muted); margin-top: 1px; }

    .pay-method-check {
        margin-left: auto;
        width: 18px; height: 18px;
        border-radius: 50%;
        border: 2px solid var(--border-default);
        background: #fff;
        transition: all 0.15s;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .pay-method-opt.selected .pay-method-check {
        background: var(--accent-blue);
        border-color: var(--accent-blue);
        color: #fff;
        font-size: 9px;
    }

    /* hidden select for form submit */
    #payment_method { display: none; }

    /* ── Summary card (right column) ── */
    .pay-summary { position: sticky; top: 24px; }

    .pay-summary-amount {
        text-align: center;
        padding: 24px 20px;
        border-bottom: 1px solid var(--border-default);
    }

    .pay-amount-label {
        font-size: 11px; font-weight: 600;
        color: var(--text-muted);
        letter-spacing: 0.07em; text-transform: uppercase;
        margin-bottom: 8px;
    }

    .pay-amount-value {
        font-family: 'JetBrains Mono', monospace;
        font-size: 34px; font-weight: 600;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.1;
    }

    .pay-amount-currency {
        font-size: 16px; font-weight: 500;
        color: var(--text-secondary);
        vertical-align: super;
        margin-right: 2px;
    }

    .pay-summary-lines {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-default);
    }

    .pay-summary-row {
        display: flex; justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: var(--text-secondary);
        margin-bottom: 10px;
    }

    .pay-summary-row:last-child { margin-bottom: 0; }

    .pay-summary-row.total {
        font-size: 14px; font-weight: 700;
        color: var(--text-primary);
        padding-top: 10px;
        border-top: 1px dashed var(--border-default);
        margin-top: 10px;
    }

    .pay-summary-row .val {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 600;
    }

    /* ── Security note ── */
    .pay-secure-note {
        padding: 14px 20px;
        display: flex; gap: 10px; align-items: flex-start;
        background: var(--accent-green-light);
        border-top: 1px solid #a7f3d0;
    }

    .pay-secure-icon {
        width: 28px; height: 28px;
        border-radius: 7px;
        background: #d1fae5;
        display: flex; align-items: center; justify-content: center;
        color: var(--accent-green-dark); font-size: 11px;
        flex-shrink: 0; margin-top: 1px;
    }

    .pay-secure-text { font-size: 11px; color: #065f46; line-height: 1.5; }
    .pay-secure-text strong { font-weight: 700; display: block; margin-bottom: 2px; }

    /* ── Submit btn ── */
    .pay-submit-wrap {
        padding: 16px 20px;
    }

    .pay-submit-btn {
        width: 100%;
        display: flex; align-items: center; justify-content: center;
        gap: 8px;
        padding: 13px 20px;
        background: var(--accent-green);
        border: none;
        border-radius: var(--radius-input);
        color: #fff;
        font-family: 'Sora', sans-serif;
        font-size: 14px; font-weight: 700;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(16,185,129,0.35), 0 4px 16px rgba(16,185,129,0.2);
        transition: all 0.15s;
        letter-spacing: 0.01em;
    }

    .pay-submit-btn:hover {
        background: var(--accent-green-dark);
        box-shadow: 0 4px 16px rgba(16,185,129,0.45), 0 8px 24px rgba(16,185,129,0.25);
        transform: translateY(-1px);
    }

    .pay-submit-btn:active { transform: translateY(0); }

    .pay-back-link {
        display: block; text-align: center;
        margin-top: 12px;
        font-size: 12px; color: var(--text-muted);
        text-decoration: none;
        transition: color 0.15s;
    }

    .pay-back-link:hover { color: var(--accent-blue); text-decoration: none; }

    .pay-back-link i { margin-right: 4px; }
</style>
@endsection

@section('content')
<div class="pay-wrap">

    <!-- ── Page header ── -->
    <div class="pay-header">
        <div class="pay-header-badge">
            <i class="fas fa-lock"></i> Secure Checkout
        </div>
        <div class="pay-header-title">Complete Your Payment</div>
        <div class="pay-header-sub">You're one step away from joining this live class</div>
    </div>

    <!-- ── Class info ── -->
    <div class="pay-class-card">
        <div class="pay-class-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="pay-class-body">
            <div class="pay-class-title">{{ $liveClass->title }}</div>
            @if($liveClass->description)
            <div class="pay-class-desc">{{ $liveClass->description }}</div>
            @endif
            <div class="pay-class-meta">
                @if($liveClass->course)
                <span class="pay-meta-chip"><i class="fas fa-book"></i> {{ $liveClass->course->course_name }}</span>
                @endif
                @if($liveClass->teacher)
                <span class="pay-meta-chip"><i class="fas fa-user-tie"></i> {{ $liveClass->teacher->name }}</span>
                @endif
                <span class="pay-meta-chip"><i class="fas fa-calendar-alt"></i> {{ $liveClass->start_time->format('M j, Y · H:i') }}</span>
                <span class="pay-meta-chip"><i class="fas fa-clock"></i> {{ $liveClass->start_time->diffInHours($liveClass->end_time) }}h session</span>
            </div>
        </div>
    </div>

    <!-- ── Errors ── -->
    @if($errors->any())
    <div class="pay-alert-error">
        <div class="pay-alert-error-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <div class="pay-alert-error-title">Please fix the following errors</div>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- ── Form + Summary layout ── -->
    <form action="{{ route('live-classes.payment.process', $liveClass) }}" method="POST">
        @csrf
        <input type="hidden" id="payment_method" name="payment_method" value="{{ old('payment_method', '') }}">

        <div class="pay-layout">

            <!-- LEFT: contact + payment method -->
            <div style="display:flex;flex-direction:column;gap:20px;">

                <!-- Contact details -->
                <div class="pay-card">
                    <div class="pay-card-header">
                        <div class="pay-card-header-icon"><i class="fas fa-user"></i></div>
                        <span class="pay-card-header-title">Your Details</span>
                    </div>
                    <div class="pay-card-body">
                        <div class="pay-field">
                            <label class="pay-label" for="name">Full Name <span class="pay-required">*</span></label>
                            <input type="text" class="pay-input" id="name" name="name"
                                   value="{{ old('name', session('name')) }}"
                                   placeholder="Enter your full name" required>
                        </div>
                        <div class="pay-field">
                            <label class="pay-label" for="email">Email Address <span class="pay-required">*</span></label>
                            <input type="email" class="pay-input" id="email" name="email"
                                   value="{{ old('email', session('email')) }}"
                                   placeholder="you@example.com" required>
                            <div class="pay-hint"><i class="fas fa-info-circle" style="color:var(--accent-blue);"></i> Used to verify your access to the class</div>
                        </div>
                        <div class="pay-field">
                            <label class="pay-label" for="phone">Phone Number <span class="pay-optional">Optional</span></label>
                            <input type="tel" class="pay-input" id="phone" name="phone"
                                   value="{{ old('phone', session('phone')) }}"
                                   placeholder="+1 000 000 0000">
                        </div>
                    </div>
                </div>

                <!-- Payment method -->
                <div class="pay-card">
                    <div class="pay-card-header">
                        <div class="pay-card-header-icon"><i class="fas fa-credit-card"></i></div>
                        <span class="pay-card-header-title">Payment Method</span>
                    </div>
                    <div class="pay-card-body">
                        <div class="pay-methods">
                            <label class="pay-method-opt {{ old('payment_method') == 'card' ? 'selected' : '' }}" data-value="card">
                                <input type="radio" name="_pm" value="card" {{ old('payment_method') == 'card' ? 'checked' : '' }}>
                                <div class="pay-method-icon card-icon"><i class="fas fa-credit-card"></i></div>
                                <div>
                                    <div class="pay-method-label">Credit / Debit Card</div>
                                    <div class="pay-method-sub">Visa, Mastercard, Amex</div>
                                </div>
                                <div class="pay-method-check">
                                    @if(old('payment_method') == 'card') <i class="fas fa-check"></i> @endif
                                </div>
                            </label>

                            <label class="pay-method-opt {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}" data-value="mobile_money">
                                <input type="radio" name="_pm" value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'checked' : '' }}>
                                <div class="pay-method-icon mobile-icon"><i class="fas fa-mobile-alt"></i></div>
                                <div>
                                    <div class="pay-method-label">Mobile Money</div>
                                    <div class="pay-method-sub">MTN, Airtel, M-Pesa</div>
                                </div>
                                <div class="pay-method-check">
                                    @if(old('payment_method') == 'mobile_money') <i class="fas fa-check"></i> @endif
                                </div>
                            </label>

                            <label class="pay-method-opt {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}" data-value="bank_transfer">
                                <input type="radio" name="_pm" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                <div class="pay-method-icon bank-icon"><i class="fas fa-university"></i></div>
                                <div>
                                    <div class="pay-method-label">Bank Transfer</div>
                                    <div class="pay-method-sub">Direct bank payment</div>
                                </div>
                                <div class="pay-method-check">
                                    @if(old('payment_method') == 'bank_transfer') <i class="fas fa-check"></i> @endif
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: order summary + submit -->
            <div class="pay-summary">
                <div class="pay-card">
                    <div class="pay-card-header">
                        <div class="pay-card-header-icon"><i class="fas fa-receipt"></i></div>
                        <span class="pay-card-header-title">Order Summary</span>
                    </div>

                    <div class="pay-summary-amount">
                        <div class="pay-amount-label">Amount Due</div>
                        <div class="pay-amount-value">
                            <span class="pay-amount-currency">{{ $liveClass->currency }}</span>{{ number_format($liveClass->public_fee, 2) }}
                        </div>
                    </div>

                    <div class="pay-summary-lines">
                        <div class="pay-summary-row">
                            <span>Live Class Access</span>
                            <span class="val">{{ number_format($liveClass->public_fee, 2) }}</span>
                        </div>
                        <div class="pay-summary-row">
                            <span>Processing Fee</span>
                            <span class="val" style="color:var(--accent-green);font-size:12px;">Free</span>
                        </div>
                        <div class="pay-summary-row total">
                            <span>Total</span>
                            <span class="val">{{ $liveClass->currency }} {{ number_format($liveClass->public_fee, 2) }}</span>
                        </div>
                    </div>

                    <div class="pay-secure-note">
                        <div class="pay-secure-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="pay-secure-text">
                            <strong>Encrypted &amp; Secure</strong>
                            Your payment is protected. Access is granted immediately after confirmation.
                        </div>
                    </div>

                    <div class="pay-submit-wrap">
                        <button type="submit" class="pay-submit-btn">
                            <i class="fas fa-lock"></i>
                            Pay {{ $liveClass->currency }} {{ number_format($liveClass->public_fee, 2) }}
                        </button>
                        <a href="{{ route('live-classes.show', $liveClass) }}" class="pay-back-link">
                            <i class="fas fa-arrow-left"></i> Return to class page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const opts = document.querySelectorAll('.pay-method-opt');
    const hidden = document.getElementById('payment_method');

    opts.forEach(opt => {
        opt.addEventListener('click', function () {
            // deselect all
            opts.forEach(o => {
                o.classList.remove('selected');
                o.querySelector('.pay-method-check').innerHTML = '';
            });
            // select this
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
            this.querySelector('.pay-method-check').innerHTML = '<i class="fas fa-check"></i>';
            hidden.value = this.dataset.value;
        });
    });
});
</script>
@endsection
@endsection