@extends('layouts.app')

@section('title', 'Bulk Question Upload - ESA Portal')

@push('styles')
<style>
    /* Modern Design System */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    
    :root {
        --primary-color: #1e293b;
        --secondary-color: #3b82f6;
        --accent-color: #06b6d4;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-success: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        line-height: 1.6;
        overflow-x: hidden;
        background: var(--bg-light);
    }

    .upload-card {
        background: var(--bg-white);
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
    }

    .upload-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-xl);
    }

    .btn-primary {
        background: var(--gradient-primary);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-md);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: var(--shadow-xl);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .btn-success {
        background: var(--gradient-success);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-md);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-xl);
        background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
    }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: var(--bg-white);
        font-family: 'Inter', sans-serif;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: translateY(-1px);
    }

    .template-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .template-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
    }

    .template-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--secondary-color);
    }

    .upload-zone {
        border: 3px dashed var(--border-color);
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        position: relative;
        overflow: hidden;
    }

    .upload-zone:hover {
        border-color: var(--secondary-color);
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
    }

    .upload-zone.dragover {
        border-color: var(--secondary-color);
        background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
        transform: scale(1.02);
    }

    .file-input {
        display: none;
    }

    .upload-icon {
        width: 80px;
        height: 80px;
        background: var(--gradient-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: white;
    }

    .info-card {
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid;
        transition: all 0.3s ease;
    }

    .info-card.blue {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-color: #3b82f6;
        color: #1e40af;
    }

    .info-card.green {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-color: #10b981;
        color: #065f46;
    }

    .info-card.yellow {
        background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
        border-color: #f59e0b;
        color: #92400e;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .sample-format {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        overflow-x: auto;
    }

    @media (max-width: 768px) {
        .upload-zone {
            padding: 2rem 1rem;
        }
        
        .template-card {
            padding: 1.5rem;
        }
        
        .btn-primary, .btn-success {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-upload mr-3 text-blue-600"></i>
                    Bulk Question Upload
                </h1>
                <p class="text-gray-600">Upload multiple questions at once using CSV or Excel files</p>
            </div>
            <a href="{{ route('admin.exams.index') }}" class="btn-primary">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Exams
            </a>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="upload-card mb-8">
        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-file-upload mr-3 text-blue-600"></i>
                Upload Questions
            </h2>

            <form id="bulkUploadForm" @submit.prevent="submitForm">
                @csrf
                
                <!-- Exam Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Exam *</label>
                    <select name="exam_id" id="examSelect" required class="form-input">
                        <option value="">Choose an exam...</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">
                                {{ $exam->exam_name }} 
                                @if($exam->courses->isNotEmpty())
                                    ({{ $exam->courses->pluck('course_name')->implode(', ') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Subject *</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="template-card cursor-pointer">
                            <input type="radio" name="subject" value="mathematics" required class="sr-only" @change="updateTemplates()">
                            <div class="text-center">
                                <div class="text-4xl mb-2">📐</div>
                                <h3 class="font-bold text-lg mb-1">Mathematics</h3>
                                <p class="text-sm text-gray-600">Algebra, Geometry, Calculus</p>
                            </div>
                        </label>
                        
                        <label class="template-card cursor-pointer">
                            <input type="radio" name="subject" value="biology" required class="sr-only" @change="updateTemplates()">
                            <div class="text-center">
                                <div class="text-4xl mb-2">🧬</div>
                                <h3 class="font-bold text-lg mb-1">Biology</h3>
                                <p class="text-sm text-gray-600">Cell Biology, Genetics, Ecology</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Question File *</label>
                    <div class="upload-zone" id="uploadZone">
                        <input type="file" name="question_file" id="fileInput" class="file-input" 
                               accept=".csv,.xlsx,.xls" required @change="handleFileSelect">
                        
                        <div class="upload-icon">
                            <i class="fas fa-file-csv"></i>
                        </div>
                        
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            Drop your CSV/Excel file here or click to browse
                        </h3>
                        <p class="text-gray-600 mb-4">Supports CSV, Excel (.xlsx, .xls) files</p>
                        <p class="text-sm text-gray-500">Maximum file size: 10MB</p>
                        
                        <div id="fileInfo" class="hidden mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-green-600 mr-3"></i>
                                <div>
                                    <p class="font-semibold text-green-800" id="fileName"></p>
                                    <p class="text-sm text-green-600" id="fileSize"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Upload for Biology -->
                <div x-show="selectedSubject === 'biology'" class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-images mr-2 text-green-600"></i>
                        Upload Biology Images (Optional)
                    </label>
                    <div class="upload-zone" id="imageUploadZone" style="border-color: #10b981; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
                        <input type="file" name="image_files[]" id="imageInput" class="file-input" 
                               accept="image/jpeg,image/jpg,image/png,image/gif" multiple @change="handleImageSelect">
                        
                        <div class="upload-icon" style="background: var(--gradient-success);">
                            <i class="fas fa-images"></i>
                        </div>
                        
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            Upload biology diagrams and images
                        </h3>
                        <p class="text-gray-600 mb-4">Supports JPEG, PNG, GIF files</p>
                        <p class="text-sm text-gray-500">Maximum file size: 2MB per image</p>
                        <p class="text-xs text-gray-400 mt-2">Name images to match CSV image paths (e.g., "mitochondria_diagram.jpg")</p>
                        
                        <div id="imageInfo" class="hidden mt-4">
                            <div class="space-y-2" id="imageList"></div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" :disabled="processing" class="btn-primary text-lg px-12 py-4">
                        <template x-if="!processing">
                            <span>
                                <i class="fas fa-upload mr-3"></i>
                                Upload Questions
                            </span>
                        </template>
                        <template x-if="processing">
                            <span>
                                <div class="loading-spinner mr-3"></div>
                                Processing...
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Template Downloads -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Mathematics Template -->
        <div class="template-card">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-calculator text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Mathematics Template</h3>
                    <p class="text-gray-600">Download the CSV template for math questions</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="info-card blue">
                    <h4 class="font-semibold mb-2">Template includes:</h4>
                    <ul class="text-sm space-y-1">
                        <li>• Question text</li>
                        <li>• Multiple choice options (A, B, C, D)</li>
                        <li>• Correct answer</li>
                        <li>• Points and difficulty level</li>
                        <li>• Explanation/notes</li>
                    </ul>
                </div>
                
                <button onclick="downloadTemplate('mathematics')" class="btn-success w-full">
                    <i class="fas fa-download mr-2"></i>
                    Download Math Template
                </button>
            </div>
        </div>

        <!-- Biology Template -->
        <div class="template-card">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-dna text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Biology Template</h3>
                    <p class="text-gray-600">Download the CSV template for biology questions with image support</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="info-card green">
                    <h4 class="font-semibold mb-2">Template includes:</h4>
                    <ul class="text-sm space-y-1">
                        <li>• Question text</li>
                        <li>• Multiple choice options (A, B, C, D)</li>
                        <li>• Correct answer</li>
                        <li>• Points and difficulty level</li>
                        <li>• Biology topic classification</li>
                        <li>• Image path (optional)</li>
                        <li>• Explanation/notes</li>
                    </ul>
                </div>
                
                <div class="info-card blue">
                    <h4 class="font-semibold mb-2">Image Support:</h4>
                    <ul class="text-sm space-y-1">
                        <li>• Upload images alongside CSV file</li>
                        <li>• Name images to match image paths in CSV</li>
                        <li>• Supported formats: JPEG, PNG, GIF</li>
                        <li>• Images stored automatically for questions</li>
                    </ul>
                </div>
                
                <button onclick="downloadTemplate('biology')" class="btn-success w-full">
                    <i class="fas fa-download mr-2"></i>
                    Download Biology Template
                </button>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="info-card yellow">
            <h4 class="font-bold text-lg mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                File Format
            </h4>
            <ul class="text-sm space-y-2">
                <li>• CSV files with comma-separated values</li>
                <li>• Excel files (.xlsx, .xls) also supported</li>
                <li>• First row must contain headers</li>
                <li>• Maximum 10MB file size</li>
            </ul>
        </div>

        <div class="info-card blue">
            <h4 class="font-bold text-lg mb-3 flex items-center">
                <i class="fas fa-list-check mr-2"></i>
                Required Fields
            </h4>
            <ul class="text-sm space-y-2">
                <li>• Question Text (cannot be empty)</li>
                <li>• Option B (cannot be empty)</li>
                <li>• Correct Answer (A, B, C, or D)</li>
                <li>• All other fields are optional</li>
            </ul>
        </div>

        <div class="info-card green">
            <h4 class="font-bold text-lg mb-3 flex items-center">
                <i class="fas fa-lightbulb mr-2"></i>
                Tips
            </h4>
            <ul class="text-sm space-y-2">
                <li>• Download templates for correct format</li>
                <li>• Test with a small file first</li>
                <li>• Check for empty rows before upload</li>
                <li>• Use UTF-8 encoding for special characters</li>
            </ul>
        </div>
    </div>
</div>

<script>
function downloadTemplate(subject) {
    window.open(`{{ route('admin.exams.download-template', '') }}${subject}`, '_blank');
}

document.addEventListener('alpine:init', () => {
    Alpine.data('bulkUpload', () => ({
        processing: false,
        selectedSubject: null,
        
        init() {
            // Handle subject selection
            document.querySelectorAll('input[name="subject"]').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    this.selectedSubject = e.target.value;
                    this.updateTemplates();
                });
            });
        },
        
        updateTemplates() {
            // This will be handled by x-show directives
        },
        
        submitForm() {
            this.processing = true;
            
            const form = document.getElementById('bulkUploadForm');
            const formData = new FormData(form);
            
            fetch('{{ route('admin.exams.bulk-upload.process') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.processing = false;
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    form.reset();
                    document.getElementById('fileInfo').classList.add('hidden');
                    document.getElementById('imageInfo').classList.add('hidden');
                    document.getElementById('imageList').innerHTML = '';
                    this.selectedSubject = null;
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                this.processing = false;
                showNotification('An error occurred while uploading. Please try again.', 'error');
                console.error('Error:', error);
            });
        }
    }));
});

// File upload handling
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('fileInput');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');

// Image upload handling
const imageUploadZone = document.getElementById('imageUploadZone');
const imageInput = document.getElementById('imageInput');
const imageInfo = document.getElementById('imageInfo');
const imageList = document.getElementById('imageList');

// Click to upload (CSV)
uploadZone.addEventListener('click', () => {
    fileInput.click();
});

// Click to upload (Images)
imageUploadZone.addEventListener('click', () => {
    imageInput.click();
});

// Drag and drop for CSV
uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('dragover');
});

