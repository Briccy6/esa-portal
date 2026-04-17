@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Attendance Management</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Manage student attendance records and marking.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-check fa-3x text-primary mb-3"></i>
                                    <h5>Mark Attendance</h5>
                                    <p class="text-muted">Mark attendance for students by course and lesson</p>
                                    <a href="{{ route('attendance.mark.form') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Mark Attendance
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
                                    <h5>Attendance Reports</h5>
                                    <p class="text-muted">View attendance statistics and reports</p>
                                    <button class="btn btn-success" disabled>
                                        <i class="fas fa-chart-line"></i> View Reports
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Quick Stats</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4 class="text-primary">-</h4>
                                        <small>Total Students</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4 class="text-success">-</h4>
                                        <small>Present Today</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4 class="text-warning">-</h4>
                                        <small>Late Today</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4 class="text-danger">-</h4>
                                        <small>Absent Today</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
