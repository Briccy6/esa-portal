@extends('layouts.app')

@section('content')
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $babyeyi->reference_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Calibri', 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 12pt;
        }

        .container {
            max-width: 8.5in;
            margin: 0 auto;
            padding: 0.5in;
        }

        /* Header Section */
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            page-break-after: avoid;
        }

        .school-logo {
            max-height: 70px;
            margin-bottom: 10px;
        }

        .school-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 5px 0;
        }

        .school-info {
            font-size: 10pt;
            color: #555;
            margin: 2px 0;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 25px 0;
            color: #1a1a1a;
            page-break-after: avoid;
        }

        /* Content */
        .content {
            margin: 20px 0;
            line-height: 1.8;
        }

        .content p {
            margin-bottom: 12px;
            text-align: justify;
        }

        .content h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 15px 0 8px 0;
            color: #1a1a1a;
            page-break-after: avoid;
        }

        .content h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 12px 0 6px 0;
            color: #333;
            page-break-after: avoid;
        }

        .content h3 {
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 5px 0;
            color: #555;
            page-break-after: avoid;
        }

        .content ul,
        .content ol {
            margin: 10px 0 10px 30px;
        }

        .content li {
            margin-bottom: 6px;
        }

        .content table {
            margin: 15px 0;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .content table td,
        .content table th {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }

        .content table th {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        .content strong {
            font-weight: bold;
        }

        .content em {
            font-style: italic;
        }

        .content u {
            text-decoration: underline;
        }

        /* Footer Section */
        .footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #ccc;
            page-break-inside: avoid;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-line {
            margin-bottom: 40px;
            border-bottom: 1px solid #000;
            min-height: 45px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 11pt;
            margin-top: 5px;
        }

        .signature-sub {
            font-size: 9pt;
            color: #666;
            margin-top: 2px;
        }

        .stamp-area {
            margin-top: 15px;
            font-size: 9pt;
            color: #999;
        }

        /* Info Box */
        .info-box {
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
            padding: 10px;
            margin: 15px 0;
            font-size: 11pt;
        }

        .info-box strong {
            color: #007bff;
        }

        /* Page Break Handling */
        @page {
            margin: 0.5in;
            @bottom-center {
                content: counter(page) " of " counter(pages);
                font-size: 10pt;
                color: #999;
            }
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with School Information -->
        <div class="header">
            @php
                // DomPDF works best with filesystem paths for local images.
                // Support both legacy `logo_path` and new `logo_file_path`.
                $logoFsPath = null;
                if ($schoolInfo) {
                    $lp = $schoolInfo->logo_file_path ?? null;
                    $lp = $lp ?: ($schoolInfo->logo_path ?? null);
                    if (!empty($lp) && !filter_var($lp, FILTER_VALIDATE_URL)) {
                        $logoFsPath = public_path('storage/' . ltrim($lp, '/'));
                    }
                }
            @endphp
            @if (!empty($logoFsPath) && file_exists($logoFsPath))
                <img src="{{ $logoFsPath }}" class="school-logo" alt="School Logo">
            @elseif ($schoolInfo && $schoolInfo->logo_url)
                <img src="{{ $schoolInfo->logo_url }}" class="school-logo" alt="School Logo">
            @endif
            <div class="school-name">{{ $schoolInfo->school_name ?? 'SCHOOL NAME' }}</div>
            @if ($schoolInfo->location)
                <div class="school-info">{{ $schoolInfo->location }}</div>
            @endif
            @if ($schoolInfo->phone)
                <div class="school-info">Tel: {{ $schoolInfo->phone }}</div>
            @endif
            @if ($schoolInfo->email)
                <div class="school-info">Email: {{ $schoolInfo->email }}</div>
            @endif
            @if ($schoolInfo->website)
                <div class="school-info">Web: {{ $schoolInfo->website }}</div>
            @endif
        </div>

        <!-- Document Title -->
        <div class="document-title">
            BABYEYI
            @if ($babyeyi->title)
                <div style="font-size: 12pt; font-weight: normal; margin-top: 5px;">{{ $babyeyi->title }}</div>
            @endif
        </div>

        <!-- Document Metadata -->
        <div class="info-box">
            <strong>Reference:</strong> {{ $babyeyi->reference_number }} | 
            <strong>Date:</strong> {{ $babyeyi->created_at->format('F j, Y') }}
        </div>

        <!-- Main Content -->
        <div class="content">
            {!! $babyeyi->content !!}
        </div>

        <!-- Footer with Signature Space -->
        <div class="footer">
            <div class="signature-section">
                <!-- School Stamp & Principal Signature -->
                <div class="signature-box" style="width: 30%;">
                    @if ($schoolInfo && $schoolInfo->stamp_signature_url)
                        <img src="{{ $schoolInfo->stamp_signature_url }}" style="max-height: 60px; max-width: 90px; margin-bottom: 10px;">
                    @elseif ($schoolInfo && $schoolInfo->principal_signature_url)
                        <img src="{{ $schoolInfo->principal_signature_url }}" style="max-height: 60px; max-width: 90px; margin-bottom: 10px;">
                    @else
                        <div class="signature-line"></div>
                    @endif
                    <div class="signature-label">Head Teacher/Principal</div>
                    <div class="signature-sub">{{ $schoolInfo->principal_name ?? 'Signature Area' }}</div>
                    <div class="signature-sub">Date: ______________</div>
                </div>

                <!-- Document Certification -->
                <div class="signature-box" style="width: 35%;">
                    <div class="signature-line"></div>
                    <div class="signature-label">Document Verified</div>
                    <div class="signature-sub">Certified by School Administration</div>
                    <div class="signature-sub">Date: ______________</div>
                </div>

                <!-- Accountant Signature (No Storage) -->
                <div class="signature-box" style="width: 30%; border-left: 2px dashed #999; padding-left: 15px;">
                    <div class="signature-line"></div>
                    <div class="signature-label">Accountant/Manager</div>
                    <div class="signature-sub">Signature Area (On-site only)</div>
                    <div class="signature-sub">Date: ______________</div>
                </div>
            </div>
        </div>

        <!-- Document Information Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 9pt; color: #999; text-align: center;">
            <p>This document was generated by the {{ $schoolInfo->school_name ?? 'School' }} Student Management System</p>
            <p>Reference: {{ $babyeyi->reference_number }} | Generated: {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
