@extends('secretary.layout')

@section('title', 'Secretary Dashboard')

@section('content')
<div class="container-fluid">
@include('partials.academic-year-banner')

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$totalStudents ?? 0}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Tokens</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format($totalTokens ?? 0)}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Registrations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$pendingRegistrations ?? 0}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Balance (<5)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$lowBalanceStudents ?? 0}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Student Registration</h5>
                        <p class="card-text text-muted">Register new students</p>
                        <a href="{{route('students.create')}}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Register Student
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-coins fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Token Management</h5>
                        <p class="card-text text-muted">Manage student tokens</p>
                        <a href="{{route('secretary.tokens.index')}}" class="btn btn-success btn-sm">
                            <i class="fas fa-cog"></i> Manage Tokens
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-money-bill-wave fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Fee Collection</h5>
                        <p class="card-text text-muted">Process student fees</p>
                        <a href="#" class="btn btn-info btn-sm">
                            <i class="fas fa-hand-holding-usd"></i> Collect Fees
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Reports</h5>
                        <p class="card-text text-muted">View reports</p>
                        <a href="#" class="btn btn-warning btn-sm">
                            <i class="fas fa-chart-line"></i> View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mb-4">
        <!-- Recent Students -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Students</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Registration</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStudents ?? [] as $student)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-gray-800">{{$student->full_name}}</div>
                                        <div class="text-gray-500 small">{{$student->email}}</div>
                                    </td>
                                    <td>{{$student->reg_number}}</td>
                                    <td>{{\Carbon\Carbon::parse($student->created_at)->format('M d, Y')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Token Transactions -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Recent Token Transactions</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions ?? [] as $transaction)
                                <tr>
                                    <td>{{$transaction->student->full_name}}</td>
                                    <td>{{$transaction->description}}</td>
                                    <td>
                                        @if($transaction->type == 'add')
                                        <span class="text-success font-weight-bold">+{{$transaction->amount}}</span>
                                        @else
                                        <span class="text-danger font-weight-bold">-{{$transaction->amount}}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Needing Attention -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">Students Needing Attention</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Registration</th>
                                    <th>Course</th>
                                    <th>Token Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentsNeedingAttention ?? [] as $student)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-gray-800">{{$student->full_name}}</div>
                                        <div class="text-gray-500 small">{{$student->email}}</div>
                                    </td>
                                    <td>{{$student->reg_number}}</td>
                                    <td>{{$student->course->course_name ?? 'N/A'}}</td>
                                    <td>
                                        <div class="font-weight-bold">{{$student->token->balance ?? 0}} tokens</div>
                                    </td>
                                    <td>
                                        @if($student->token && $student->token->balance < 5)
                                        <span class="badge badge-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Low Balance
                                        </span>
                                        @elseif(!$student->nesa_registered)
                                        <span class="badge badge-danger">
                                            <i class="fas fa-clock"></i> NESA Pending
                                        </span>
                                        @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> OK
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{route('secretary.tokens.index')}}" class="btn btn-primary">
                                                <i class="fas fa-coins"></i> Tokens
                                            </a>
                                            <a href="#" class="btn btn-info">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
