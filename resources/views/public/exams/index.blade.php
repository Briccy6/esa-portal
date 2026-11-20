@extends('layouts.public')

@section('title', 'Available Exams - Excellent Servant Academy')

@section('content')
<!-- ALX-style hero header -->
<div class="public-exams-page">
<div class="public-exams-hero bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white py-12 md:py-16">
    <div class="container mx-auto px-4 pt-12 pb-10 md:pt-16 md:pb-14">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <p class="inline-flex items-center space-x-2 rounded-full bg-white/10 px-4 py-1 mb-4 backdrop-blur-sm border border-white/10 text-xs font-semibold tracking-wide uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span class="text-white">Public practice tests</span>
                </p>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-3 text-white">
                    Test your exam<br class="hidden md:block">
                    readiness with ESA.
                </h1>
                <p class="text-base md:text-lg text-white max-w-xl mb-6">
                    Timed, auto‑marked practice exams built by ESA teachers to help you
                    identify gaps, build confidence, and improve before the real exam day.
                </p>
                @if($exams->count() > 0)
                    <div class="flex flex-wrap items-center gap-4 text-sm text-white">
                        <span>{{ $exams->count() }} practice tests available</span>
                        @if($exams->first()->courses->count() > 0)
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10">
                                <i class="fas fa-graduation-cap text-xs"></i>
                                <span class="text-white">Sample course: {{ $exams->first()->courses->first()->name }}</span>
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Compact summary card on the right -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 md:p-6 shadow-2xl backdrop-blur-sm">
                <h2 class="text-lg font-semibold mb-1 text-white">How public exams work</h2>
                <p class="text-sm text-white mb-4">
                    Choose a practice test, review the details, then start when you&apos;re ready.
                    You&apos;ll see your score as soon as you submit.
                </p>
                <ul class="space-y-3 text-sm text-white">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Multiple‑choice questions, marked automatically.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        <span>Clear duration and timing for every exam.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        <span>Free and paid options, with token support.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Section -->
