@extends('admin.layout')

@section('title', 'Add Question - ' . $questionBank->name)

@section('styles')
<style>
.question-form {
    max-width: 800px;
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

    <!-- Tab Navigation -->
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
                <textarea name="text" id="text" class="form-control" rows="4" required placeholder="Enter your question here..."></textarea>
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
                    <i class="fas fa-download"></i> Sample CSV Template
                </h3>
                <p class="mb-3">Download a sample CSV file to understand the required format:</p>
                <a href="{{ asset('questions_template.csv') }}" class="btn btn-outline-primary" download>
                    <i class="fas fa-download"></i> Download Sample CSV
                </a>
            </div>
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
    
    let optionCount = 0;

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

    addOptionBtn.addEventListener('click', addOption);
});
</script>
@endsection
