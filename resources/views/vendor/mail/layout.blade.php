<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Excellent Servant Academy</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f7fa;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .email-wrapper {
            background-color: #f4f7fa;
            padding: 20px 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: 600;
        }

        .email-body {
            padding: 30px 20px;
        }

        .email-body h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .email-body h3 {
            color: #555;
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .email-body p {
            margin-bottom: 15px;
            color: #555;
            line-height: 1.8;
        }

        .email-body ul,
        .email-body ol {
            margin: 15px 0 15px 20px;
            color: #555;
            line-height: 1.8;
        }

        .email-body li {
            margin-bottom: 10px;
        }

        .fee-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background: #f9fafb;
            border-radius: 6px;
            overflow: hidden;
        }

        .fee-table th {
            background-color: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        .fee-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .fee-table tr:last-child td {
            border-bottom: none;
        }

        .fee-table tr.total td {
            background-color: #f0f3ff;
            font-weight: 600;
            border-top: 2px solid #667eea;
        }

        .highlight-box {
            background-color: #fef3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .highlight-box strong {
            color: #856404;
        }

        .alert-box {
            background-color: #f8d7da;
            border-left: 4px solid #f5c6cb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #721c24;
        }

        .alert-box strong {
            color: #721c24;
        }

        .contact-info {
            background-color: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .contact-info strong {
            color: #0066cc;
        }

        .contact-info a {
            color: #0066cc;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .btn-primary {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #5568d3;
            text-decoration: none;
        }

        .email-footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }

        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }

        .email-footer a:hover {
            text-decoration: underline;
        }

        @media only screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }

            .email-header h1 {
                font-size: 22px !important;
            }

            .email-body h2 {
                font-size: 18px !important;
            }

            .email-body {
                padding: 20px !important;
            }

            .fee-table th,
            .fee-table td {
                padding: 10px !important;
                font-size: 12px !important;
            }

            .highlight-box,
            .alert-box,
            .contact-info {
                padding: 12px !important;
                margin: 15px 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <h1>{{ config('app.name', 'Excellent Servant Academy') }}</h1>
            </div>

            <div class="email-body">
                {{ $slot }}
            </div>

            <div class="email-footer">
                <p style="margin: 0;">
                    © {{ date('Y') }} {{ config('app.name', 'Excellent Servant Academy') }} All rights reserved.<br>
                    <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
