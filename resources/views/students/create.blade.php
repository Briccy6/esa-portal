@extends('layouts.app')

@section('title', 'Student Registration - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Student Registration</h2>
                <p class="text-gray-600">Join our academic community and start your learning journey</p>
                <div class="mt-4">
                    <a href="{{ route('register.choice') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Choose Different Registration Type
                    </a>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-8">
    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

        <!-- Academic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                                <select id="academic_year_id" name="academic_year_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Select Academic Year</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                                <select id="branch_id" name="branch_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required disabled></select>
                            </div>

                            <div>
                                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                                <select id="course_id" name="course_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required disabled></select>
                            </div>
                        </div>

        <!-- Personal Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" id="full_name" name="full_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label for="identification_type" class="block text-sm font-medium text-gray-700 mb-2">Identification Type</label>
                                <select name="identification_type" id="identification_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Select Type</option>
                                    <option value="ID">ID</option>
                                    <option value="Passport">Passport</option>
                                </select>
                            </div>

                            <div>
                                <label for="identification_number" class="block text-sm font-medium text-gray-700 mb-2">Identification Number</label>
                                <input type="text" id="identification_number" name="identification_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div id="id_photo_group" style="display:none;">
                                <label for="id_photo" class="block text-sm font-medium text-gray-700 mb-2">ID Document (Max: 200KB)</label>
                                <input type="file" id="id_photo" name="id_photo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="application/pdf,image/*">
                            </div>
                        </div>

                        <!-- Passport Photo -->
                        <div>
                            <label for="passport_photo" class="block text-sm font-medium text-gray-700 mb-2">Passport Photo (Max: 200KB)</label>
                            <input type="file" id="passport_photo" name="passport_photo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="image/*" required>
                        </div>

        <!-- Education Level -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Education Level</label>
                            <select name="education_level" id="education_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select Level</option>
                                <option value="s6">S6</option>
                                <option value="s3">S3</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Education Documents -->
                        <div class="space-y-4">
                            <div class="d-none" id="s6_field">
                                <label for="senior6_document" class="block text-sm font-medium text-gray-700 mb-2">S6 Result Slip (Max: 200KB)</label>
                                <input type="file" name="senior6_document" id="senior6_document" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="application/pdf,image/*">
                            </div>

                            <div class="d-none" id="s3_field">
                                <label for="senior3_document" class="block text-sm font-medium text-gray-700 mb-2">S3 Result Slip (Max: 200KB)</label>
                                <input type="file" name="senior3_document" id="senior3_document" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="application/pdf,image/*">
                            </div>

                            <div class="d-none" id="other_field">
                                <label for="other_supporting_document" class="block text-sm font-medium text-gray-700 mb-2">Other Supporting Document (Max: 200KB)</label>
                                <input type="file" name="other_supporting_document" id="other_supporting_document" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="application/pdf,image/*">
                            </div>
                        </div>

                        <!-- Registration Fee Display -->
                        <div class="alert alert-info" id="registration_fee_box" style="display:none;">
                            <strong>Registration Fee:</strong> <span id="registration_fee_amount">0</span> RWF
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-user-plus mr-2"></i>
                                Proceed to Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    document.getElementById('academic_year_id').addEventListener('change', function () {
        const yearId = this.value;
        fetch(`/api/branches?academic_year_id=${yearId}`)
            .then(res => res.json())
            .then(data => {
                const branchSelect = document.getElementById('branch_id');
                branchSelect.innerHTML = '<option value="">Select Branch</option>';
                data.forEach(branch => {
                    branchSelect.innerHTML += `<option value="${branch.id}">${branch.name}</option>`;
                });
                branchSelect.disabled = false;
            });
    });

    document.getElementById('branch_id').addEventListener('change', function () {
        const branchId = this.value;
        const yearId = document.getElementById('academic_year_id').value;
        fetch(`/api/courses?academic_year_id=${yearId}&branch_id=${branchId}`)
            .then(res => res.json())
            .then(data => {
                const courseSelect = document.getElementById('course_id');
                courseSelect.innerHTML = '<option value="">Select Course</option>';
                data.forEach(course => {
                    courseSelect.innerHTML += `<option value="${course.id}" data-fee="${course.registration_fee}">${course.course_name}</option>`;
                });
                courseSelect.disabled = false;
            });
    });

    document.getElementById('course_id').addEventListener('change', function () {
        const fee = this.options[this.selectedIndex].getAttribute('data-fee');
        if (fee) {
            document.getElementById('registration_fee_box').style.display = 'block';
            document.getElementById('registration_fee_amount').textContent = fee;
        } else {
            document.getElementById('registration_fee_box').style.display = 'none';
        }
    });

    document.getElementById('education_level').addEventListener('change', function () {
        document.getElementById('s6_field').classList.add('d-none');
        document.getElementById('s3_field').classList.add('d-none');
        document.getElementById('other_field').classList.add('d-none');

        if (this.value === 's6') {
            document.getElementById('s6_field').classList.remove('d-none');
        } else if (this.value === 's3') {
            document.getElementById('s3_field').classList.remove('d-none');
        } else if (this.value === 'other') {
            document.getElementById('other_field').classList.remove('d-none');
        }
    });
</script>
