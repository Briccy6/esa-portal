@extends('layouts.app')

@section('title', 'Available Exams - Excellent Servant Academy')

@push('styles')
<style>
    /* Page-specific styles that complement the enhanced layout */
    .hero-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 4rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        }
    }
    
    .hero-title {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
    }
    
    .filters-container {
        background: var(--bg-white);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-lg);
        margin: 2rem 0;
        border: 1px solid var(--border-color);
    }
    
    .filter-group {
        margin-bottom: 1.5rem;
    }
    
    .filter-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .filter-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        background: var(--bg-white);
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .filter-select:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    .search-box {
        position: relative;
        margin-bottom: 2rem;
    }
    
    .search-input {
        width: 100%;
        padding: 1rem 3rem 1rem 3rem;
        border: 2px solid var(--border-color);
        border-radius: 50px;
        font-size: 1rem;
        background: var(--bg-white);
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    .search-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--gradient-primary);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .search-btn:hover {
        background: var(--gradient-secondary);
        transform: translateY(-50%) scale(1.05);
    }
    
    .exam-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .exam-card {
        height: 100%;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--bg-white);
        border-radius: 20px;
        box-shadow: var(--shadow-md);
        margin: 2rem 0;
    }
    
    .empty-icon {
        width: 80px;
        height: 80px;
        background: var(--gradient-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
    }
    
    /* Enhanced Mobile Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem !important;
        }
        
        .exam-grid {
            grid-template-columns: 1fr !important;
            gap: 1.5rem !important;
        }
        
        .filters-container {
            padding: 1.5rem !important;
            margin: 1rem 0 !important;
        }
        
        .search-input {
            padding: 0.75rem 2rem 0.75rem 2rem !important;
        }
    }
    
    @media (max-width: 640px) {
        .hero-title {
            font-size: 1.5rem !important;
        }
        
        .exam-grid {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
        
        .filters-container {
            padding: 1rem !important;
        }
    }
</style>
@endpush

@section('content')
@include('components.public-navigation')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-5xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-4">Test Your Skills</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
            Practice and prepare for your exams with our comprehensive skill assessments. 
            Build confidence and identify areas for improvement before the real exam.
        </p>
        @if($exams->count() > 0)
            <div class="mt-6 text-sm text-gray-500">
                Found {{ $exams->count() }} practice tests available
                @if($exams->first()->courses->count() > 0)
                    | Sample course: {{ $exams->first()->courses->first()->name }}
                @endif
            </div>
        @endif
    </div>

    <!-- Subscription Banner -->
    @if($subscriptions->count() > 0)
    <div class="subscription-banner bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
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
    <div class="filters-container bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
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
                         data-questions="{{ $exam->questions->count() }}">>
                        
                        <!-- Exam Header -->
                        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 p-6 text-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative z-10">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-xl font-bold leading-tight">{{ Str::limit($exam->title, 60) }}</h3>
                                    <button onclick="shareExam({{ $exam->id }}, '{{ Str::limit($exam->title, 40) }}')" 
                                            class="bg-white bg-opacity-20 hover:bg-opacity-30 p-2 rounded-lg transition-all duration-200"
                                            title="Share this exam">
                                        <i class="fas fa-share-alt text-white"></i>
                                    </button>
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
                                    <span class="text-sm font-semibold text-gray-900">Self-paced</span>
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
                                   class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 text-center block font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
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

// Share Function
function shareExam(examId, examTitle) {
    const url = window.location.origin + '/public/exams/' + examId;
    const text = `Check out this practice test: ${examTitle}`;
    
    if (navigator.share) {
        navigator.share({
            title: examTitle,
            text: text,
            url: url
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback - copy to clipboard
        const shareText = `${text} - ${url}`;
        navigator.clipboard.writeText(shareText).then(() => {
            showNotification('Link copied to clipboard!');
        }).catch(() => {
            // Last resort - open in new window
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        });
    }
}

function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
