@extends('layouts.app')

@section('title', 'Taking Exam - ' . $exam->title . ' - Excellent Servant Academy')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Exam Header -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl p-8 mb-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $exam->title }}</h1>
                <div class="flex items-center space-x-6 text-blue-100">
                    <div class="flex items-center">
                        <i class="fas fa-list-ol mr-2"></i>
                        Question {{ $questionNumber }} of {{ $totalQuestions }}
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span id="timer">00:00</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl px-6 py-4">
                    <p class="text-sm text-blue-100 mb-1">Progress</p>
                    <p class="text-2xl font-bold">{{ round(($questionNumber / $totalQuestions) * 100) }}%</p>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-6">
            <div class="w-full bg-white bg-opacity-20 rounded-full h-3">
                <div class="bg-white rounded-full h-3 transition-all duration-300" 
                     style="width: {{ ($questionNumber / $totalQuestions) * 100 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Question Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
        <div class="mb-8">
            <div class="flex items-start justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Question {{ $questionNumber }}</h2>
                <div class="flex items-center space-x-2">
                    @if($sessionData['user_type'] === 'guest')
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-user-secret mr-1"></i>Guest Mode
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-800 leading-relaxed text-lg">{{ $currentQuestion->question_text }}</p>
            </div>
        </div>

        <!-- Answer Options -->
    <form id="answerForm" method="POST" action="{{ route('public.exams.submit', [$exam, $sessionId]) }}" class="space-y-4">
        @csrf
            <div class="grid gap-4">
                @foreach(['A', 'B', 'C', 'D'] as $option)
                    <label class="relative flex items-center p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all duration-200 answer-option" data-option="{{ $option }}">
                        <input type="radio" name="answer" value="{{ $option }}" class="sr-only" 
                               @if(old('answer') === $option) checked @endif>
                        <div class="flex items-center w-full">
                            <div class="w-8 h-8 border-2 border-gray-300 rounded-full flex items-center justify-center mr-4 option-indicator">
                                <div class="w-4 h-4 bg-blue-600 rounded-full hidden option-dot"></div>
                            </div>
                            <div class="flex-1">
                                <span class="text-lg font-medium text-gray-800">
                                    {{ $option }}. {{ $currentQuestion->{'option_' . strtolower($option)} ?? 'Option ' . $option }}
                                </span>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                @if($questionNumber > 1)
                    <a href="{{ route('public.exams.take.question', [$exam, $sessionId, $questionNumber - 1]) }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Previous
                    </a>
                @else
                    <div></div>
                @endif

                <div class="flex items-center space-x-4">
                    @if($questionNumber < $totalQuestions)
                        <button type="button" id="nextBtn" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            Next<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    @else
                        <button type="submit" form="answerForm" id="submitBtn" 
                                class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fas fa-check-circle mr-2"></i>Submit Exam
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Exam Info Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Exam Info</h3>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Total Questions:</span>
                    <span class="font-medium">{{ $totalQuestions }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Time Limit:</span>
                    <span class="font-medium">{{ $totalQuestions * 2 }} minutes</span>
                </div>
                <div class="flex justify-between">
                    <span>Passing Score:</span>
                    <span class="font-medium">60%</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-lightbulb text-green-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Tips</h3>
            </div>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                    <span>Read each question carefully</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                    <span>Choose the best possible answer</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                    <span>You can review before submitting</span>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-keyboard text-purple-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Shortcuts</h3>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Select Option:</span>
                    <span class="font-medium">Click or Tab</span>
                </div>
                <div class="flex justify-between">
                    <span>Next Question:</span>
                    <span class="font-medium">Enter</span>
                </div>
                <div class="flex justify-between">
                    <span>Submit Exam:</span>
                    <span class="font-medium">Ctrl+Enter</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const answerOptions = document.querySelectorAll('.answer-option');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const answerForm = document.getElementById('answerForm');
    const timerElement = document.getElementById('timer');
    
    let startTime = Date.now();
    let currentAnswer = null;

    // Timer
    function updateTimer() {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    setInterval(updateTimer, 1000);

    // Answer selection
    answerOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            const optionValue = this.dataset.option;
            
            // Remove previous selection
            answerOptions.forEach(opt => {
                opt.classList.remove('border-blue-600', 'bg-blue-50');
                opt.querySelector('.option-dot').classList.add('hidden');
                opt.querySelector('.option-indicator').classList.remove('border-blue-600', 'bg-blue-100');
                opt.querySelector('.option-indicator').classList.add('border-gray-300');
            });
            
            // Add selection to current
            this.classList.add('border-blue-600', 'bg-blue-50');
            this.querySelector('.option-dot').classList.remove('hidden');
            this.querySelector('.option-indicator').classList.remove('border-gray-300');
            this.querySelector('.option-indicator').classList.add('border-blue-600', 'bg-blue-100');
            
            radio.checked = true;
            currentAnswer = optionValue;
            
            // Enable navigation buttons
            if (nextBtn) nextBtn.disabled = false;
            if (submitBtn) submitBtn.disabled = false;
            
            // Auto-save answer
            saveAnswer(optionValue);
        });
    });

    // Save answer function
    function saveAnswer(answer) {
        return fetch(`{{ route('public.exams.save-answer', [$exam, $sessionId, $questionNumber]) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ answer: answer })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to save answer:', data);
            }
            return data;
        })
        .catch(error => {
            console.error('Error saving answer:', error);
            return { success: false };
        });
    }

    // Navigation
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            window.location.href = '{{ route('public.exams.take.question', [$exam, $sessionId, $questionNumber + 1]) }}';
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.')) {
                // Save the current answer before submitting
                if (currentAnswer) {
                    saveAnswer(currentAnswer).then(() => {
                        answerForm.submit();
                    });
                } else {
                    answerForm.submit();
                }
            }
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey && currentAnswer) {
            e.preventDefault();
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.click();
            } else if (nextBtn && !nextBtn.disabled) {
                nextBtn.click();
            }
        }
    });

    // Prevent accidental navigation
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = '';
    });
});
</script>
@endsection
