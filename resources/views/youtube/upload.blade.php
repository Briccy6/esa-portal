@extends('admin.layout')

@section('content')
<style>
    .upload-container {
        max-width: 600px;
        margin: 2rem auto;
        padding: 2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .upload-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        z-index: 1;
    }

    .upload-content {
        position: relative;
        z-index: 2;
    }

    .upload-title {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 2rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .upload-title::before {
        content: '🎬';
        display: block;
        font-size: 3rem;
        margin-bottom: 0.5rem;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }

    .success-alert {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: none;
        box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .upload-form {
        background: rgba(255, 255, 255, 0.95);
        padding: 2rem;
        border-radius: 16px;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .form-control {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
        background: white;
    }

    .form-control:hover {
        border-color: #cbd5e0;
        transform: translateY(-1px);
    }

    /* File input styling */
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        background: linear-gradient(45deg, #f7fafc 0%, #edf2f7 100%);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-input-wrapper:hover {
        border-color: #667eea;
        background: linear-gradient(45deg, #edf2f7 0%, #e2e8f0 100%);
        transform: translateY(-2px);
    }

    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: -9999px;
    }

    .file-input-text {
        color: #4a5568;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .file-input-text::before {
        content: '📹';
        display: block;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }

    .upload-btn {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
        padding: 1rem 2.5rem;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .upload-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: all 0.3s ease;
    }

    .upload-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
    }

    .upload-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .upload-btn:active {
        transform: translateY(-1px);
    }

    .upload-btn span {
        position: relative;
        z-index: 1;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .upload-container {
            margin: 1rem;
            padding: 1.5rem;
        }
        
        .upload-title {
            font-size: 2rem;
        }
        
        .upload-form {
            padding: 1.5rem;
        }
    }

    /* Loading animation for form submission */
    .upload-btn.loading {
        pointer-events: none;
        opacity: 0.8;
    }

    .upload-btn.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="upload-container">
    <div class="upload-content">
        <h2 class="upload-title">Upload to YouTube</h2>
        
        @if(session('success'))
            <div class="success-alert">
                ✅ {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('youtube.upload') }}" method="POST" enctype="multipart/form-data" class="upload-form" id="uploadForm">
            @csrf
            
            <div class="form-group">
                <label for="video" class="form-label">📹 Video File</label>
                <div class="file-input-wrapper" onclick="document.getElementById('video').click()">
                    <input type="file" name="video" id="video" accept="video/*" required>
                    <div class="file-input-text" id="fileText">
                        Click to select your video file
                        <br><small style="color: #718096;">Supported formats: MP4, AVI, MOV, WMV</small>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="title" class="form-label">📝 Video Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Enter an engaging title for your video" required>
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">📄 Description</label>
                <textarea name="description" id="description" class="form-control" placeholder="Describe your video content, add tags, and include relevant information..."></textarea>
            </div>
            
            <button type="submit" class="upload-btn" id="uploadButton">
                <span>🚀 Upload to YouTube</span>
            </button>
        </form>
    </div>
</div>

<script>
    // File input handling
    document.getElementById('video').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const fileText = document.getElementById('fileText');
        if (fileName) {
            fileText.innerHTML = `
                <strong>Selected:</strong> ${fileName}
                <br><small style="color: #38a169;">Ready to upload! ✓</small>
            `;
        }
    });

    // Form submission handling
    document.getElementById('uploadForm').addEventListener('submit', function() {
        const button = document.getElementById('uploadButton');
        button.classList.add('loading');
        button.innerHTML = '<span>Uploading...</span>';
    });

    // Add smooth focus transitions
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.querySelector('.form-label').style.color = '#667eea';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.querySelector('.form-label').style.color = '#2d3748';
        });
    });
</script>
@endsection