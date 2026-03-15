@extends('teacher.layout')

@section('styles')
<style>
.import-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 15px;
}

.import-header h2 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.import-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.import-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.import-card .card-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.csv-format {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 1.5rem;
}

.csv-format h5 {
    color: #10b981;
    font-weight: 600;
    margin-bottom: 1rem;
}

.csv-table {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}

.csv-table th {
    background: #10b981;
    color: white;
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
}

.csv-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
}

.csv-example {
    background: #f0fdf4;
    border-left: 4px solid #10b981;
    padding: 1rem;
    margin-top: 1rem;
    border-radius: 0 8px 8px 0;
}

.upload-area {
    border: 2px dashed #cbd5e1;
    border-radius: 15px;
    padding: 3rem;
    text-align: center;
    transition: all 0.3s ease;
    background: #fafbfc;
}

.upload-area:hover {
    border-color: #10b981;
    background: #f0fdf4;
}

.upload-area i {
    font-size: 3rem;
    color: #10b981;
    margin-bottom: 1rem;
}

.btn-import {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-import:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3);
    color: white;
}

.alert-custom {
    border-radius: 10px;
    border: none;
    padding: 1rem 1.5rem;
}

.alert-custom i {
    font-size: 1.2rem;
    margin-right: 0.5rem;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="import-header">
        <div class="container">
            <h2><i class="bi bi-cloud-upload"></i> Import Questions to "{{ $questionBank->name }}"</h2>
            <p class="mb-0">Upload a CSV file to add multiple questions to this question bank</p>
        </div>
    </div>

    <!-- Back Button -->
    <div class="container mb-4">
        <a href="{{ route('teacher.question-banks.show', $questionBank) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Question Bank
        </a>
    </div>

    <!-- Alerts -->
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show alert-custom" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-warning alert-dismissible fade show alert-custom" role="alert">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>Import Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <div class="container">
        <div class="row">
            <!-- Upload Form -->
            <div class="col-lg-7">
                <div class="import-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-arrow-up"></i> Upload CSV File</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('teacher.question-banks.import', $questionBank) }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="upload-area">
                                <i class="bi bi-file-earmark-spreadsheet"></i>
                                <h4>Choose CSV File</h4>
                                <p class="text-muted mb-3">Select a CSV file containing questions to import</p>
                                
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="csv_file" name="csv_file" 
                                           accept=".csv,.txt" required>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle"></i>
                                        Maximum file size: 10MB. Accepted formats: CSV, TXT
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('teacher.question-banks.show', $questionBank) }}" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-import">
                                    <i class="bi bi-cloud-upload"></i> Import Questions
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- CSV Format Guide -->
            <div class="col-lg-5">
                <div class="import-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> CSV Format Guide</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="csv-format">
                            <h5>Required Columns:</h5>
                            <div class="csv-table">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Column</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>question_type</td>
                                            <td>multiple_choice, true_false, etc.</td>
                                        </tr>
                                        <tr>
                                            <td>text</td>
                                            <td>Question text</td>
                                        </tr>
                                        <tr>
                                            <td>option_a</td>
                                            <td>First option (if applicable)</td>
                                        </tr>
                                        <tr>
                                            <td>option_b</td>
                                            <td>Second option (if applicable)</td>
                                        </tr>
                                        <tr>
                                            <td>correct_answer</td>
                                            <td>Correct answer (A, B, True, False)</td>
                                        </tr>
                                        <tr>
                                            <td>marks</td>
                                            <td>Question marks</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="csv-example">
                            <h6>Example CSV:</h6>
                            <pre class="mb-0">question_type,text,option_a,option_b,correct_answer,marks
multiple_choice,"What is $2+2$?",3,4,B,1
multiple_choice,"Solve for x: $x^2 + 5x + 6 = 0$","$x = -2$","$x = -3$",A,2
true_false,"The sky is blue",,True,True,1</pre>
                        </div>

                        <div class="mt-3">
                            <h6><i class="bi bi-download"></i> Download CSV Templates:</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ asset('mathematics_questions_template.csv') }}" class="btn btn-outline-primary btn-sm" download>
                                    <i class="bi bi-file-earmark-spreadsheet"></i> Mathematics Questions Template
                                </a>
                                <a href="{{ asset('biology_questions_template.csv') }}" class="btn btn-outline-success btn-sm" download>
                                    <i class="bi bi-file-earmark-spreadsheet"></i> Biology Questions Template
                                </a>
                                <a href="{{ asset('questions_template.csv') }}" class="btn btn-outline-secondary btn-sm" download>
                                    <i class="bi bi-file-earmark-spreadsheet"></i> General Questions Template
                                </a>
                            </div>
                            <div class="alert alert-info mt-2">
                                <i class="bi bi-info-circle"></i>
                                <small>Download these templates to see the proper format for different subject areas. Mathematics and Biology templates include subject-specific columns and examples.</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6>Optional Columns:</h6>
                            <ul class="small text-muted">
                                <li><strong>option_c, option_d</strong> - Additional options</li>
                                <li><strong>explanation</strong> - Answer explanation</li>
                                <li><strong>difficulty_level</strong> - 1-5 scale</li>
                                <li><strong>subject_area</strong> - mathematics, biology, general</li>
                                <li><strong>keywords</strong> - Search keywords</li>
                            </ul>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="bi bi-lightbulb"></i>
                            <strong>Tip:</strong> Use spreadsheet software like Excel or Google Sheets to create your CSV file, then export it as CSV format.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
