@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-900">{{$exam->title}}</h1>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        In Progress
                    </span>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600" id="timer">00:00:00</div>
                        <div class="text-sm text-gray-500">Time Remaining</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{isset($currentQuestion) ? $currentQuestionNumber : 0}} / {{isset($questions) ? $questions->count() : 0}}
                        </div>
                        <div class="text-sm text-gray-500">Questions</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Question Panel -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <!-- Question Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold">Question {{$currentQuestionNumber ?? 1}}</h2>
                                <p class="text-blue-100">Select the best answer</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if(isset($currentQuestion) && $currentQuestion->points)
                                <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                                    {{$currentQuestion->points}} points
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Question Content -->
                    <div class="p-8">
                        @if(isset($currentQuestion))
                        <div class="mb-8">
                            <div class="prose max-w-none">
                                <p class="text-lg text-gray-800 leading-relaxed">
                                    {!! $currentQuestion->question_text !!}
                                </p>
                            </div>
                            
                            @if($currentQuestion->question_image)
                            <div class="mt-6 text-center">
                                <img src="{{asset('storage/' . $currentQuestion->question_image)}}" 
                                     alt="Question Image" 
                                     class="max-w-full h-auto rounded-lg shadow-md">
                            </div>
                            @endif
                        </div>

                        <!-- Answer Options -->
                        <div class="space-y-4" id="answerOptions">
                            @foreach($currentQuestion->options ?? [] as $index => $option)
                            <div class="answer-option border-2 border-gray-200 rounded-lg p-4 hover:border-blue-400 transition cursor-pointer"
                                 onclick="selectAnswer({{$index}})">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-400 mr-4 flex items-center justify-center"
                                         id="option-{{$index}}">
                                        <div class="w-3 h-3 rounded-full bg-blue-600 hidden" id="option-dot-{{$index}}"></div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-800">
                                            {{chr(65 + $index)}}. {!! $option !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-gray-500">No question available</p>
                        </div>
                        @endif
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="bg-gray-50 px-8 py-6 border-t">
                        <div class="flex items-center justify-between">
                            <button onclick="previousQuestion()" 
                                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition disabled:opacity-50"
                                    id="prevBtn" {{($currentQuestionNumber ?? 1) <= 1 ? 'disabled' : ''}}>
                                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Previous
                            </button>

                            <div class="flex space-x-3">
                                <button onclick="markForReview()" 
                                        class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    Mark for Review
                                </button>

                                @if(($currentQuestionNumber ?? 1) >= (isset($questions) ? $questions->count() : 1))
                                <button onclick="submitExam()" 
                                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Submit Exam
                                </button>
                                @else
                                <button onclick="nextQuestion()" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Next
                                    <svg class="w-5 h-5 inline ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Question Navigator -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Question Navigator</h3>
                    <div class="grid grid-cols-5 gap-2" id="questionGrid">
                        @if(isset($questions))
                        @foreach($questions as $index => $question)
                        <button onclick="jumpToQuestion({{$index + 1}})" 
                                class="w-10 h-10 rounded-lg border-2 border-gray-300 text-sm font-medium hover:border-blue-500 transition"
                                id="nav-question-{{$index + 1}}">
                            {{$index + 1}}
                        </button>
                        @endforeach
                        @endif
                    </div>
                    
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center text-sm">
                            <div class="w-4 h-4 bg-blue-600 rounded mr-2"></div>
                            <span>Current</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <div class="w-4 h-4 bg-green-600 rounded mr-2"></div>
                            <span>Answered</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                            <span>Marked for Review</span>
                        </div>
                    </div>
                </div>

                <!-- Exam Info -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duration</span>
                            <span class="font-medium">{{$exam->duration ?? 60}} minutes</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Questions</span>
                            <span class="font-medium">{{isset($questions) ? $questions->count() : 0}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Marks</span>
                            <span class="font-medium">{{$exam->total_marks ?? 100}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tokens Used</span>
                            <span class="font-medium text-blue-600">{{$exam->token_cost ?? 0}}</span>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">Instructions</h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li>• Select one answer for each question</li>
                        <li>• Use the navigator to jump between questions</li>
                        <li>• Mark questions for review if unsure</li>
                        <li>• Submit exam when you're finished</li>
                        <li>• You cannot change answers after submission</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div id="submitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Exam?</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to submit your exam? You cannot change your answers after submission.</p>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-yellow-800">
                            Unanswered questions: <span id="unansweredCount">0</span>
                        </span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button onclick="closeSubmitModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Cancel
                    </button>
                    <button onclick="confirmSubmit()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Submit Exam
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentQuestion = {{$currentQuestionNumber ?? 1}};
let totalQuestions = {{isset($questions) ? $questions->count() : 0}};
let answers = {};
let markedForReview = new Set();
let timeRemaining = {{($exam->duration ?? 60) * 60}}; // in seconds

// Timer
function updateTimer() {
    const hours = Math.floor(timeRemaining / 3600);
    const minutes = Math.floor((timeRemaining % 3600) / 60);
    const seconds = timeRemaining % 60;
    
    document.getElementById('timer').textContent = 
        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    if (timeRemaining > 0) {
        timeRemaining--;
        setTimeout(updateTimer, 1000);
    } else {
        submitExam();
    }
}

// Answer selection
function selectAnswer(optionIndex) {
    // Remove previous selection
    document.querySelectorAll('.answer-option').forEach(option => {
        option.classList.remove('border-blue-500', 'bg-blue-50');
    });
    
    // Add selection to current option
    const selectedOption = document.querySelectorAll('.answer-option')[optionIndex];
    selectedOption.classList.add('border-blue-500', 'bg-blue-50');
    
    // Update radio button
    document.querySelectorAll('[id^="option-dot-"]').forEach(dot => {
        dot.classList.add('hidden');
    });
    document.getElementById(`option-dot-${optionIndex}`).classList.remove('hidden');
    
    // Store answer
    answers[currentQuestion] = optionIndex;
    updateQuestionGrid();
}

// Navigation
function nextQuestion() {
    if (currentQuestion < totalQuestions) {
        jumpToQuestion(currentQuestion + 1);
    }
}

function previousQuestion() {
    if (currentQuestion > 1) {
        jumpToQuestion(currentQuestion - 1);
    }
}

function jumpToQuestion(questionNumber) {
    // Save current answer before moving
    if (answers[currentQuestion] !== undefined) {
        // Save to server via AJAX
        saveAnswer(currentQuestion, answers[currentQuestion]);
    }
    
    currentQuestion = questionNumber;
    // Load new question via AJAX or reload page
    window.location.href = `{{route('exams.take-question', [$exam->id, ':session'])}}`.replace(':session', '{{$session ?? 'current'}}') + '/' + questionNumber;
}

function markForReview() {
    if (markedForReview.has(currentQuestion)) {
        markedForReview.delete(currentQuestion);
    } else {
        markedForReview.add(currentQuestion);
    }
    updateQuestionGrid();
}

function updateQuestionGrid() {
    for (let i = 1; i <= totalQuestions; i++) {
        const button = document.getElementById(`nav-question-${i}`);
        button.classList.remove('bg-blue-600', 'bg-green-600', 'bg-yellow-500', 'text-white');
        button.classList.add('border-gray-300');
        
        if (i === currentQuestion) {
            button.classList.add('bg-blue-600', 'text-white');
            button.classList.remove('border-gray-300');
        } else if (answers[i] !== undefined) {
            if (markedForReview.has(i)) {
                button.classList.add('bg-yellow-500', 'text-white');
            } else {
                button.classList.add('bg-green-600', 'text-white');
            }
            button.classList.remove('border-gray-300');
        }
    }
}

// Submit
function submitExam() {
    const unanswered = Object.keys(answers).length;
    document.getElementById('unansweredCount').textContent = totalQuestions - unanswered;
    document.getElementById('submitModal').classList.remove('hidden');
}

function closeSubmitModal() {
    document.getElementById('submitModal').classList.add('hidden');
}

function confirmSubmit() {
    // Submit all answers via AJAX
    fetch(`{{route('exams.submit', [$exam->id, ':session'])}}`.replace(':session', '{{$session ?? 'current'}}'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{csrf_token()}}'
        },
        body: JSON.stringify({
            answers: answers,
            marked_for_review: Array.from(markedForReview)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        }
    })
    .catch(error => {
        console.error('Error submitting exam:', error);
    });
}

function saveAnswer(questionNumber, answer) {
    // Save answer to server via AJAX
    fetch(`/exams/{{$exam->id}}/save-answer/{{$session ?? 'current'}}/${questionNumber}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{csrf_token()}}'
        },
        body: JSON.stringify({ answer: answer })
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
}

// Initialize
updateTimer();
updateQuestionGrid();

// Auto-save every 30 seconds
setInterval(() => {
    if (answers[currentQuestion] !== undefined) {
        saveAnswer(currentQuestion, answers[currentQuestion]);
    }
}, 30000);

// Prevent leaving the page
window.addEventListener('beforeunload', (e) => {
    if (Object.keys(answers).length > 0) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endsection
