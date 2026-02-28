@extends('students.app')

@section('title', 'Student Login')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Student Login</h2>
    <form action="{{ route('student.login.idTypeSelected') }}" method="POST">
        @csrf
        <div class="form-check">
            <input class="form-check-input" type="radio" name="identification_type" id="passport" value="passport" required>
            <label class="form-check-label" for="passport">
                Passport
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="identification_type" id="id_number" value="id_number" required>
            <label class="form-check-label" for="id_number">
                National ID
            </label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Continue</button>
    </form>
</div>
@endsection