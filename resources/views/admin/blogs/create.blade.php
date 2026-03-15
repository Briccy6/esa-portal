@extends('admin.layout')

@section('title', 'Create Blog Post')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-purple-500 to-pink-500 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                        <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V9a1 1 0 00-1-1h-1v-1z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Blog Post</h1>
                    <p class="text-gray-600 text-sm mt-1">Share your thoughts and insights with your audience</p>
                </div>
            </div>
            <a href="{{ route('admin.blogs.index') }}"
               class="inline-flex items-center px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Blogs
            </a>
        </div>
    </div>

    <!-- Validation Errors Summary -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="text-red-800 font-medium">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        {{-- ✅ Route: admin.blogs.store --}}
        <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-6 space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Left Column - Main Content -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-900 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required
                                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 {{ $errors->has('title') ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-300' }}"
                                   placeholder="Enter a catchy title for your blog post">
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-900 mb-2">
                                Content <span class="text-red-500">*</span>
                            </label>
                            <textarea id="content"
                                      name="content"
                                      rows="15"
                                      required
                                      class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 resize-vertical {{ $errors->has('content') ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-300' }}"
                                      placeholder="Write your amazing blog content here...">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                You can use HTML formatting in your content
                            </p>
                        </div>

                        <!-- SEO Settings -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                                SEO Settings
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                                    <input type="text"
                                           id="meta_title"
                                           name="meta_title"
                                           value="{{ old('meta_title') }}"
                                           maxlength="60"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm {{ $errors->has('meta_title') ? 'border-red-500' : '' }}"
                                           placeholder="SEO title (max 60 chars)">
                                    <p class="mt-1 text-xs text-gray-500" id="meta_title_counter">{{ strlen(old('meta_title', '')) }}/60 characters</p>
                                    @error('meta_title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                    <textarea id="meta_description"
                                              name="meta_description"
                                              rows="3"
                                              maxlength="160"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm resize-vertical {{ $errors->has('meta_description') ? 'border-red-500' : '' }}"
                                              placeholder="SEO description (max 160 chars)">{{ old('meta_description') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500" id="meta_desc_counter">{{ strlen(old('meta_description', '')) }}/160 characters</p>
                                    @error('meta_description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Settings & Media -->
                    <div class="space-y-6">

                        <!-- Publish Settings -->
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Publish Settings</h3>

                            <!-- Status -->
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="status"
                                        name="status"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $errors->has('status') ? 'border-red-500' : '' }}">
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>📝 Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>🌐 Published</option>
                                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>📦 Archived</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Featured -->
                            <div class="mb-4">
                                <div class="flex items-center">
                                    {{-- ✅ name="is_featured" to match controller's $request->boolean('is_featured') --}}
                                    <input type="checkbox"
                                           id="is_featured"
                                           name="is_featured"
                                           value="1"
                                           {{ old('is_featured') ? 'checked' : '' }}
                                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                    <label for="is_featured" class="ml-3 flex items-center">
                                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Featured Post</span>
                                    </label>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Featured posts appear prominently on the blog page</p>
                                @error('is_featured')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="bg-white rounded-xl p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                </svg>
                                Images
                            </h3>
                            <div class="mb-4">
                                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Upload Images</label>
                                <input type="file"
                                       id="images"
                                       name="images[]"
                                       multiple
                                       accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 {{ $errors->has('images') ? 'border-red-500' : '' }}">
                                <p class="mt-2 text-xs text-gray-500">Multiple images allowed. Max 2MB each. JPEG, PNG, GIF.</p>
                                @error('images')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div id="image-previews" class="space-y-3"></div>
                        </div>

                        <!-- Documents -->
                        <div class="bg-white rounded-xl p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                Documents
                            </h3>
                            <div class="mb-4">
                                <label for="documents" class="block text-sm font-medium text-gray-700 mb-2">Upload Documents</label>
                                <input type="file"
                                       id="documents"
                                       name="documents[]"
                                       multiple
                                       accept=".pdf,.doc,.docx"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 {{ $errors->has('documents') ? 'border-red-500' : '' }}">
                                <p class="mt-2 text-xs text-gray-500">PDF, DOC, DOCX. Max 5MB per file.</p>
                                @error('documents')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div id="document-previews" class="space-y-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-2xl">
                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('admin.blogs.index') }}"
                       class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 text-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-sm hover:shadow-md inline-flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Blog Post
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- ✅ Fixed: was @section('scripts') — must be @push to pair with @stack('scripts') in layout --}}
@push('scripts')
<script>
document.getElementById('images').addEventListener('change', function(e) {
    const previews = document.getElementById('image-previews');
    previews.innerHTML = '';
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const div = document.createElement('div');
            div.className = 'border border-gray-200 rounded-lg p-3';
            div.innerHTML = `
                <div class="flex items-start space-x-3">
                    <img src="${URL.createObjectURL(file)}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                        <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        <div class="mt-2 space-y-2">
                            <input type="text" class="w-full px-2 py-1 text-xs border border-gray-300 rounded" placeholder="Caption" name="image_captions[${index}]">
                            <input type="text" class="w-full px-2 py-1 text-xs border border-gray-300 rounded" placeholder="Alt Text" name="image_alt_texts[${index}]">
                        </div>
                    </div>
                </div>
            `;
            previews.appendChild(div);
        }
    });
});

document.getElementById('documents').addEventListener('change', function(e) {
    const previews = document.getElementById('document-previews');
    previews.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-3 p-2 border border-gray-200 rounded-lg';
        const icon = file.type === 'application/pdf' ? '📄' : '📝';
        div.innerHTML = `
            <span class="text-2xl">${icon}</span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
            </div>
        `;
        previews.appendChild(div);
    });
});

// Character counters
document.getElementById('meta_title').addEventListener('input', function() {
    const counter = document.getElementById('meta_title_counter');
    counter.textContent = `${this.value.length}/60 characters`;
    counter.className = `mt-1 text-xs ${this.value.length > 60 ? 'text-red-500' : 'text-gray-500'}`;
});

document.getElementById('meta_description').addEventListener('input', function() {
    const counter = document.getElementById('meta_desc_counter');
    counter.textContent = `${this.value.length}/160 characters`;
    counter.className = `mt-1 text-xs ${this.value.length > 160 ? 'text-red-500' : 'text-gray-500'}`;
});
</script>
@endpush