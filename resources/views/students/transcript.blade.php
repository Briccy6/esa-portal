
@extends('staff.partials.sidebar_sec')

@section('content')
    @include('students.partials.transcript_table', ['student' => $student, 'payments' => $payments])
@endsection
