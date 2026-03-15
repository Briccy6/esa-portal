@extends('layouts.client')

@section('title', 'Settings - ESA Portal')

@section('content')
<div class="page-content">
    <!-- Page Header -->
    <div class="topbar">
        <div class="topbar-left">
            <h1>Settings</h1>
            <p>Manage your account preferences</p>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="panel">
        <div class="panel-title">Account Settings</div>
        
        <form action="{{ route('client.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Notification Settings -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Notifications</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-700">Email Notifications</div>
                            <p class="text-sm text-gray-500">Receive email updates about your account activity</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="email_notifications" 
                                   value="1"
                                   @if(old('email_notifications', $client->email_notifications ?? true)) checked @endif
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-700">Exam Reminders</div>
                            <p class="text-sm text-gray-500">Get reminded about upcoming exams and deadlines</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="exam_reminders" 
                                   value="1"
                                   @if(old('exam_reminders', $client->exam_reminders ?? true)) checked @endif
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Display Settings -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Display Preferences</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-700">Dark Mode</div>
                            <p class="text-sm text-gray-500">Use dark theme across the platform</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="dark_mode" 
                                   value="1"
                                   @if(old('dark_mode', $client->dark_mode ?? false)) checked @endif
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Privacy Settings -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Privacy & Security</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="font-medium text-gray-700 mb-3">Data & Privacy</div>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p>Your data is encrypted and stored securely.</p>
                            <p>We never share your personal information with third parties.</p>
                            <p>You can request a copy of your data at any time.</p>
                        </div>
                        <div class="mt-4 space-x-3">
                            <button type="button" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm">
                                <i class="fas fa-download mr-2"></i>
                                Download My Data
                            </button>
                            <button type="button" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-sm">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="border-t pt-6">
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('client.dashboard') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