uploadZone.addEventListener('dragleave', () => {
    uploadZone.classList.remove('dragover');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect({ target: { files } });
    }
});

// Drag and drop for Images
imageUploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    imageUploadZone.classList.add('dragover');
});

imageUploadZone.addEventListener('dragleave', () => {
    imageUploadZone.classList.remove('dragover');
});

imageUploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    imageUploadZone.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        imageInput.files = files;
        handleImageSelect({ target: { files } });
    }
});

// File selection
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.classList.remove('hidden');
    } else {
        fileInfo.classList.add('hidden');
    }
}

// Image selection
function handleImageSelect(event) {
    const files = event.target.files;
    if (files.length > 0) {
        imageInfo.classList.remove('hidden');
        imageList.innerHTML = '';
        
        Array.from(files).forEach((file, index) => {
            const imageItem = document.createElement('div');
            imageItem.className = 'flex items-center justify-between p-2 bg-green-50 rounded border border-green-200';
            imageItem.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-image text-green-600 mr-2"></i>
                    <div>
                        <p class="text-sm font-medium text-green-800">${file.name}</p>
                        <p class="text-xs text-green-600">${formatFileSize(file.size)}</p>
                    </div>
                </div>
                <button type="button" onclick="removeImage(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
            imageList.appendChild(imageItem);
        });
    } else {
        imageInfo.classList.add('hidden');
    }
}

// Remove image
function removeImage(index) {
    const dt = new DataTransfer();
    const files = imageInput.files;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    imageInput.files = dt.files;
    handleImageSelect({ target: { files: dt.files } });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-3"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endsection
