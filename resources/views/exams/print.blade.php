<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }} - Exam Paper</title>
    <style>
        @media print {
            @page {
                margin: 0.5cm;
                size: A4;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: white;
        }
        
        .exam-paper {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }
        
        .school-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }
        
        .school-logo {
            max-width: 100px;
            max-height: 100px;
            margin-bottom: 10px;
        }
        
        .school-name {
            font-size: 24pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .school-motto {
            font-size: 12pt;
            font-style: italic;
            color: #333;
            margin-bottom: 10px;
        }
        
        .school-address {
            font-size: 10pt;
            color: #666;
        }
        
        .exam-header {
            background: #f5f5f5;
            padding: 20px;
            margin-bottom: 30px;
            border: 2px solid #000;
        }
        
        .exam-title {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        
        .exam-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            font-size: 11pt;
        }
        
        .exam-detail-item {
            display: flex;
        }
        
        .exam-detail-label {
            font-weight: bold;
            min-width: 120px;
        }
        
        .exam-detail-value {
            flex: 1;
        }
        
        .exam-instructions {
            background: #fff3cd;
            padding: 15px;
            margin-bottom: 30px;
            border-left: 4px solid #ffc107;
            font-size: 11pt;
        }
        
        .exam-instructions h3 {
            font-size: 14pt;
            margin-bottom: 10px;
            color: #856404;
        }
        
        .exam-instructions ul {
            margin-left: 20px;
        }
        
        .exam-instructions li {
            margin-bottom: 5px;
        }
        
        .questions-section {
            margin-bottom: 30px;
        }
        
        .question-item {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #ddd;
            background: #fafafa;
        }
        
        .question-number {
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 10px;
        }
        
        .question-text {
            font-size: 12pt;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .question-marks {
            float: right;
            font-weight: bold;
            background: #007bff;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10pt;
        }
        
        .question-options {
            margin-left: 20px;
        }
        
        .option-item {
            margin-bottom: 8px;
            font-size: 11pt;
        }
        
        .option-label {
            font-weight: bold;
            margin-right: 10px;
        }
        
        .exam-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .btn-print {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        
        .btn-print:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Exam
        </button>
    </div>

    <div class="exam-paper">
        <!-- School Header -->
        <div class="school-header">
            @if($schoolInfo && $schoolInfo->logo_url)
                <img src="{{ $schoolInfo->logo_url }}" alt="School Logo" class="school-logo">
            @endif
            <div class="school-name">{{ $schoolInfo->school_name ?? 'Excellent Servant Academy' }}</div>
            @if($schoolInfo && $schoolInfo->motto)
                <div class="school-motto">{{ $schoolInfo->motto }}</div>
            @endif
            @if($schoolInfo)
                <div class="school-address">
                    {{ $schoolInfo->address ?? '' }} 
                    @if($schoolInfo->phone) | Tel: {{ $schoolInfo->phone }} @endif
                    @if($schoolInfo->email) | Email: {{ $schoolInfo->email }} @endif
                </div>
            @endif
        </div>

        <!-- Exam Header -->
        <div class="exam-header">
            <div class="exam-title">{{ $exam->title }}</div>
            
            <div class="exam-details">
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Course:</span>
                    <span class="exam-detail-value">{{ $exam->course->course_name ?? 'N/A' }}</span>
                </div>
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Lesson:</span>
                    <span class="exam-detail-value">{{ $exam->lesson->title ?? 'N/A' }}</span>
                </div>
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Branch:</span>
                    <span class="exam-detail-value">{{ $exam->branch->name ?? 'N/A' }}</span>
                </div>
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Exam Type:</span>
                    <span class="exam-detail-value">{{ ucfirst($exam->exam_type) }}</span>
                </div>
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Total Marks:</span>
                    <span class="exam-detail-value">{{ $exam->total_marks }}</span>
                </div>
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Exam Weight:</span>
                    <span class="exam-detail-value">{{ $exam->exam_weight }}%</span>
                </div>
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Exam Date:</span>
                    <span class="exam-detail-value">{{ $exam->class_date ? \Carbon\Carbon::parse($exam->class_date)->format('d M Y') : 'N/A' }}</span>
                </div>
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Teacher:</span>
                    <span class="exam-detail-value">{{ $exam->teacher->teacher_name ?? 'N/A' }}</span>
                </div>
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Academic Year:</span>
                    <span class="exam-detail-value">{{ $exam->academic_year->year ?? 'N/A' }}</span>
                </div>
                <div class="exam-detail-item">
                    <span class="exam-detail-label">Term:</span>
                    <span class="exam-detail-value">{{ $exam->term->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Exam Instructions -->
        <div class="exam-instructions">
            <h3>Instructions:</h3>
            <ul>
                <li>Answer all questions.</li>
                <li>Write clearly and legibly.</li>
                <li>Show all working where necessary.</li>
                <li>No calculators or electronic devices allowed unless specified.</li>
                <li>Time allocated: {{ $exam->duration ?? 'N/A' }} minutes.</li>
            </ul>
        </div>

        <!-- Questions Section -->
        <div class="questions-section">
            <h2 style="text-align: center; margin-bottom: 20px; font-size: 16pt;">EXAM QUESTIONS</h2>
            
            @if($exam->questions->count() > 0)
                @foreach($exam->questions as $index => $question)
                    <div class="question-item">
                        <div class="question-number">
                            Question {{ $index + 1 }}
                            <span class="question-marks">{{ $question->marks ?? 0 }} Marks</span>
                        </div>
                        <div class="question-text">
                            {{ $question->question_text ?? $question->text ?? 'N/A' }}
                        </div>
                        
                        @if(in_array($question->question_type ?? 'multiple_choice', ['multiple_choice', 'multiple_select']))
                            <div class="question-options">
                                @if($question->option_a)
                                    <div class="option-item">
                                        <span class="option-label">A.</span>
                                        <span>{{ $question->option_a }}</span>
                                    </div>
                                @endif
                                @if($question->option_b)
                                    <div class="option-item">
                                        <span class="option-label">B.</span>
                                        <span>{{ $question->option_b }}</span>
                                    </div>
                                @endif
                                @if($question->option_c)
                                    <div class="option-item">
                                        <span class="option-label">C.</span>
                                        <span>{{ $question->option_c }}</span>
                                    </div>
                                @endif
                                @if($question->option_d)
                                    <div class="option-item">
                                        <span class="option-label">D.</span>
                                        <span>{{ $question->option_d }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        @if($question->question_image)
                            <div style="margin-top: 15px;">
                                <img src="{{ asset('images/question_images/' . $question->question_image) }}" 
                                     alt="Question Image" 
                                     style="max-width: 100%; height: auto; border: 1px solid #ddd;">
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 40px; color: #666;">
                    <p>No questions added to this exam yet.</p>
                </div>
            @endif
        </div>

        <!-- Exam Footer -->
        <div class="exam-footer">
            <p><strong>Total Questions:</strong> {{ $exam->questions->count() }} | <strong>Total Marks:</strong> {{ $exam->total_marks }}</p>
            <p style="margin-top: 10px;">Generated on: {{ now()->format('d M Y H:i') }}</p>
            @if($schoolInfo)
                <p style="margin-top: 5px;">{{ $schoolInfo->school_name ?? 'Excellent Servant Academy' }}</p>
            @endif
        </div>
    </div>
</body>
</html>
