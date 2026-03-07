@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">School Information</h3>
                    <div class="card-tools">
                        <small class="text-muted">Configure school details for report cards</small>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.school-information.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="school_name">School Name *</label>
                                    <input type="text" name="school_name" id="school_name" 
                                           class="form-control @error('school_name') is-invalid @enderror" 
                                           value="{{ old('school_name', $schoolInfo->school_name) }}" required>
                                    @error('school_name')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" name="phone" id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $schoolInfo->phone) }}">
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address *</label>
                                    <textarea name="address" id="address" rows="3" 
                                              class="form-control @error('address') is-invalid @enderror" 
                                              required>{{ old('address', $schoolInfo->address) }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $schoolInfo->email) }}">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="website">Website</label>
                                    <input type="url" name="website" id="website" 
                                           class="form-control @error('website') is-invalid @enderror" 
                                           value="{{ old('website', $schoolInfo->website) }}"
                                           placeholder="https://www.example.com">
                                    @error('website')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="motto">School Motto</label>
                                    <input type="text" name="motto" id="motto" 
                                           class="form-control @error('motto') is-invalid @enderror" 
                                           value="{{ old('motto', $schoolInfo->motto) }}"
                                           placeholder="e.g., Excellence in Education">
                                    @error('motto')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vision">Vision Statement</label>
                                    <textarea name="vision" id="vision" rows="3" 
                                              class="form-control @error('vision') is-invalid @enderror">{{ old('vision', $schoolInfo->vision) }}</textarea>
                                    @error('vision')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mission">Mission Statement</label>
                                    <textarea name="mission" id="mission" rows="3" 
                                              class="form-control @error('mission') is-invalid @enderror">{{ old('mission', $schoolInfo->mission) }}</textarea>
                                    @error('mission')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="principal_name">Principal Name</label>
                                    <input type="text" name="principal_name" id="principal_name" 
                                           class="form-control @error('principal_name') is-invalid @enderror" 
                                           value="{{ old('principal_name', $schoolInfo->principal_name) }}">
                                    @error('principal_name')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logo">School Logo</label>
                                    <input type="file" name="logo" id="logo" 
                                           class="form-control @error('logo') is-invalid @enderror" 
                                           accept="image/*">
                                    @error('logo')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                    @if($schoolInfo->logo_url)
                                        <small class="text-muted">
                                            Current logo: 
                                            <img src="{{ $schoolInfo->logo_url }}" alt="Logo" style="max-height: 30px; max-width: 100px;">
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="principal_signature">Principal Signature</label>
                                    <input type="file" name="principal_signature" id="principal_signature" 
                                           class="form-control @error('principal_signature') is-invalid @enderror" 
                                           accept="image/*">
                                    @error('principal_signature')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                    @if($schoolInfo->principal_signature_url)
                                        <small class="text-muted">
                                            Current signature: 
                                            <img src="{{ $schoolInfo->principal_signature_url }}" alt="Signature" style="max-height: 30px;">
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stamp_signature">School Stamp</label>
                                    <input type="file" name="stamp_signature" id="stamp_signature" 
                                           class="form-control @error('stamp_signature') is-invalid @enderror" 
                                           accept="image/*">
                                    @error('stamp_signature')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                    @if($schoolInfo->stamp_signature_url)
                                        <small class="text-muted">
                                            Current stamp: 
                                            <img src="{{ $schoolInfo->stamp_signature_url }}" alt="Stamp" style="max-height: 30px;">
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save School Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
