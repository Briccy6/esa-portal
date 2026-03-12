@extends('admin.layout')

@section('title', 'Add Question - ' . $questionBank->name)

@section('styles')
<!-- MathJax for mathematical equations -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script>
    window.MathJax = {
        tex: {
            inlineMath: [['$', '$'], ['\\(', '\\)']],
            displayMath: [['$$', '$$'], ['\\[', '\\]']],
            processEscapes: true,
            processEnvironments: true
        },
        options: {
            skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre']
        }
    };
</script>

<style>
.question-form {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.form-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #007bff;
}

.section-title {
    color: #007bff;
    margin-bottom: 20px;
    font-size: 1.3rem;
    font-weight: 600;
}

.question-type-card {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.question-type-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.question-type-card.selected {
    border-color: #007bff;
    background-color: #f8f9ff;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.question-type-icon {
    font-size: 2rem;
    color: #007bff;
    margin-bottom: 10px;
}

.question-type-title {
    font-weight: 600;
    margin-bottom: 5px;
}

.question-type-desc {
    color: #6c757d;
    font-size: 0.9rem;
}

.options-container {
    margin-top: 15px;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.option-input {
    flex: 1;
}

.option-checkbox {
    min-width: 20px;
}

.remove-option {
    color: #dc3545;
    cursor: pointer;
    font-size: 1.2rem;
}

.add-option-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
}

.add-option-btn:hover {
    background: #218838;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
}

.btn-custom {
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary-custom {
    background: #007bff;
    color: white;
}

.btn-primary-custom:hover {
    background: #0056b3;
    color: white;
}

.btn-secondary-custom {
    background: #6c757d;
    color: white;
}

.btn-secondary-custom:hover {
    background: #545b62;
    color: white;
}

.alert-info {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

/* Math and Science specific styles */
.math-preview {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
    min-height: 60px;
}

.image-upload-area {
    border: 2px dashed #007bff;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: #f8f9ff;
    cursor: pointer;
    transition: all 0.3s ease;
}

.image-upload-area:hover {
    background: #e6f3ff;
    border-color: #0056b3;
}

.image-upload-area.dragover {
    background: #cce7ff;
    border-color: #004085;
}

.uploaded-image-preview {
    max-width: 100%;
    max-height: 300px;
    border-radius: 8px;
    margin-top: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.subject-tabs {
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 20px;
}

.subject-tab {
    background: none;
    border: none;
    padding: 12px 24px;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    font-weight: 500;
}

.subject-tab.active {
    color: #007bff;
    border-bottom-color: #007bff;
    background: #f8f9ff;
}

.subject-tab:hover {
    color: #0056b3;
    background: #f1f8ff;
}

.math-tools {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin: 10px 0;
}

.math-tool-btn {
    background: #e9ecef;
    border: 1px solid #ced4da;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.math-tool-btn:hover {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.diagram-tools {
    display: flex;
    gap: 10px;
    align-items: center;
    margin: 10px 0;
}

.diagram-label {
    font-weight: 500;
    color: #495057;
}
</style>
@endsection

@section('content')
<div class="question-form">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Add Questions</h2>
            <p class="text-muted mb-0">Question Bank: <strong>{{ $questionBank->name }}</strong></p>
        </div>
        <a href="{{ route('admin.question-banks.show', $questionBank) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Question Bank
        </a>
    </div>

    <!-- Enhanced Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="questionTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual-tab-pane" type="button" role="tab" aria-controls="manual-tab-pane" aria-selected="true">
                <i class="fas fa-plus"></i> Manual Question
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="csv-tab" data-bs-toggle="tab" data-bs-target="#csv-tab-pane" type="button" role="tab" aria-controls="csv-tab-pane" aria-selected="false">
                <i class="fas fa-file-csv"></i> CSV Import
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="math-tab" data-bs-toggle="tab" data-bs-target="#math-tab-pane" type="button" role="tab" aria-controls="math-tab-pane" aria-selected="false">
                <i class="fas fa-square-root-alt"></i> Mathematics
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="biology-tab" data-bs-toggle="tab" data-bs-target="#biology-tab-pane" type="button" role="tab" aria-controls="biology-tab-pane" aria-selected="false">
                <i class="fas fa-microscope"></i> Biology
            </button>
        </li>
    </ul>

    <div class="tab-content" id="questionTabContent">
        <!-- Manual Question Tab -->
        <div class="tab-pane fade show active" id="manual-tab-pane" role="tabpanel" aria-labelledby="manual-tab" tabindex="0">
            <form action="{{ route('admin.question-banks.store-question', $questionBank) }}" method="POST">
                @csrf

        <!-- Question Type Selection -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-list-check"></i> Question Type
            </h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="question-type-card" data-type="multiple_choice">
                        <div class="question-type-icon">
                            <i class="fas fa-dot-circle"></i>
                        </div>
                        <div class="question-type-title">Multiple Choice</div>
                        <div class="question-type-desc">Select one correct answer</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="question-type-card" data-type="multiple_select">
                        <div class="question-type-icon">
                            <i class="fas fa-check-square"></i>
                        </div>
                        <div class="question-type-title">Multiple Select</div>
                        <div class="question-type-desc">Select multiple correct answers</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="question-type-card" data-type="true_false">
                        <div class="question-type-icon">
                            <i class="fas fa-toggle-on"></i>
                        </div>
                        <div class="question-type-title">True/False</div>
                        <div class="question-type-desc">True or false statement</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="question-type-card" data-type="short_answer">
                        <div class="question-type-icon">
                            <i class="fas fa-pen"></i>
                        </div>
                        <div class="question-type-title">Short Answer</div>
                        <div class="question-type-desc">Brief text response</div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="question_type" id="question_type" required>
        </div>

        <!-- Question Details -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-question-circle"></i> Question Details
            </h3>

            <div class="mb-3">
                <label for="text" class="form-label">Question Text <span class="text-danger">*</span></label>
                <textarea name="text" id="text" class="form-control" rows="4" required placeholder="Enter your question here... Use $ for inline math (e.g., $x^2 + y^2 = r^2$) or $$ for display math (e.g., $$\\int_{-\\infty}^{\\infty} e^{-x^2} dx = \\sqrt{\\pi}$$)"></textarea>
                <div class="mt-2">
                    <small class="text-muted">Math expressions: Use LaTeX notation between $ or $$ delimiters</small>
                    <div id="mathPreview" class="math-preview">Preview will appear here...</div>
                </div>
                @error('text')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="marks" class="form-label">Marks <span class="text-danger">*</span></label>
                        <input type="number" name="marks" id="marks" class="form-control" min="1" required placeholder="Enter marks">
                        @error('marks')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="weight" class="form-label">Weight (Optional)</label>
                        <input type="number" name="weight" id="weight" class="form-control" min="0" max="10" step="0.1" placeholder="Question weight (0-10)">
                        @error('weight')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Answer Options (shown conditionally) -->
        <div class="form-section" id="options_section" style="display: none;">
            <h3 class="section-title">
                <i class="fas fa-list"></i> Answer Options
            </h3>
            
            <div id="options_container" class="options-container">
                <!-- Options will be dynamically added here -->
            </div>

            <button type="button" class="add-option-btn" id="add_option_btn">
                <i class="fas fa-plus"></i> Add Option
            </button>
        </div>

        <!-- Image Upload Section (shown conditionally) -->
        <div class="form-section" id="image_section" style="display: none;">
            <h3 class="section-title">
                <i class="fas fa-image"></i> Question Image/Diagram
            </h3>
            
            <div class="mb-3">
                <label class="form-label">Upload Image (Optional)</label>
                <div class="image-upload-area" id="imageUploadArea">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                    <p class="mb-2">Click to upload or drag and drop</p>
                    <p class="text-muted small">Supports: JPG, PNG, GIF (Max 5MB)</p>
                    <input type="file" id="questionImage" name="question_image" accept="image/*" style="display: none;">
                </div>
                <div id="imagePreview" style="display: none;">
                    <img id="uploadedImage" class="uploaded-image-preview" alt="Question image">
                    <button type="button" class="btn btn-sm btn-danger mt-2" id="removeImage">
                        <i class="fas fa-trash"></i> Remove Image
                    </button>
                </div>
            </div>
        </div>

        <!-- True/False Options (shown conditionally) -->
        <div class="form-section" id="true_false_section" style="display: none;">
            <h3 class="section-title">
                <i class="fas fa-toggle-on"></i> Correct Answer
            </h3>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="correct_answer" id="true_option" value="true">
                    <label class="form-check-label" for="true_option">
                        <strong>True</strong>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="correct_answer" id="false_option" value="false">
                    <label class="form-check-label" for="false_option">
                        <strong>False</strong>
                    </label>
                </div>
                @error('correct_answer')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Short Answer (shown conditionally) -->
        <div class="form-section" id="short_answer_section" style="display: none;">
            <h3 class="section-title">
                <i class="fas fa-key"></i> Correct Answer
            </h3>
            
            <div class="mb-3">
                <label for="short_answer_text" class="form-label">Expected Answer <span class="text-danger">*</span></label>
                <textarea name="correct_answer" id="short_answer_text" class="form-control" rows="3" placeholder="Enter the expected answer..."></textarea>
                <div class="alert alert-info mt-2">
                    <i class="fas fa-info-circle"></i> This answer will be used for exact matching. Consider variations of the correct answer.
                </div>
                @error('correct_answer')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="{{ route('admin.question-banks.show', $questionBank) }}" class="btn-custom btn-secondary-custom">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-custom btn-primary-custom">
                <i class="fas fa-save"></i> Save Question
            </button>
        </div>
            </form>
        </div>

        <!-- CSV Import Tab -->
        <div class="tab-pane fade" id="csv-tab-pane" role="tabpanel" aria-labelledby="csv-tab" tabindex="0">
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-file-csv"></i> Import Questions from CSV
                </h3>

                <div class="alert alert-info mb-4">
                    <h5><i class="fas fa-info-circle"></i> CSV Format Requirements</h5>
                    <p class="mb-2">Your CSV file should contain the following columns in order:</p>
                    <ol class="mb-2">
                        <li><strong>question_type</strong> - multiple_choice, multiple_select, true_false, or short_answer</li>
                        <li><strong>text</strong> - The question text</li>
                        <li><strong>marks</strong> - Points for the question</li>
                        <li><strong>options</strong> - For multiple choice/select: pipe-separated options (e.g., "Option A|Option B|Option C|Option D")</li>
                        <li><strong>correct_answer</strong> - For multiple choice: option number (1,2,3,4); For multiple select: comma-separated numbers (1,3); For true/false: "true" or "false"; For short answer: the expected text</li>
                        <li><strong>weight</strong> - Optional question weight (0-10)</li>
                    </ol>
                    <p class="mb-0"><strong>Example:</strong> multiple_choice,"What is 2+2?",4,"2|3|4|5",3,1</p>
                </div>

                <form action="{{ route('admin.question-banks.import', $questionBank) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                        @error('csv_file')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="has_header" id="has_header" value="1">
                            <label class="form-check-label" for="has_header">
                                CSV file has header row (will be skipped)
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.question-banks.show', $questionBank) }}" class="btn-custom btn-secondary-custom">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn-custom btn-primary-custom">
                            <i class="fas fa-upload"></i> Import Questions
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sample CSV Download -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-download"></i> Sample CSV Templates
                </h3>
                <p class="mb-3">Download specialized CSV templates for different subjects:</p>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <i class="fas fa-square-root-alt"></i> Mathematics Template
                                </h5>
                                <p class="card-text">Pre-formatted template with mathematical equations and symbols using LaTeX notation.</p>
                                <a href="{{ asset('mathematics_questions_template.csv') }}" class="btn btn-outline-primary btn-sm" download>
                                    <i class="fas fa-download"></i> Download Math Template
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <i class="fas fa-microscope"></i> Biology Template
                                </h5>
                                <p class="card-text">Specialized template for biological questions with diagram labels and topics.</p>
                                <a href="{{ asset('biology_questions_template.csv') }}" class="btn btn-outline-success btn-sm" download>
                                    <i class="fas fa-download"></i> Download Biology Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="{{ asset('questions_template.csv') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-download"></i> Download General Template
                    </a>
                    <small class="text-muted ms-2">Basic template for all question types</small>
                </div>
            </div>
        </div>

        <!-- Mathematics Tab -->
        <div class="tab-pane fade" id="math-tab-pane" role="tabpanel" aria-labelledby="math-tab" tabindex="0">
            <form action="{{ route('admin.question-banks.store-question', $questionBank) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="question_type" value="multiple_choice">
                <input type="hidden" name="subject_area" value="mathematics">

                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-square-root-alt"></i> Mathematics Question
                    </h3>

                    <div class="mb-3">
                        <label for="math_text" class="form-label">Mathematical Question <span class="text-danger">*</span></label>
                        <textarea name="text" id="math_text" class="form-control" rows="4" required placeholder="Enter mathematical question... e.g., Solve for x: $x^2 + 5x + 6 = 0$"></textarea>
                        
                        <div class="math-tools mt-2">
                            <span class="diagram-label">Quick symbols:</span>
                            <button type="button" class="math-tool-btn" onclick="insertMath('\\frac{a}{b}')">Fraction</button>
                            <button type="button" class="math-tool-btn" onclick="insertMath('x^2')">Power</button>
                            <button type="button" class="math-tool-btn" onclick="insertMath('\\sqrt{x}')">Square Root</button>
                            <button type="button" class="math-tool-btn" onclick="insertMath('\\sum_{i=1}^{n}')">Sum</button>
                            <button type="button" class="math-tool-btn" onclick="insertMath('\\int_{a}^{b}')">Integral</button>
                            <button type="button" class="math-tool-btn" onclick="insertMath('\\lim_{x \\to \\infty}')">Limit</button>
                            <button type="button" class="math-tool-btn" onclick="insertMath('\\pi')">π</button>
                            <button type="button" class="math-tool-btn" onclick="insertMath('\\infty')">∞</button>
                        </div>
                        
                        <div id="mathPreview" class="math-preview mt-2">Mathematical preview will appear here...</div>
                        
                        @error('text')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="math_marks" class="form-label">Marks <span class="text-danger">*</span></label>
                                <input type="number" name="marks" id="math_marks" class="form-control" min="1" required placeholder="Enter marks">
                                @error('marks')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="math_difficulty" class="form-label">Difficulty Level</label>
                                <select name="difficulty_level" id="math_difficulty" class="form-control">
                                    <option value="1">Easy</option>
                                    <option value="2" selected>Medium</option>
                                    <option value="3">Hard</option>
                                    <option value="4">Very Hard</option>
                                    <option value="5">Expert</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Math Options Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-list"></i> Answer Options (Mathematical)
                    </h3>
                    
                    <div id="math_options_container" class="options-container">
                        <!-- Math options will be dynamically added here -->
                    </div>

                    <button type="button" class="add-option-btn" id="add_math_option_btn">
                        <i class="fas fa-plus"></i> Add Math Option
                    </button>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.question-banks.show', $questionBank) }}" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-custom btn-primary-custom">
                        <i class="fas fa-save"></i> Save Math Question
                    </button>
                </div>
            </form>
        </div>

        <!-- Biology Tab -->
        <div class="tab-pane fade" id="biology-tab-pane" role="tabpanel" aria-labelledby="biology-tab" tabindex="0">
            <form action="{{ route('admin.question-banks.store-question', $questionBank) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="question_type" value="multiple_choice">
                <input type="hidden" name="subject_area" value="biology">

                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-microscope"></i> Biology Question
                    </h3>

                    <div class="mb-3">
                        <label for="biology_text" class="form-label">Biological Question <span class="text-danger">*</span></label>
                        <textarea name="text" id="biology_text" class="form-control" rows="4" required placeholder="Enter biological question... e.g., Identify the structure shown in the diagram below:"></textarea>
                        @error('text')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Biological Diagram/Image</label>
                        <div class="image-upload-area" id="bioImageUploadArea">
                            <i class="fas fa-microscope fa-3x text-muted mb-2"></i>
                            <p class="mb-2">Upload biological diagram or illustration</p>
                            <p class="text-muted small">Diagrams: cell structures, organs, ecosystems, etc.</p>
                            <input type="file" id="biologyImage" name="question_image" accept="image/*" style="display: none;">
                        </div>
                        <div id="bioImagePreview" style="display: none;">
                            <img id="bioUploadedImage" class="uploaded-image-preview" alt="Biological diagram">
                            <div class="mt-2">
                                <label for="diagram_labels" class="form-label">Diagram Labels (Optional)</label>
                                <input type="text" name="diagram_labels" id="diagram_labels" class="form-control" placeholder="e.g., A: Nucleus, B: Mitochondria, C: Cell membrane">
                                <small class="text-muted">Enter labels for parts A, B, C, etc. shown in the diagram</small>
                            </div>
                                    <button type="button" class="btn btn-sm btn-danger mt-2" id="removeBioImage">
                                        <i class="fas fa-trash"></i> Remove Diagram
                                    </button>
                                </div>
                            </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="biology_marks" class="form-label">Marks <span class="text-danger">*</span></label>
                                <input type="number" name="marks" id="biology_marks" class="form-control" min="1" required placeholder="Enter marks">
                                @error('marks')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="biology_topic" class="form-label">Biology Topic</label>
                                <select name="biology_topic" id="biology_topic" class="form-control">
                                    <option value="">Select Topic</option>
                                    <option value="cell_biology">Cell Biology</option>
                                    <option value="genetics">Genetics</option>
                                    <option value="ecology">Ecology</option>
                                    <option value="physiology">Physiology</option>
                                    <option value="anatomy">Anatomy</option>
                                    <option value="evolution">Evolution</option>
                                    <option value="biochemistry">Biochemistry</option>
                                    <option value="botany">Botany</option>
                                    <option value="zoology">Zoology</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biology Options Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-list"></i> Answer Options (Biological)
                    </h3>
                    
                    <div id="biology_options_container" class="options-container">
                        <!-- Biology options will be dynamically added here -->
                    </div>

                    <button type="button" class="add-option-btn" id="add_biology_option_btn">
                        <i class="fas fa-plus"></i> Add Biology Option
                    </button>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.question-banks.show', $questionBank) }}" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-custom btn-primary-custom">
                        <i class="fas fa-save"></i> Save Biology Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionTypeCards = document.querySelectorAll('.question-type-card');
    const questionTypeInput = document.getElementById('question_type');
    const optionsSection = document.getElementById('options_section');
    const trueFalseSection = document.getElementById('true_false_section');
    const shortAnswerSection = document.getElementById('short_answer_section');
    const optionsContainer = document.getElementById('options_container');
    const addOptionBtn = document.getElementById('add_option_btn');
    const imageSection = document.getElementById('image_section');
    
    let optionCount = 0;
    let mathOptionCount = 0;
    let biologyOptionCount = 0;

    // Question type selection
    questionTypeCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            questionTypeCards.forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Set the question type
            const type = this.dataset.type;
            questionTypeInput.value = type;
            
            // Show/hide appropriate sections
            optionsSection.style.display = 'none';
            trueFalseSection.style.display = 'none';
            shortAnswerSection.style.display = 'none';
            imageSection.style.display = 'block'; // Show image section for all types
            
            if (type === 'multiple_choice' || type === 'multiple_select') {
                optionsSection.style.display = 'block';
                if (optionsContainer.children.length === 0) {
                    addOption();
                    addOption();
                }
            } else if (type === 'true_false') {
                trueFalseSection.style.display = 'block';
            } else if (type === 'short_answer') {
                shortAnswerSection.style.display = 'block';
            }
        });
    });

    // Math preview functionality
    function updateMathPreview(textareaId, previewId) {
        const textarea = document.getElementById(textareaId);
        const preview = document.getElementById(previewId);
        
        if (textarea && preview) {
            textarea.addEventListener('input', function() {
                preview.innerHTML = this.value || 'Preview will appear here...';
                // Re-render MathJax
                if (window.MathJax) {
                    MathJax.typesetPromise([preview]).catch(function (err) {
                        console.log('MathJax error:', err);
                    });
                }
            });
        }
    }

    // Initialize math previews
    updateMathPreview('text', 'mathPreview');
    updateMathPreview('math_text', 'mathPreview');
    
    // Insert math symbols
    window.insertMath = function(symbol) {
        const activeTextarea = document.activeElement;
        if (activeTextarea && activeTextarea.tagName === 'TEXTAREA') {
            const start = activeTextarea.selectionStart;
            const end = activeTextarea.selectionEnd;
            const text = activeTextarea.value;
            
            activeTextarea.value = text.substring(0, start) + symbol + text.substring(end);
            activeTextarea.selectionStart = activeTextarea.selectionEnd = start + symbol.length;
            activeTextarea.focus();
            
            // Trigger input event to update preview
            activeTextarea.dispatchEvent(new Event('input'));
        }
    };

    // Image upload functionality
    function setupImageUpload(uploadAreaId, inputId, previewId, imageId, removeBtnId) {
        const uploadArea = document.getElementById(uploadAreaId);
        const fileInput = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const image = document.getElementById(imageId);
        const removeBtn = document.getElementById(removeBtnId);
        
        if (!uploadArea || !fileInput) return;
        
        // Click to upload
        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });
        
        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleImageUpload(files[0], preview, image, uploadArea);
            }
        });
        
        // File input change
        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                handleImageUpload(this.files[0], preview, image, uploadArea);
            }
        });
        
        // Remove image
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                preview.style.display = 'none';
                uploadArea.style.display = 'block';
                fileInput.value = '';
            });
        }
    }

    function handleImageUpload(file, preview, image, uploadArea) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Image size must be less than 5MB.');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            image.src = e.target.result;
            preview.style.display = 'block';
            uploadArea.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    // Setup image uploads
    setupImageUpload('imageUploadArea', 'questionImage', 'imagePreview', 'uploadedImage', 'removeImage');
    setupImageUpload('bioImageUploadArea', 'biologyImage', 'bioImagePreview', 'bioUploadedImage', 'removeBioImage');

    // Add option functionality
    function addOption() {
        optionCount++;
        const optionDiv = document.createElement('div');
        optionDiv.className = 'option-item';
        
        const isMultipleSelect = questionTypeInput.value === 'multiple_select';
        const inputType = isMultipleSelect ? 'checkbox' : 'radio';
        const inputName = isMultipleSelect ? 'correct_answers[]' : 'correct_answer';
        
        optionDiv.innerHTML = `
            <input type="${inputType}" name="${inputName}" value="option_${optionCount}" class="option-checkbox">
            <input type="text" name="options[]" class="form-control option-input" placeholder="Enter option text..." required>
            <i class="fas fa-times remove-option"></i>
        `;
        
        optionsContainer.appendChild(optionDiv);
        
        // Add remove functionality
        optionDiv.querySelector('.remove-option').addEventListener('click', function() {
            optionDiv.remove();
        });
    }

    // Math options functionality
    function addMathOption() {
        mathOptionCount++;
        const container = document.getElementById('math_options_container');
        const optionDiv = document.createElement('div');
        optionDiv.className = 'option-item';
        
        optionDiv.innerHTML = `
            <input type="radio" name="correct_answer" value="math_option_${mathOptionCount}" class="option-checkbox">
            <input type="text" name="options[]" class="form-control option-input" placeholder="Enter mathematical option... e.g., $x = -2$" required>
            <i class="fas fa-times remove-option"></i>
        `;
        
        container.appendChild(optionDiv);
        
        // Add remove functionality
        optionDiv.querySelector('.remove-option').addEventListener('click', function() {
            optionDiv.remove();
        });
        
        // Add math preview to this option
        const optionInput = optionDiv.querySelector('.option-input');
        optionInput.addEventListener('input', function() {
            // Re-render MathJax for all math previews
            if (window.MathJax) {
                MathJax.typesetPromise([container]).catch(function (err) {
                    console.log('MathJax error:', err);
                });
            }
        });
    }

    // Biology options functionality
    function addBiologyOption() {
        biologyOptionCount++;
        const container = document.getElementById('biology_options_container');
        const optionDiv = document.createElement('div');
        optionDiv.className = 'option-item';
        
        optionDiv.innerHTML = `
            <input type="radio" name="correct_answer" value="bio_option_${biologyOptionCount}" class="option-checkbox">
            <input type="text" name="options[]" class="form-control option-input" placeholder="Enter biological option..." required>
            <i class="fas fa-times remove-option"></i>
        `;
        
        container.appendChild(optionDiv);
        
        // Add remove functionality
        optionDiv.querySelector('.remove-option').addEventListener('click', function() {
            optionDiv.remove();
        });
    }

    // Tab change handlers - initialize options when switching tabs
    document.getElementById('math-tab').addEventListener('shown.bs.tab', function() {
        const container = document.getElementById('math_options_container');
        if (container.children.length === 0) {
            addMathOption();
            addMathOption();
            addMathOption();
            addMathOption();
        }
    });
    
    document.getElementById('biology-tab').addEventListener('shown.bs.tab', function() {
        const container = document.getElementById('biology_options_container');
        if (container.children.length === 0) {
            addBiologyOption();
            addBiologyOption();
            addBiologyOption();
            addBiologyOption();
        }
    });

    // Event listeners for add option buttons
    addOptionBtn.addEventListener('click', addOption);
    
    // Add event listeners for math and biology option buttons
    const addMathOptionBtn = document.getElementById('add_math_option_btn');
    if (addMathOptionBtn) {
        addMathOptionBtn.addEventListener('click', addMathOption);
    }
    
    const addBiologyOptionBtn = document.getElementById('add_biology_option_btn');
    if (addBiologyOptionBtn) {
        addBiologyOptionBtn.addEventListener('click', addBiologyOption);
    }

    // Initialize math options if math tab is active on load
    if (document.getElementById('math-tab-pane').classList.contains('active')) {
        const container = document.getElementById('math_options_container');
        if (container.children.length === 0) {
            addMathOption();
            addMathOption();
            addMathOption();
            addMathOption();
        }
    }
});
</script>
@endsection
