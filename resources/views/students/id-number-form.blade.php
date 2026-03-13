@extends('students.layout')

@section('title', 'Enter Identification Details')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        @if($identificationType === 'ID')
                            Enter National ID Details
                        @else
                            Enter Passport Details
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('students.index') }}">
                        <div class="mb-3">
                            <label for="id_number" class="form-label">
                                @if($identificationType === 'ID')
                                    National ID Number
                                @else
                                    Passport Number
                                @endif
                            </label>
                            <input type="text" class="form-control" id="id_number" name="id_number" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="id_photo" class="form-label">
                                @if($identificationType === 'ID')
                                    ID Card Photo
                                @else
                                    Passport Photo
                                @endif
                            </label>
                            <input type="file" class="form-control" id="id_photo" name="id_photo" accept="image/*">
                        </div>
                        
                        @if($identificationType === 'ID')
                        <div class="mb-3">
                            <label for="passport_photo" class="form-label">Passport Size Photo</label>
                            <input type="file" class="form-control" id="passport_photo" name="passport_photo" accept="image/*">
                        </div>
                        @endif
                        
                        <input type="hidden" name="identification_type" value="{{ $identificationType }}">
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Continue</button>
                            <a href="{{ route('students.login') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
