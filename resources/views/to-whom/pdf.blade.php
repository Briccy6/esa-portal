<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $letter->reference_number }} - Official Document</title>
    <style>
        @page { size: A4 portrait; margin: 12mm 14mm; }
        * { box-sizing: border-box; }
        body {
            color: #17212b;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            line-height: 1.35;
            margin: 0;
        }
        .topline { border-top: 5px solid #174b43; margin-bottom: 12px; }
        .header { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .identity { width: 76%; }
        .logo { max-height: 48px; max-width: 90px; margin-right: 10px; }
        .school-name { color: #174b43; font-size: 17pt; font-weight: bold; margin: 0 0 3px; }
        .school-detail { color: #53615d; font-size: 8pt; margin: 2px 0; }
        .qr-cell { text-align: right; width: 24%; }
        .qr-code { height: 78px; width: 78px; }
        .verify-label { color: #53615d; font-size: 7pt; margin: 2px 0 0; }
        .rule { border-bottom: 1px solid #c8d4d0; margin: 10px 0 14px; }
        .document-meta { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .document-meta td { padding: 2px 0; vertical-align: top; }
        .date { text-align: right; }
        .document-title { color: #174b43; font-size: 15pt; font-weight: bold; margin: 12px 0; text-align: center; }
        .recipient { font-weight: bold; margin: 14px 0; }
        .content { margin: 10px 0; white-space: pre-line; overflow-wrap: break-word; }
        .closing { margin-top: 12px; }
        .signature { margin-top: 22px; width: 100%; }
        .signature td { vertical-align: bottom; width: 50%; }
        .signature-image { max-height: 42px; max-width: 150px; }
        .signature-line { border-bottom: 1px solid #66736f; height: 38px; margin-bottom: 5px; width: 190px; }
        .stamp { max-height: 58px; max-width: 100px; }
        .footer { border-top: 1px solid #c8d4d0; color: #53615d; font-size: 7pt; margin-top: 18px; padding-top: 6px; }
        .footer table { width: 100%; }
        .footer td:last-child { text-align: right; }
    </style>
</head>
<body>
    <div class="topline"></div>
    <table class="header">
        <tr>
            <td class="identity">
                @if($schoolInfo && $schoolInfo->logo_url)
                    <img src="{{ $schoolInfo->logo_url }}" alt="" class="logo">
                @endif
                <h1 class="school-name">{{ $schoolInfo->school_name ?? 'Excellent Servant Academy' }}</h1>
                <p class="school-detail">{{ $schoolInfo->address ?? '' }}</p>
                <p class="school-detail">
                    {{ $schoolInfo->phone ?? '' }}@if($schoolInfo && $schoolInfo->phone && $schoolInfo->email) &nbsp;|&nbsp; @endif{{ $schoolInfo->email ?? '' }}
                </p>
            </td>
            <td class="qr-cell">
                <div class="qr-code">{!! $qrCode !!}</div>
                <p class="verify-label">Scan to verify document</p>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    <table class="document-meta">
        <tr>
            <td><strong>Reference:</strong> {{ $letter->reference_number }}</td>
            <td class="date"><strong>Date:</strong> {{ $letter->formatted_letter_date }}</td>
        </tr>
    </table>
    <h2 class="document-title">{{ \App\Models\ToWhomLetter::getLetterTypes()[$letter->letter_type] ?? 'Official Letter' }}</h2>
    <p class="recipient">{{ $letter->recipient_title }}</p>
    <div class="content">{{ $letter->content }}</div>
    @if($letter->purpose)
        <p><strong>Purpose:</strong> {{ $letter->purpose }}</p>
    @endif
    <p class="closing">Should further information be required, please contact the academy administration.</p>
    <p>Yours faithfully,</p>

    <table class="signature">
        <tr>
            <td>
                @if($schoolInfo && $schoolInfo->principal_signature_url)
                    <img src="{{ $schoolInfo->principal_signature_url }}" alt="Principal signature" class="signature-image">
                @else
                    <div class="signature-line"></div>
                @endif
                <strong>{{ $schoolInfo->principal_name ?? 'Principal' }}</strong><br>
                {{ $schoolInfo->school_name ?? 'Excellent Servant Academy' }}
            </td>
            <td style="text-align: right;">
                @if($schoolInfo && $schoolInfo->stamp_signature_url)
                    <img src="{{ $schoolInfo->stamp_signature_url }}" alt="Official stamp" class="stamp">
                @endif
            </td>
        </tr>
    </table>
    <div class="footer">
        <table>
            <tr>
                <td>{{ $schoolInfo->motto ?? '' }}</td>
                <td>Online verification: {{ $verificationUrl }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
