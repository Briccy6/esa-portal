@extends('admin.layout')

@section('title', 'Edit NESA Registration - ' . $student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit mr-2"></i>
                        Edit NESA Registration - {{ $student->full_name }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Student Information -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle mr-2"></i>Student Information</h6>
                                <div class="row">
                                    <div class="col-md-3"><strong>Reg Number:</strong> {{ $student->reg_number }}</div>
                                    <div class="col-md-3"><strong>Email:</strong> {{ $student->email }}</div>
                                    <div class="col-md-3"><strong>Phone:</strong> {{ $student->phone ?? 'N/A' }}</div>
                                    <div class="col-md-3"><strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}</div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3"><strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}</div>
                                    <div class="col-md-3"><strong>Academic Year:</strong> {{ $student->academicYear->year ?? 'N/A' }}</div>
                                    <div class="col-md-3"><strong>Current Status:</strong> 
                                        @if($student->nesa_registered)
                                            <span class="badge badge-success">Registered</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NESA Registration Form -->
                    <form method="POST" action="{{ route('admin.nesa.update', $student) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nesa_registered" class="form-label">
                                        <i class="fas fa-check-circle mr-1"></i>NESA Registered
                                    </label>
                                    <div class="form-check">
                                        <input type="checkbox" name="nesa_registered" id="nesa_registered" class="form-check-input" value="1" {{ $student->nesa_registered ? 'checked' : '' }}>
                                        <label class="form-check-label" for="nesa_registered">
                                            Mark student as registered for NESA examination
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="nesa_sdms_code" class="form-label">
                                        <i class="fas fa-barcode mr-1"></i>NESA SDMS Code
                                    </label>
                                    <input type="text" name="nesa_sdms_code" id="nesa_sdms_code" class="form-control" 
                                           value="{{ old('nesa_sdms_code', $student->nesa_sdms_code) }}" required>
                                    <small class="form-text text-muted">Enter the SDMS code provided by NESA</small>
                                </div>

                                <div class="form-group">
                                    <label for="nesa_reg_number" class="form-label">
                                        <i class="fas fa-hashtag mr-1"></i>NESA Registration Number
                                    </label>
                                    <input type="text" name="nesa_reg_number" id="nesa_reg_number" class="form-control" 
                                           value="{{ old('nesa_reg_number', $student->nesa_reg_number) }}" required>
                                    <small class="form-text text-muted">Enter the examination registration number</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nesa_document" class="form-label">
                                        <i class="fas fa-file-upload mr-1"></i>NESA Document
                                    </label>
                                    <input type="file" name="nesa_document" id="nesa_document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload document downloaded from NESA website (PDF, DOC, DOCX, JPG, JPEG, PNG - Max 5MB)</small>
                                    
                                    @if($student->nesa_document)
                                        <div class="mt-2">
                                            <small class="text-info">Current document: 
                                                <a href="{{ route('admin.nesa.download', $student) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-download mr-1"></i>Download
                                                </a>
                                            </small>
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="nesa_registration_date" class="form-label">
                                        <i class="fas fa-calendar mr-1"></i>Registration Date
                                    </label>
                                    <input type="date" name="nesa_registration_date" id="nesa_registration_date" class="form-control" 
                                           value="{{ old('nesa_registration_date', $student->nesa_registration_date ? $student->nesa_registration_date->format('Y-m-d') : '') }}">
                                    <small class="form-text text-muted">Date when student was registered with NESA</small>
                                </div>

                                <div class="form-group">
                                    <label for="nesa_remarks" class="form-label">
                                        <i class="fas fa-comment mr-1"></i>Remarks
                                    </label>
                                    <textarea name="nesa_remarks" id="nesa_remarks" class="form-control" rows="3">{{ old('nesa_remarks', $student->nesa_remarks) }}</textarea>
                                    <small class="form-text text-muted">Additional notes about the NESA registration</small>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="send_email" id="send_email" class="form-check-input" value="1" checked>
                                        <label class="form-check-label" for="send_email">
                                            <i class="fas fa-envelope mr-1"></i>Send email notification to student
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Student will receive an email about their NESA registration status update</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Update NESA Registration
                                    </button>
                                    <a href="{{ route('admin.nesa.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                                    </a>
                                    
                                    @if(!$student->nesa_registered)
                                        <button type="button" class="btn btn-success ml-2" data-toggle="modal" data-target="#quickRegisterModal">
                                            <i class="fas fa-rocket mr-2"></i>Quick Register
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Register Modal -->
@if(!$student->nesa_registered)
<div class="modal fade" id="quickRegisterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-rocket mr-2"></i>Quick NESA Registration
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.nesa.markAsRegistered', $student) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        This will immediately mark {{ $student->full_name }} as registered for NESA examination.
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_nesa_sdms_code" class="form-label">NESA SDMS Code *</label>
                        <input type="text" name="nesa_sdms_code" id="quick_nesa_sdms_code" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_nesa_reg_number" class="form-label">NESA Registration Number *</label>
                        <input type="text" name="nesa_reg_number" id="quick_nesa_reg_number" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_nesa_document" class="form-label">NESA Document</label>
                        <input type="file" name="nesa_document" id="quick_nesa_document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_nesa_remarks" class="form-label">Remarks</label>
                        <textarea name="nesa_remarks" id="quick_nesa_remarks" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="send_email" id="quick_send_email" class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="quick_send_email">
                                <i class="fas fa-envelope mr-1"></i>Send email notification to student
                            </label>
                        </div>
                        <small class="form-text text-info">Student will receive an email confirmation of their NESA registration</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-2"></i>Register Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
