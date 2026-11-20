<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Student Card Verification | {{ config('app.name', 'Excellent Servant Academy') }}</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, Helvetica, sans-serif;
            color: #172b3a;
            background: #f2f6f7;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; }
        .verification-card {
            width: min(100%, 620px);
            overflow: hidden;
            background: #fff;
            border: 1px solid #dce6e8;
            border-radius: 18px;
            box-shadow: 0 18px 48px rgba(20, 45, 54, .1);
        }
        .verification-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 24px 28px;
            border-bottom: 1px solid #e8eef0;
        }
        .verification-header img { width: 58px; height: 58px; object-fit: contain; }
        .school-name { margin: 0; font-size: 1.15rem; font-weight: 700; }
        .school-caption { margin: 4px 0 0; color: #64748b; font-size: .9rem; }
        .verification-body { padding: 28px; }
        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: .85rem;
        }
        .status-valid { color: #126657; background: #e8f5f1; }
        .status-invalid { color: #a33333; background: #fff0ef; }
        h1 { margin: 18px 0 6px; font-size: clamp(1.5rem, 5vw, 2rem); }
        .intro { margin: 0; color: #64748b; }
        .details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; margin: 26px 0 0; }
        .detail { padding: 14px; border: 1px solid #e5ecee; border-radius: 12px; }
        .detail dt { margin-bottom: 6px; color: #64748b; font-size: .75rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .detail dd { margin: 0; font-weight: 600; overflow-wrap: anywhere; }
        .notice { margin: 24px 0 0; padding: 14px 16px; border-left: 3px solid #0f766e; border-radius: 8px; background: #f2f8f7; color: #48606a; font-size: .9rem; line-height: 1.5; }
        @media (max-width: 520px) {
            body { padding: 14px; }
            .verification-header, .verification-body { padding: 20px; }
            .details { grid-template-columns: 1fr; gap: 12px; }
        }
    </style>
</head>
<body>
    <main class="verification-card">
        <header class="verification-header">
            <img src="{{ \App\Helpers\LogoHelper::getLogoUrl() }}" alt="School logo">
            <div>
                <p class="school-name">{{ config('app.name', 'Excellent Servant Academy') }}</p>
                <p class="school-caption">Student identity card verification</p>
            </div>
        </header>
        <section class="verification-body" aria-live="polite">
            @if($verified)
                <span class="status status-valid"><span aria-hidden="true">✓</span> Verified card</span>
                <h1>{{ $student->full_name }}</h1>
                <p class="intro">This card record matches the school's student registry.</p>
                <dl class="details">
                    <div class="detail"><dt>Registration Number</dt><dd>{{ $card->reg_number }}</dd></div>
                    <div class="detail"><dt>Card Status</dt><dd>Valid</dd></div>
                    <div class="detail"><dt>Course</dt><dd>{{ $card->course ?: 'Not recorded' }}</dd></div>
                    <div class="detail"><dt>Branch</dt><dd>{{ $card->branch ?: 'Not recorded' }}</dd></div>
                    <div class="detail"><dt>Academic Year</dt><dd>{{ $card->academic_year ?: 'Not recorded' }}</dd></div>
                    <div class="detail"><dt>Issued</dt><dd>{{ $card->generated_at ? \Carbon\Carbon::parse($card->generated_at)->format('F j, Y') : 'Not recorded' }}</dd></div>
                </dl>
                <p class="notice">This page confirms the card record. It intentionally displays only the information needed for card verification.</p>
            @else
                <span class="status status-invalid">Card not verified</span>
                <h1>Verification link is invalid</h1>
                <p class="intro">This link could not be verified. Please contact the school administration to confirm the card.</p>
            @endif
        </section>
    </main>
</body>
</html>
