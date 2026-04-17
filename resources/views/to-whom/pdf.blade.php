<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $letter->reference_number }} - Professional Letter</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            max-height: 80px;
            margin-bottom: 20px;
        }
        
        .school-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        
        .address {
            font-size: 10pt;
            color: #6b7280;
            margin: 5px 0;
        }
        
        .letter-header {
            margin-bottom: 30px;
        }
        
        .reference-info {
            text-align: right;
            margin-bottom: 20px;
        }
        
        .recipient {
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .content {
            margin-bottom: 30px;
            white-space: pre-line;
        }
        
        .signature-section {
            margin-top: 60px;
        }
        
        .signature-line {
            height: 60px;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
        }
        
        .signature-image {
            max-height: 60px;
            margin-bottom: 10px;
        }
        
        .principal-name {
            font-weight: bold;
            margin: 0;
        }
        
        .school-title {
            margin: 0;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 9pt;
            color: #6b7280;
        }
        
        .motto {
            font-weight: bold;
        }
        
        .generated-info {
            text-align: right;
        }
        
        .stamp {
            max-height: 80px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- School Header -->
    <div class="header">
        @if($schoolInfo && $schoolInfo->logo_url)
            <img src="{{ $schoolInfo->logo_url }}" alt="{{ $schoolInfo->school_name }}" class="logo">
        @endif
        
        <h2 class="school-name">{{ $schoolInfo->school_name ?? 'ESA Learning Portal' }}</h2>
        <p class="address">{{ $schoolInfo->address ?? '123 Education Street, Learning City, Knowledge State' }}</p>
        <p class="address">
            Phone: {{ $schoolInfo->phone ?? '+250-788-123-456' }} | 
            Email: {{ $schoolInfo->email ?? 'info@esa-learning.rw' }}
        </p>
        @if($schoolInfo->website)
            <p class="address">Website: {{ $schoolInfo->website }}</p>
        @endif
    </div>

    <!-- Letter Header -->
    <div class="letter-header">
        <div class="reference-info">
            <p style="margin: 0; font-weight: bold;">Reference: {{ $letter->reference_number }}</p>
            <p style="margin: 0;">Date: {{ $letter->formatted_letter_date }}</p>
        </div>
        
        <p class="recipient">{{ $letter->recipient_title }}</p>
    </div>

    <!-- Letter Content -->
    <div class="content">
        <p style="margin: 0 0 20px 0;">Dear Sir/Madam,</p>
        
        <div>{{ $letter->content }}</div>

        <p style="margin: 30px 0 20px 0;">This letter is issued for the purpose of: <strong>{{ $letter->purpose }}</strong></p>

        <p style="margin: 20px 0;">Should you require any further information or clarification, please do not hesitate to contact our administration office.</p>

        <p style="margin: 20px 0;">Yours faithfully,</p>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div style="display: flex; justify-content: space-between;">
            <div style="width: 60%;">
                @if($schoolInfo && $schoolInfo->principal_signature_url)
                    <div class="signature-image">
                        <img src="{{ $schoolInfo->principal_signature_url }}" alt="Principal Signature" class="signature-image">
                    </div>
                @else
                    <div class="signature-line"></div>
                @endif
                <p class="principal-name">{{ $schoolInfo->principal_name ?? 'Principal' }}</p>
                <p class="school-title">{{ $schoolInfo->school_name ?? 'ESA Learning Portal' }}</p>
            </div>
            <div style="width: 30%; text-align: right;">
                @if($schoolInfo && $schoolInfo->stamp_signature_url)
                    <img src="{{ $schoolInfo->stamp_signature_url }}" alt="Official Stamp" class="stamp">
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="display: flex; justify-content: space-between;">
            <div style="width: 60%;">
                <p class="motto" style="margin: 5px 0;">
                    {{ $schoolInfo->motto ?? 'Excellence in Education Through Technology' }}
                </p>
            </div>
            <div style="width: 40%; text-align: right;">
                <p class="generated-info" style="margin: 5px 0;">
                    Generated on: {{ now()->format('F j, Y \a\t g:i A') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
