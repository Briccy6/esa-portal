@extends('layouts.app')

@section('title', 'Exam Results - ' . $exam->title . ' - Excellent Servant Academy')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Results Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br {{ $score >= 60 ? 'from-green-500 to-emerald-600' : 'from-red-500 to-pink-600' }} rounded-full mb-6 shadow-lg">
            <i class="fas {{ $score >= 60 ? 'fa-trophy' : 'fa-times-circle' }} text-white text-3xl"></i>
        </div>
        
        <h1 class="text-4xl font-bold mb-4">
            @if($score >= 60)
                <span class="text-green-600">Congratulations!</span>
            @else
                <span class="text-red-600">Keep Practicing!</span>
            @endif
        </h1>
        
        <p class="text-xl text-gray-600 mb-8">You've completed the exam: "{{ $exam->title }}"</p>
        
        <!-- Score Display -->
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="mb-6">
                    <div class="text-6xl font-bold bg-gradient-to-r {{ $score >= 60 ? 'from-green-600 to-emerald-600' : 'from-red-600 to-pink-600' }} bg-clip-text text-transparent">
                        {{ number_format($score, 1) }}%
                    </div>
                    <p class="text-gray-600 mt-2">Your Score</p>
                </div>
                
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $correctAnswers }}</div>
                        <p class="text-gray-600">Correct</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-red-600">{{ $totalQuestions - $correctAnswers }}</div>
                        <p class="text-gray-600">Incorrect</p>
                    </div>
                </div>
                
                <div class="border-t pt-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $score >= 60 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $score >= 60 ? 'PASSED' : 'FAILED' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600">Time Taken:</span>
                        <span class="font-medium">
                            @if(isset($sessionData['end_time']) && isset($sessionData['start_time']))
                                {{ \Carbon\Carbon::parse($sessionData['start_time'])->diffForHumans(\Carbon\Carbon::parse($sessionData['end_time']), true) }}
                            @else
                                Not recorded
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">User Type:</span>
                        <span class="font-medium">{{ ucfirst($sessionData['user_type']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Performance Analysis</h3>
            </div>
            
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Accuracy Rate</span>
                        <span class="font-medium">{{ number_format(($correctAnswers / $totalQuestions) * 100, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 rounded-full h-3" style="width: {{ ($correctAnswers / $totalQuestions) * 100 }}%"></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <div class="text-2xl font-bold text-green-600">{{ $correctAnswers }}</div>
                        <p class="text-sm text-gray-600">Questions Correct</p>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-xl">
                        <div class="text-2xl font-bold text-red-600">{{ $totalQuestions - $correctAnswers }}</div>
                        <p class="text-sm text-gray-600">Questions Wrong</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-lightbulb text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Recommendations</h3>
            </div>
            
            <div class="space-y-4">
                @if($score >= 60)
                    <div class="flex items-start p-4 bg-green-50 rounded-xl">
                        <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-green-800 mb-1">Great Job!</h4>
                            <p class="text-gray-600">You've passed this exam. Consider trying more challenging exams to further improve your skills.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start p-4 bg-blue-50 rounded-xl">
                        <i class="fas fa-arrow-up text-blue-600 mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-blue-800 mb-1">Next Steps</h4>
                            <p class="text-gray-600">Explore advanced topics or try timed exams to improve your speed and accuracy.</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start p-4 bg-yellow-50 rounded-xl">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-yellow-800 mb-1">Keep Practicing</h4>
                            <p class="text-gray-600">Review the topics where you struggled and try the exam again after more preparation.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start p-4 bg-blue-50 rounded-xl">
                        <i class="fas fa-book text-blue-600 mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-blue-800 mb-1">Study Tips</h4>
                            <p class="text-gray-600">Focus on understanding concepts rather than memorizing answers. Practice regularly to improve.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="text-center">
        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('public.exams.index') }}" 
               class="px-8 py-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium shadow-lg hover:shadow-xl">
                <i class="fas fa-list mr-2"></i>Browse More Exams
            </a>
            
            @if($sessionData['user_type'] === 'guest')
                <a href="{{ route('register.choice') }}" 
                   class="px-8 py-4 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium shadow-lg hover:shadow-xl">
                    <i class="fas fa-user-plus mr-2"></i>Create Account to Save Progress
                </a>
            @endif
            
            <button onclick="window.print()" 
                    class="px-8 py-4 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-medium">
                <i class="fas fa-print mr-2"></i>Print Results
            </button>
        </div>
        
        @if($sessionData['user_type'] === 'guest')
            <div class="mt-8 p-6 bg-yellow-50 rounded-xl border border-yellow-200 max-w-2xl mx-auto">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-600 mr-3 mt-1"></i>
                    <div class="text-left">
                        <h4 class="font-semibold text-yellow-800 mb-2">Guest Session Notice</h4>
                        <p class="text-gray-700">You completed this exam as a guest. Your results are not saved permanently. Create an account to track your progress, save results, and access more features.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
