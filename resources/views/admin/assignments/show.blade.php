@extends('layouts.app')

@section('title', 'View Assignment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assignment Details</h3>
                    <div class="float-right">
                        <a href="{{ route('dos.assignments.edit', $assignment->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('dos.assignments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Assignment View</strong> - This feature is under development.
                        Assignment details and student submissions will be shown here.
                    </div>
                    
                    <p>Assignment ID: {{ $assignment->id }}</p>
                    <p>Features coming soon:</p>
                    <ul>
                        <li>Assignment details and requirements</li>
                        <li>Attached files and resources</li>
                        <li>Student submission list</li>
                        <li>Grading interface</li>
                        <li>Submission statistics</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
