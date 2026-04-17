@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Exam Payment</h1>
                <p class="text-xl text-gray-600">Complete payment to access this exam</p>
            </div>

            <!-- Exam Details -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{$exam->title}}</h2>
                        <p class="text-gray-600 mt-2">{{$exam->description}}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-600">{{$exam->formatted_public_price}}</div>
                        <div class="text-sm text-gray-500">One-time payment</div>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-900">Duration</div>
                                <div class="text-sm text-gray-600">{{$exam->duration ?? 60}} minutes</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100-4h2a1 1 0 100-2 2 2 0 00-2 2v11a2 2 0 002 2h6a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v11z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-900">Questions</div>
                                <div class="text-sm text-gray-600">{{$exam->questions_count ?? 'Multiple'}}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-900">Total Marks</div>
                                <div class="text-sm text-gray-600">{{$exam->total_marks ?? 100}}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($exam->start_time || $exam->end_time)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-blue-800">
                            @if($exam->start_time)
                            Available from: {{$exam->start_time->format('M d, Y H:i')}}
                            @endif
                            @if($exam->end_time)
                            {{($exam->start_time ? 'until' : 'Until')}}: {{$exam->end_time->format('M d, Y H:i')}}
                            @endif
                        </span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Payment Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Payment Information</h3>

                <form action="{{route('exam.payment.process', $exam->id)}}" method="POST" id="paymentForm">
                    @csrf

                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h4>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="full_name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter your full name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" name="email" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="your@email.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input type="tel" name="phone" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="+250 788 123 456">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ID Number/Passport *</label>
                                <input type="text" name="id_number" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="National ID or Passport number">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h4>
                        <div class="space-y-4">
                            <!-- Mobile Money -->
                            <label class="block border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="payment_method" value="mobile_money" class="mr-3" checked>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <div class="font-semibold">Mobile Money</div>
                                            <div class="text-sm text-gray-600">MTN, Airtel, Tigo</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Bank Card -->
                            <label class="block border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="payment_method" value="card" class="mr-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <div class="font-semibold">Credit/Debit Card</div>
                                            <div class="text-sm text-gray-600">Visa, Mastercard, American Express</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Bank Transfer -->
                            <label class="block border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="payment_method" value="bank_transfer" class="mr-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-purple-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 5a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm0 3a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <div class="font-semibold">Bank Transfer</div>
                                            <div class="text-sm text-gray-600">Direct bank deposit</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Conditional Payment Fields -->
                    <div id="mobileMoneyFields" class="mb-8">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number *</label>
                                <input type="tel" name="mobile_number" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="250 788 123 456">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Provider *</label>
                                <select name="mobile_provider" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select provider</option>
                                    <option value="mtn">MTN Mobile Money</option>
                                    <option value="airtel">Airtel Money</option>
                                    <option value="tigo">Tigo Cash</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="cardFields" class="mb-8 hidden">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Number *</label>
                                <input type="text" name="card_number"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="1234 5678 9012 3456">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                                <input type="text" name="card_expiry"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="MM/YY">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CVV *</label>
                                <input type="text" name="card_cvv"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="123">
                            </div>
                        </div>
                    </div>

                    <div id="bankFields" class="mb-8 hidden">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h5 class="font-semibold text-gray-900 mb-4">Bank Transfer Details</h5>
                            <div class="space-y-2 text-sm">
                                <p><strong>Bank:</strong> Bank of Kigali</p>
                                <p><strong>Account Name:</strong> Excellent Servant Academy</p>
                                <p><strong>Account Number:</strong> 1234567890</p>
                                <p><strong>Branch:</strong> Kigali Main Branch</p>
                                <p><strong>SWIFT Code:</strong> BKRWRWRW</p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Reference *</label>
                                <input type="text" name="transaction_reference" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter bank transaction reference">
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-8">
                        <label class="flex items-center">
                            <input type="checkbox" name="terms_accepted" required class="mr-3">
                            <span class="text-sm text-gray-700">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">terms and conditions</a> 
                                and understand that this payment is non-refundable once the exam is accessed.
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold text-lg transition duration-200">
                            Pay {{$exam->formatted_public_price}} and Start Exam
                        </button>
                    </div>
                </form>
            </div>

            <!-- Alternative Options -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mt-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Alternative Options</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-900 mb-2">Register as Student</h4>
                        <p class="text-gray-600 mb-4">Get free tokens and access to multiple exams with a student account.</p>
                        <a href="{{route('students.create')}}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                            Register Now
                        </a>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-900 mb-2">Buy Subscription</h4>
                        <p class="text-gray-600 mb-4">Get unlimited access to all exams with a subscription plan.</p>
                        <a href="{{route('subscriptions.index')}}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                            View Plans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method toggle
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const mobileMoneyFields = document.getElementById('mobileMoneyFields');
    const cardFields = document.getElementById('cardFields');
    const bankFields = document.getElementById('bankFields');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            mobileMoneyFields.classList.add('hidden');
            cardFields.classList.add('hidden');
            bankFields.classList.add('hidden');

            if (this.value === 'mobile_money') {
                mobileMoneyFields.classList.remove('hidden');
            } else if (this.value === 'card') {
                cardFields.classList.remove('hidden');
            } else if (this.value === 'bank_transfer') {
                bankFields.classList.remove('hidden');
            }
        });
    });

    // Form validation
    const form = document.getElementById('paymentForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>
@endsection
