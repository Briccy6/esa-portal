@extends('layouts.app')

@section('title', 'Exam Results - ' . $exam->title)

@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">
                <i class="fas fa-chart-line me-2"></i>
                Exam Results
            </h3>
        </div>
        <div class="card-body text-center">
            <!-- Success/Failure Message -->
            <div class="alert {{ $passed ? 'alert-success' : 'alert-danger' }} mb-4" role="alert">
                <h4 class="alert-heading">
                    <i class="fas {{ $passed ? 'fa-check-circle' : 'fa-times-circle' }} me-2"></i>
                    {{ $passed ? 'Congratulations! You Passed!' : 'Better Luck Next Time!' }}
                </h4>
                <p class="mb-0">
                    {{ $passed ? 'You have successfully completed the exam.' : 'You did not meet the passing score. Please review and try again.' }}
                </p>
            </div>

            <!-- Score Display -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Your Score</h5>
                            <h2 class="display-4 text-primary">{{ $score }}%</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h5 class="card-title text-success">Correct Answers</h5>
                            <h2 class="display-4 text-success">{{ $correctAnswers }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body">
                            <h5 class="card-title text-info">Total Questions</h5>
                            <h2 class="display-4 text-info">{{ $totalQuestions }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar {{ $passed ? 'bg-success' : 'bg-danger' }}" 
                         role="progressbar" 
                         style="width: {{ $score }}%"
                         aria-valuenow="{{ $score }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ $score }}%
                    </div>
                </div>
            </div>

            <!-- Exam Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Exam Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Exam Title:</strong> {{ $exam->title }}</p>
                            <p><strong>Subject:</strong> {{ $exam->subject ?? 'N/A' }}</p>
                            <p><strong>Exam Type:</strong> {{ ucfirst($exam->exam_type) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Marks:</strong> {{ $exam->total_marks ?? 100 }}</p>
                            <p><strong>Passing Score:</strong> {{ $exam->passing_score ?? 50 }}%</p>
                            <p><strong>Completed:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('public.exams.index') }}" class="btn btn-primary">
                    <i class="fas fa-list me-2"></i>
                    Browse More Exams
                </a>
                @if($exam->is_public && $exam->public_price > 0)
                    <a href="{{ route('public.exams.payment', $exam) }}" class="btn btn-success">
                        <i class="fas fa-redo me-2"></i>
                        Retake Exam
                    </a>
                @else
                    <a href="{{ route('public.exams.start', $exam) }}" class="btn btn-success">
                        <i class="fas fa-redo me-2"></i>
                        Retake Exam
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
