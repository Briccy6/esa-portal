@extends('students.layout')

@section('content')
<div class="container mt-4">
    {{-- Profile Header --}}
    <div class="profile-header d-flex flex-column align-items-center">
        <div class="d-flex align-items-center justify-content-center mb-2" style="gap: 1rem;">
            @if($student->passport_photo)
                <img src="data:image/jpeg;base64,{{ $student->passport_photo }}" alt="Profile Photo"
                     class="rounded-circle shadow" style="width:70px; height:70px; object-fit:cover; border:3px solid #007bff;">
            @else
                <i class="fas fa-user-edit" style="font-size: 3rem; color: #007bff;"></i>
            @endif
            <h3 class="mb-0 ms-2">Update Profile</h3>
        </div>
        <p class="text-muted">Keep your information up to date</p>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('updated_fields'))
        <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
            Updated fields: {{ implode(', ', session('updated_fields')) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Profile Update Form --}}
    <form id="profileForm" action="{{ route('students.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf

        {{-- Passport Photo --}}
        <div class="mb-3">
            <label class="form-label">Passport Photo (JPG/PNG, max 200KB)</label>
            <input type="file" name="passport_photo" id="passportPhotoInput" class="form-control file-input" accept=".jpg,.jpeg,.png">
            <div class="image-preview-container mt-2">
                <img id="passportPhotoPreview"
                     src="{{ $student->passport_photo ? 'data:image/jpeg;base64,' . $student->passport_photo : '#' }}"
                     alt="Passport Photo Preview"
                     class="preview-image"
                     style="max-width:100px; {{ $student->passport_photo ? '' : 'display:none;' }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control modern-input" value="{{ old('full_name', $student->full_name) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Telephone</label>
            <input type="text" name="telephone" class="form-control modern-input" value="{{ old('telephone', $student->phone) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control modern-input" value="{{ old('email', $student->email) }}">
        </div>

        {{-- Sex --}}
        <div class="mb-3">
            <label class="form-label">Sex</label>
            <select name="sex" class="form-select modern-input">
                <option value="">Select Sex</option>
                <option value="Male" {{ old('sex', $student->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('sex', $student->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('sex', $student->sex) == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Identification Type</label>
            <select name="identification_type" id="identificationTypeSelect" class="form-select modern-input">
                <option value="">Select Type</option>
                <option value="ID" {{ old('identification_type', $student->identification_type) == 'ID' ? 'selected' : '' }}>ID</option>
                <option value="Passport" {{ old('identification_type', $student->identification_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
            </select>
        </div>

        <div class="mb-3" id="idNumberDiv" style="display:none;">
            <label class="form-label" id="idNumberLabel">Identification Number</label>
            <input type="text" name="id_number" id="idNumberInput" class="form-control modern-input" value="{{ old('id_number', $student->id_number) }}">
        </div>

        <div class="mb-3" id="idPhotoDiv" style="display:none;">
            <label class="form-label" id="idPhotoLabel">Identification Image (JPG/PNG, max 200KB)</label>
            <input type="file" name="id_photo" id="idPhotoInput" class="form-control file-input" accept=".jpg,.jpeg,.png">
            <div class="image-preview-container mt-2">
                <img id="idPhotoPreview"
                     src="{{ $student->id_photo ? 'data:image/jpeg;base64,' . $student->id_photo : '#' }}"
                     alt="ID/Passport Photo Preview"
                     class="preview-image"
                     style="max-width:100px; {{ $student->id_photo ? '' : 'display:none;' }}">
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control modern-input" id="passwordInput">
            <input type="password" name="password_confirmation" class="form-control modern-input mt-2" placeholder="Confirm Password" id="passwordConfirmInput">
            <div id="passwordHelp" class="form-text text-danger"></div>
        </div>

        {{-- Education Level --}}
        <div class="mb-3">
            <label class="form-label">Education Level</label>
            <select name="education_level" id="educationLevelSelect" class="form-select modern-input">
                <option value="">Select Level</option>
                <option value="Senior 3" {{ old('education_level', $student->education_level) == 'Senior 3' ? 'selected' : '' }}>Senior 3</option>
                <option value="Senior 6" {{ old('education_level', $student->education_level) == 'Senior 6' ? 'selected' : '' }}>Senior 6</option>
                <option value="Other" {{ old('education_level', $student->education_level) == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        {{-- Document Uploads --}}
        <div class="mb-3" id="senior3DocumentDiv" style="display:none;">
            <label class="form-label">Senior 3 Document (PDF/JPG/PNG, max 200KB)</label>
            <input type="file" name="senior3_document" id="senior3DocumentInput" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png">
            <div class="image-preview-container mt-2">
                <img id="senior3DocumentPreview"
                     src="{{ $student->senior3_document ? 'data:image/jpeg;base64,' . $student->senior3_document : '#' }}"
                     alt="Senior 3 Document Preview"
                     class="preview-image"
                     style="max-width:100px; {{ $student->senior3_document ? '' : 'display:none;' }}">
            </div>
        </div>

        <div class="mb-3" id="senior6DocumentDiv" style="display:none;">
            <label class="form-label">Senior 6 Document (PDF/JPG/PNG, max 200KB)</label>
            <input type="file" name="senior6_document" id="senior6DocumentInput" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png">
            <div class="image-preview-container mt-2">
                <img id="senior6DocumentPreview"
                     src="{{ $student->senior6_document ? 'data:image/jpeg;base64,' . $student->senior6_document : '#' }}"
                     alt="Senior 6 Document Preview"
                     class="preview-image"
                     style="max-width:100px; {{ $student->senior6_document ? '' : 'display:none;' }}">
            </div>
        </div>

        <div class="mb-3" id="otherSupportingDocumentDiv" style="display:none;">
            <label class="form-label">Other Supporting Document (PDF/JPG/PNG, max 200KB)</label>
            <input type="file" name="other_supporting_document" id="otherSupportingDocumentInput" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png">
            <div class="image-preview-container mt-2">
                <img id="otherSupportingDocumentPreview"
                     src="{{ $student->other_supporting_document ? 'data:image/jpeg;base64,' . $student->other_supporting_document : '#' }}"
                     alt="Other Supporting Document Preview"
                     class="preview-image"
                     style="max-width:100px; {{ $student->other_supporting_document ? '' : 'display:none;' }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<style>
.profile-header { margin-bottom: 1.5rem; text-align: center; }
.preview-image { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); max-width: 100px; max-height: 100px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function previewImage(input, previewId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);
        if (file && file.type.match('image.*')) {
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
            reader.readAsDataURL(file);
        } else { preview.src = "#"; preview.style.display = 'none'; }
    }

    function validateFileSize(input) {
        if (input.files[0] && input.files[0].size > 204800) {
            alert("File size must be 200KB or less.");
            input.value = "";
            return false;
        }
        return true;
    }

    function validatePasswordStrength(password) {
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        return strongRegex.test(password);
    }

    function showDocumentInput() {
        const level = document.getElementById('educationLevelSelect').value;
        document.getElementById('senior3DocumentDiv').style.display = (level === 'Senior 3') ? 'block' : 'none';
        document.getElementById('senior6DocumentDiv').style.display = (level === 'Senior 6') ? 'block' : 'none';
        document.getElementById('otherSupportingDocumentDiv').style.display = (level === 'Other') ? 'block' : 'none';
    }
    document.getElementById('educationLevelSelect').addEventListener('change', showDocumentInput);
    showDocumentInput();

    function showIdentificationInputs() {
        const type = document.getElementById('identificationTypeSelect').value;
        const idNumberDiv = document.getElementById('idNumberDiv');
        const idPhotoDiv = document.getElementById('idPhotoDiv');
        const idNumberLabel = document.getElementById('idNumberLabel');
        const idPhotoLabel = document.getElementById('idPhotoLabel');
        if (type === 'ID') {
            idNumberDiv.style.display = 'block'; idPhotoDiv.style.display = 'block';
            idNumberLabel.textContent = 'ID Number'; idPhotoLabel.textContent = 'ID Image (JPG/PNG, max 200KB)';
        } else if (type === 'Passport') {
            idNumberDiv.style.display = 'block'; idPhotoDiv.style.display = 'block';
            idNumberLabel.textContent = 'Passport Number'; idPhotoLabel.textContent = 'Passport Image (JPG/PNG, max 200KB)';
        } else { idNumberDiv.style.display = 'none'; idPhotoDiv.style.display = 'none'; }
    }
    document.getElementById('identificationTypeSelect').addEventListener('change', showIdentificationInputs);
    showIdentificationInputs();

    // File inputs
    document.getElementById('passportPhotoInput').addEventListener('change', function() { if (validateFileSize(this)) previewImage(this, 'passportPhotoPreview'); });
    document.getElementById('idPhotoInput').addEventListener('change', function() { if (validateFileSize(this)) previewImage(this, 'idPhotoPreview'); });
    document.getElementById('senior3DocumentInput').addEventListener('change', function() { if (validateFileSize(this)) previewImage(this, 'senior3DocumentPreview'); });
    document.getElementById('senior6DocumentInput').addEventListener('change', function() { if (validateFileSize(this)) previewImage(this, 'senior6DocumentPreview'); });
    document.getElementById('otherSupportingDocumentInput').addEventListener('change', function() { if (validateFileSize(this)) previewImage(this, 'otherSupportingDocumentPreview'); });

    // Password validation
    document.getElementById('passwordInput').addEventListener('input', function() {
        const password = this.value, help = document.getElementById('passwordHelp');
        if (password && !validatePasswordStrength(password)) {
            help.textContent = "Password must be 8+ chars, include upper, lower, number & special char.";
        } else { help.textContent = ""; }
    });
    document.getElementById('passwordConfirmInput').addEventListener('input', function() {
        const password = document.getElementById('passwordInput').value, confirm = this.value, help = document.getElementById('passwordHelp');
        if (password && confirm && password !== confirm) {
            help.textContent = "Passwords do not match.";
        } else if (password && !validatePasswordStrength(password)) {
            help.textContent = "Password must be 8+ chars, include upper, lower, number & special char.";
        } else { help.textContent = ""; }
    });

    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const password = document.getElementById('passwordInput').value, confirm = document.getElementById('passwordConfirmInput').value, help = document.getElementById('passwordHelp');
        if (password && !validatePasswordStrength(password)) { help.textContent = "Weak password."; e.preventDefault(); }
        if (password && password !== confirm) { help.textContent = "Passwords do not match."; e.preventDefault(); }
    });
});
</script>
@endsection
