@extends('dos.layout')

@section('content')
    @include('staff.timetable.partials.form', ['editing' => false])
@endsection
