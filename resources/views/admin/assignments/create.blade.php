@extends('layouts.app')

@section('title', 'Create Assignment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Assignment</h3>
                    <a href="{{ route('dos.assignments.index') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Back to Assignments
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Assignment Creation</strong> - This feature is under development.
                        You will be able to create assignments with various options here.
                    </div>
                    
                    <p>Features coming soon:</p>
                    <ul>
                        <li>Assignment title and description</li>
                        <li>Due date and time settings</li>
                        <li>File attachments</li>
                        <li>Course and branch assignment</li>
                        <li>Grading criteria</li>
                        <li>Submission guidelines</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
