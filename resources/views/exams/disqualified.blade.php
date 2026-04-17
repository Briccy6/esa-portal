@extends('students.app')

@section('content')
<div class="container py-5 text-center">
    <h2 class="text-danger mb-4">Disqualified</h2>
    <p>You have been disqualified from this exam due to repeated cheating attempts. Your score is zero.</p>
    <a href="{{ route('student.dashboard') }}" class="btn btn-primary mt-3">Return to Dashboard</a>
</div>
@endsection