<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded max-w-lg mx-auto">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-4 bg-white shadow-md rounded p-6 max-w-lg mx-auto">
            <h2 class="text-2xl font-semibold text-center text-indigo-600">Create Your Account</h2>

            <!-- First Name -->
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" placeholder="Enter your first name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>

            <!-- Last Name -->
            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" placeholder="Enter your last name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>

            <!-- Birthday -->
            <div>
                <x-input-label for="birthday" :value="__('Birthday')" />
                <x-text-input id="birthday" placeholder="Select your birth date" class="block mt-1 w-full" type="date" name="birthday" :value="old('birthday')" required />
                <x-input-error :messages="$errors->get('birthday')" class="mt-2" />
            </div>

            <!-- Gender -->
            <div>
                <x-input-label for="gender" :value="__('Gender')" />
                <select id="gender" name="gender" class="block mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Select gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
            </div>

            <!-- Phone -->
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" placeholder="Enter your phone number" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" placeholder="Enter your email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Course -->
            <div>
                <x-input-label for="course" :value="__('Course')" />
                <select id="course" name="course" required class="block mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @forelse($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course') == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                    @empty
                        <option disabled selected>No courses available at the moment</option>
                    @endforelse
                </select>
                <x-input-error :messages="$errors->get('course')" class="mt-2" />
            </div>

            <!-- Study Mode -->
            <div>
                <x-input-label for="study_mode" :value="__('Preferred Study Mode')" />
                <select id="study_mode" name="study_mode" required class="block mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select study mode</option>
                    <option value="online" {{ old('study_mode') == 'online' ? 'selected' : '' }}>Online</option>
                    <option value="day" {{ old('study_mode') == 'day' ? 'selected' : '' }}>Day</option>
                    <option value="night" {{ old('study_mode') == 'night' ? 'selected' : '' }}>Night</option>
                </select>
                <x-input-error :messages="$errors->get('study_mode')" class="mt-2" />
            </div>

            <!-- Location -->
            <div>
                <x-input-label for="location" :value="__('Preferred Location')" />
                <select id="location" name="location" required class="block mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select location</option>
                    <option value="kigali" {{ old('location') == 'kigali' ? 'selected' : '' }}>Kigali</option>
                    <option value="musanze" {{ old('location') == 'musanze' ? 'selected' : '' }}>Musanze</option>
                </select>
                <x-input-error :messages="$errors->get('location')" class="mt-2" />
            </div>

 
            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" placeholder="Enter your password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" placeholder="Confirm your password" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
                           <!-- Upload ID -->
            <div>
                <x-input-label for="id_upload" :value="__('Upload ID')" />
                <input id="id_upload" type="file" name="id_document" accept=".jpg,.png,.jpeg,.pdf" class="block mt-1 w-full border border-gray-300 rounded-md" required />
                <x-input-error :messages="$errors->get('id_upload')" class="mt-2" />
            </div>

            <!-- Upload Result Slip -->
            <div>
                <x-input-label for="result_slip" :value="__('Upload Result Slip')" />
                <input id="result_slip" type="file" name="result_slip" accept=".jpg,.png,.jpeg,.pdf" class="block mt-1 w-full border border-gray-300 rounded-md" required />
                <x-input-error :messages="$errors->get('result_slip')" class="mt-2" />
            </div>

            <!-- Upload Passport Photo -->
            <div>
                <x-input-label for="passport_photo" :value="__('Upload Passport Photo')" />
                <input id="passport_photo" type="file" name="passport_photo" accept=".jpg,.png,.jpeg" class="block mt-1 w-full border border-gray-300 rounded-md" required />
                <x-input-error :messages="$errors->get('passport_photo')" class="mt-2" />
            </div>


                <x-primary-button class="ml-4">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>
