@extends('dos.layout')

@section('title', 'Add Question')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    .question-form {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 12px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .btn-secondary {
        background: #6c757d;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .card {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 12px;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="question-form">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add Question to Exam: {{ $exam->title ?? 'Exam' }}
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dos.exams.questions.store', $exam->id) }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Question Text</label>
                        <textarea name="text" class="form-control" rows="4" required placeholder="Enter the question text here..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Option A</label>
                                <input type="text" name="option_a" class="form-control" placeholder="Enter option A">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Option B</label>
                                <input type="text" name="option_b" class="form-control" placeholder="Enter option B">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Option C</label>
                                <input type="text" name="option_c" class="form-control" placeholder="Enter option C">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Option D</label>
                                <input type="text" name="option_d" class="form-control" placeholder="Enter option D">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Correct Answer</label>
                                <select name="correct_answer" class="form-select" required>
                                    <option value="">Select correct answer</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Marks</label>
                                <input type="number" name="marks" class="form-control" value="1" min="1" placeholder="Enter marks">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Question Type (Optional)</label>
                        <select name="question_type" class="form-select">
                            <option value="">Select question type</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Difficulty Level (Optional)</label>
                        <select name="difficulty" class="form-select">
                            <option value="">Select difficulty</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Explanation (Optional)</label>
                        <textarea name="explanation" class="form-control" rows="3" placeholder="Enter explanation for the correct answer..."></textarea>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Save Question
                        </button>
                        <a href="{{ route('dos.exams.questions', $exam->id) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Back to Questions
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Quick Tips -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Tips for Creating Good Questions
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Keep questions clear and concise
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Ensure all options are plausible
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Avoid negative statements unless necessary
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Test for only one concept per question
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
