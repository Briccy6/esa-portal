@extends('layouts.app')

@section('title', 'Assignments Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assignments Management</h3>
                    <a href="{{ route('dos.assignments.create') }}" class="btn btn-primary float-right">
                        <i class="fas fa-plus"></i> Create Assignment
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Assignment Management</strong> - This section is under development.
                        You will be able to create and manage assignments for students here.
                    </div>
                    
                    <p>Features coming soon:</p>
                    <ul>
                        <li>Create assignments with due dates</li>
                        <li>Attach documents and resources</li>
                        <li>Assign to specific courses and branches</li>
                        <li>Track student submissions</li>
                        <li>Grade and provide feedback</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
