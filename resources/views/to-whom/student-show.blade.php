@extends('students.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Letter Preview</h1>
                <div>
                    <a href="{{ route('to-whom.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Letters
                    </a>
                    <a href="{{ route('to-whom.download', $letter) }}" class="btn btn-success ms-2">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                    @if($canDelete)
                        <button type="button" class="btn btn-danger ms-2" onclick="deleteLetterConfirm()">
                            <i class="fas fa-trash"></i> Delete Letter
                        </button>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="letter-preview" style="padding: 40px; background: white; min-height: 800px;">
                        <!-- School Header -->
                        <div class="text-center mb-5">
                            @if($schoolInfo && $schoolInfo->logo_url)
                                <img src="{{ $schoolInfo->logo_url }}" alt="{{ $schoolInfo->school_name }}" 
                                     style="max-height: 80px; margin-bottom: 20px;">
                            @endif
                            <h2 style="margin: 0; color: #1e3a8a; font-weight: bold;">{{ $schoolInfo->school_name ?? 'ESA Learning Portal' }}</h2>
                            <p style="margin: 5px 0; color: #6b7280;">{{ $schoolInfo->address ?? '123 Education Street, Learning City, Knowledge State' }}</p>
                            <p style="margin: 5px 0; color: #6b7280;">
                                Phone: {{ $schoolInfo->phone ?? '+250-788-123-456' }} | 
                                Email: {{ $schoolInfo->email ?? 'info@esa-learning.rw' }}
                            </p>
                            @if($schoolInfo->website)
                                <p style="margin: 5px 0; color: #6b7280;">Website: {{ $schoolInfo->website }}</p>
                            @endif
                        </div>

                        <!-- Letter Header -->
                        <div class="mb-4">
                            <div class="text-end mb-3">
                                <p style="margin: 0; font-weight: bold;">Reference: {{ $letter->reference_number }}</p>
                                <p style="margin: 0;">Date: {{ $letter->formatted_letter_date }}</p>
                            </div>
                            
                            <p style="margin: 0 0 20px 0; font-weight: bold;">{{ $letter->recipient_title }}</p>
                        </div>

                        <!-- Letter Content -->
                        <div style="line-height: 1.6; margin-bottom: 30px;">
                            <p style="margin: 0 0 20px 0;">Dear Sir/Madam,</p>
                            
                            <div style="white-space: pre-line; margin: 20px 0;">
                                {{ $letter->content }}
                            </div>

                            <p style="margin: 30px 0 20px 0;">This letter is issued for the purpose of: <strong>{{ $letter->purpose }}</strong></p>

                            <p style="margin: 20px 0;">Should you require any further information or clarification, please do not hesitate to contact our administration office.</p>

                            <p style="margin: 20px 0;">Yours faithfully,</p>
                        </div>

                        <!-- Signature Section -->
                        <div class="mt-5" style="margin-top: 60px;">
                            <div class="row">
                                <div class="col-md-6">
                                    @if($schoolInfo && $schoolInfo->principal_signature_url)
                                        <div style="margin-bottom: 10px;">
                                            <img src="{{ $schoolInfo->principal_signature_url }}" alt="Principal Signature" 
                                                 style="max-height: 60px;">
                                        </div>
                                    @else
                                        <div style="height: 60px; border-bottom: 1px solid #000; margin-bottom: 10px;"></div>
                                    @endif
                                    <p style="margin: 0; font-weight: bold;">{{ $schoolInfo->principal_name ?? 'Principal' }}</p>
                                    <p style="margin: 0;">{{ $schoolInfo->school_name ?? 'ESA Learning Portal' }}</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    @if($schoolInfo && $schoolInfo->stamp_signature_url)
                                        <img src="{{ $schoolInfo->stamp_signature_url }}" alt="Official Stamp" 
                                             style="max-height: 80px; opacity: 0.8;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-5 pt-4 border-top" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin: 5px 0; font-size: 12px; color: #6b7280;">
                                        <strong>{{ $schoolInfo->motto ?? 'Excellence in Education Through Technology' }}</strong>
                                    </p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p style="margin: 5px 0; font-size: 12px; color: #6b7280;">
                                        Generated on: {{ now()->format('F j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Letter Information -->
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">Letter Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Reference Number:</strong></td>
                                    <td>{{ $letter->reference_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Student:</strong></td>
                                    <td>{{ $letter->student->full_name }} ({{ $letter->student->reg_number }})</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $letter->student->course->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Letter Type:</strong></td>
                                    <td>{{ \App\Models\ToWhomLetter::getLetterTypes()[$letter->letter_type] ?? $letter->letter_type }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Recipient:</strong></td>
                                    <td>{{ $letter->recipient_title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Purpose:</strong></td>
                                    <td>{{ $letter->purpose }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Letter Date:</strong></td>
                                    <td>{{ $letter->formatted_letter_date }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Generated By:</strong></td>
                                    <td>{{ $letter->generator ? $letter->generator->name : 'System' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteLetterConfirm() {
    if (confirm('Are you sure you want to delete this letter? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('to-whom.destroy', $letter) }}';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(tokenInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

@push('styles')
<style>
    .letter-preview {
        font-family: 'Times New Roman', serif;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    @media print {
        .container-fluid {
            padding: 0;
        }
        
        .card {
            border: none;
            box-shadow: none;
        }
        
        .d-flex {
            display: none !important;
        }
        
        .card:last-child {
            display: none !important;
        }
        
        .letter-preview {
            border: none;
            box-shadow: none;
            padding: 20px;
        }
        
        body {
            background: white;
        }
    }
</style>
@endpush
