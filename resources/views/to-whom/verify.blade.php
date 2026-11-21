<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document verification | Excellent Servant Academy</title>
    <style>
        body { background: #f2f5f3; color: #17212b; font-family: Arial, sans-serif; margin: 0; padding: 32px 16px; }
        main { background: #fff; border-top: 5px solid #174b43; border-radius: 8px; box-shadow: 0 12px 32px #12382d18; margin: 0 auto; max-width: 560px; padding: 28px; }
        .status { color: {{ $letter ? '#176b48' : '#a12727' }}; font-size: 13px; font-weight: bold; letter-spacing: .08em; text-transform: uppercase; }
        h1 { color: #174b43; font-size: 24px; margin: 10px 0 8px; }
        p { line-height: 1.5; }
        dl { border-top: 1px solid #e1e8e4; margin-top: 24px; }
        dl div { border-bottom: 1px solid #e1e8e4; display: flex; gap: 20px; justify-content: space-between; padding: 12px 0; }
        dt { color: #596761; }
        dd { font-weight: 600; margin: 0; text-align: right; }
        .note { color: #596761; font-size: 13px; margin-top: 22px; }
    </style>
</head>
<body>
<main>
    @if($letter)
        <div class="status">Verified official document</div>
        <h1>{{ $letter->subject_name }}</h1>
        <p>This document was issued by Excellent Servant Academy and is currently active.</p>
        <dl>
            <div><dt>Document</dt><dd>{{ \App\Models\ToWhomLetter::getLetterTypes()[$letter->letter_type] ?? 'Official letter' }}</dd></div>
            @if($letter->recipient_role)
                <div><dt>Position</dt><dd>{{ $letter->recipient_role }}</dd></div>
            @endif
            <div><dt>Reference</dt><dd>{{ $letter->reference_number }}</dd></div>
            <div><dt>Issue date</dt><dd>{{ $letter->formatted_letter_date }}</dd></div>
        </dl>
        <p class="note">This page confirms authenticity only. Contact the academy administration if any document details appear incorrect.</p>
    @else
        <div class="status">Not verified</div>
        <h1>Document not found</h1>
        <p>This verification link is invalid, or the document is no longer active. Do not rely on a copy that cannot be verified.</p>
    @endif
</main>
</body>
</html>