<div class="bg-white">
    <div class="container mx-auto px-4 py-8">

    <!-- Subscription Banner -->
    @if($subscriptions->count() > 0)
    <div class="bg-gradient-to-br from-emerald-800 via-teal-700 to-emerald-800 rounded-2xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-10 rounded-full -mr-20 -mt-20"></div>
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="mb-4 lg:mb-0">
                    <h2 class="text-3xl font-bold mb-3 flex items-center">
                        <span class="bg-white bg-opacity-20 backdrop-blur-sm rounded-full p-2 mr-3">
                            🎯
                        </span>
                        Get More with Subscriptions
                    </h2>
                    <p class="text-blue-100 text-lg leading-relaxed">
                        Purchase tokens and save money on multiple practice tests. 
                        Subscriptions start from <span class="font-bold text-white">{{ $subscriptions->first()->formatted_price }}</span>!
                    </p>
                </div>
                <a href="{{ route('public.subscriptions.index') }}" 
                   class="bg-white text-indigo-600 px-8 py-4 rounded-xl font-bold hover:bg-blue-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    View Subscriptions
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                <input type="text" id="search-exams" placeholder="Search exams..." 
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Price Range</label>
                <select id="price-filter" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                    <option value="">All Prices</option>
                    <option value="free">Free</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                <select id="sort-exams" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Exams Grid -->
    <div id="exams-container">
        @if($exams->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($exams as $exam)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 exam-card group" 
                         data-exam-id="{{ $exam->id }}"
                         data-price="{{ $exam->public_price }}"
                         data-title="{{ $exam->title }}"
                         data-questions="{{ $exam->questions->count() }}">
                        
                        <!-- Exam Header -->
                        <div class="bg-gradient-to-br from-emerald-800 via-teal-700 to-emerald-800 p-6 text-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative z-10">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-xl font-bold leading-tight">{{ Str::limit($exam->title, 60) }}</h3>
                                    <span class="bg-white bg-opacity-20 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium">
                                        {{ $exam->questions->count() }} Questions
                                    </span>
                                </div>
                                
                                @if($exam->courses->count() > 0)
                                    <div class="flex items-center text-sm text-blue-100 mb-2">
                                        <i class="fas fa-graduation-cap mr-2 text-blue-200"></i>
                                        <span class="font-medium">{{ $exam->courses->pluck('name')->implode(', ') }}</span>
                                    </div>
                                @endif
                                
                                @if($exam->lessons->count() > 0)
                                    <div class="flex items-center text-sm text-blue-100">
                                        <i class="fas fa-book-open mr-2 text-blue-200"></i>
                                        <span>{{ $exam->lessons->pluck('title')->implode(', ') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Exam Body -->
                        <div class="p-6">
                            <p class="text-gray-600 mb-6 leading-relaxed">{{ Str::limit($exam->description ?? 'Practice your skills and prepare for exams with this comprehensive assessment.', 120) }}</p>
                            
                            <!-- Exam Meta -->
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium">Duration</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $exam->questions->count() * 2 }} minutes</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium">Mode</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">Self-paced</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium">Status</span>
                                    </div>
                                    <span class="ml-1 px-3 py-1 text-xs font-semibold rounded-full {{ $exam->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $exam->is_public ? 'Public' : 'Private' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Price/Token Info -->
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-6 border border-gray-200">
                                @if($exam->requiresPayment())
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-600 mb-1">Practice Test</p>
                                        <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">{{ $exam->formatted_public_price }}</p>
                                        @if(auth()->guard('client')->check() && auth()->guard('client')->user()->hasActiveSubscription())
                                            <p class="text-xs text-green-600 mt-2 flex items-center justify-center">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Covered by your subscription
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-600 mb-1">Free Access</p>
                                        <p class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">NO COST</p>
                                        @if(auth()->guard('client')->check())
                                            <p class="text-xs text-green-600 mt-2 flex items-center justify-center">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Free for you
                                            </p>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($exam->requiresTokens())
                                    <div class="mt-3 pt-3 border-t border-gray-300">
                                        <p class="text-xs text-gray-500 text-center">Or use {{ $exam->formatted_token_cost }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <a href="{{ route('public.exams.show', $exam) }}" 
                                   class="w-full bg-gradient-to-r from-emerald-700 to-teal-700 text-white py-3 px-4 rounded-xl hover:from-emerald-800 hover:to-teal-800 transition-all duration-200 text-center block font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Details
                                </a>
                                
                                @if($exam->isAccessible())
                                    <a href="{{ route('public.exams.start', $exam) }}" 
                                       class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 px-4 rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 text-center block font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                        <i class="fas fa-play mr-2"></i>
                                        Start Practice
                                    </a>
                                @else
                                    <button disabled class="w-full bg-gray-400 text-white py-3 px-4 rounded-xl cursor-not-allowed text-center block font-medium opacity-60">
                                        <i class="fas fa-lock mr-2"></i>
                                        Not Available
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $exams->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No Practice Tests Available</h3>
                <p class="text-gray-600 text-lg max-w-md mx-auto">There are currently no practice tests available. Check back later for exciting new assessments!</p>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript for Filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-exams');
    const priceFilter = document.getElementById('price-filter');
    const sortSelect = document.getElementById('sort-exams');
    const examCards = document.querySelectorAll('.exam-card');

    function filterExams() {
        const searchTerm = searchInput.value.toLowerCase();
        const priceValue = priceFilter.value;
        const sortBy = sortSelect.value;

        let filteredCards = Array.from(examCards).filter(card => {
            const title = card.dataset.title.toLowerCase();
            const price = parseFloat(card.dataset.price);
            
            // Search filter
            const matchesSearch = title.includes(searchTerm);
            
            // Price filter
            let matchesPrice = true;
            if (priceValue === 'free') {
                matchesPrice = price === 0;
            } else if (priceValue === 'paid') {
                matchesPrice = price > 0;
            }
            
            return matchesSearch && matchesPrice;
        });

        // Sort filtered cards
        filteredCards.sort((a, b) => {
            switch(sortBy) {
                case 'price-low':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price-high':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'oldest':
                    return a.dataset.examId - b.dataset.examId;
                default: // newest
                    return b.dataset.examId - a.dataset.examId;
            }
        });

        // Hide all cards first
        examCards.forEach(card => card.style.display = 'none');
        
        // Show filtered cards in order
        filteredCards.forEach(card => card.style.display = 'block');
    }

    searchInput.addEventListener('input', filterExams);
    priceFilter.addEventListener('change', filterExams);
    sortSelect.addEventListener('change', filterExams);
});
</script>
    </div>
</div>
</div>
@endsection
