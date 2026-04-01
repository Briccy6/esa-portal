<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Assessment Report - {{ $reportCard->student->full_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.5cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
            margin: 0;
            padding: 0;
        }
        
        .print-area {
            border: 1px solid black;
        }
        
        /* Header: logo | details | logo (table layout for DomPDF) */
        .print-header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #000;
        }
        .print-header-table td {
            vertical-align: middle;
            padding: 10px 8px;
        }
        .print-header-logo {
            width: 22%;
            text-align: center;
            border-right: 1px solid #000;
        }
        .print-header-logo--right {
            border-right: none;
            border-left: 1px solid #000;
        }
        .print-header-center {
            width: 56%;
            text-align: center;
            padding: 12px 14px !important;
        }
        .print-header-logo img,
        .school-logo-img {
            max-height: 88px;
            max-width: 110px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .print-logo-placeholder {
            width: 88px;
            height: 88px;
            margin: 0 auto;
            background: #ecf0f1;
            border: 2px dashed #bdc3c7;
            text-align: center;
            font-size: 8px;
            color: #7f8c8d;
            font-weight: bold;
            line-height: 84px;
        }
        .hdr-line {
            font-size: 10px;
            line-height: 1.45;
            margin: 0 0 3px 0;
        }
        .hdr-line .lbl {
            font-weight: bold;
        }
        .hdr-school-name {
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0 6px 0;
            letter-spacing: 0.3px;
        }
        .hdr-contact {
            font-size: 9px;
            line-height: 1.5;
            margin: 2px 0;
        }
        
        .title-section {
            background: #f0f0f0;
            border-bottom: 1px solid black;
            padding: 4px;
            text-align: center;
        }
        
        .title-section h2 {
            font-size: 12px;
            font-weight: bold;
            margin: 0;
        }
        
        .qualification-section {
            border-bottom: 1px solid black;
        }
        
        .qualification-row {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            font-size: 10px;
        }
        
        .qualification-cell {
            padding: 4px;
        }
        
        .qualification-cell:not(:last-child) {
            border-right: 1px solid black;
        }
        
        .legend-section {
            border-bottom: 1px solid black;
            padding: 4px;
            font-size: 8px;
        }
        
        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        
        .assessment-table th,
        .assessment-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
        }
        
        .assessment-table .text-left {
            text-align: left;
        }
        
        .assessment-table .bg-gray {
            background: #f0f0f0;
        }
        
        .assessment-table .bg-blue {
            background: #e6f3ff;
        }
        
        .assessment-table .bg-green {
            background: #e6ffe6;
        }
        
        .assessment-table .bg-yellow {
            background: #ffffe6;
        }
        
        .assessment-table .font-bold {
            font-weight: bold;
        }
        
        .competent-mark {
            color: green;
            font-weight: bold;
        }
        
        .not-competent-mark {
            color: red;
            text-decoration: underline;
            font-weight: bold;
        }
        
        .deliberation-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-top: 1px;
        }
        
        .deliberation-table th,
        .deliberation-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
        }
        
        .deliberation-table .text-left {
            text-align: left;
        }
        
        .deliberation-table .bg-gray {
            background: #f0f0f0;
        }
        
        .footer {
            background: #f0f0f0;
            padding: 2px;
            text-align: right;
            font-size: 7px;
            border-top: 1px solid black;
        }
        
        .checkbox {
            width: 16px;
            height: 16px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="print-area">
        @include('admin.report-cards.partials.print-assessment-inner', compact('reportCard', 'schoolInfo', 'studentPhotoBase64', 'studentPhotoMime'))
    </div>
</body>
</html>