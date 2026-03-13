@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Token Management</h5>
                </div>
                <div class="card-body">
                    @if($students->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No students found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Registration Number</th>
                                        <th>Current Balance</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            <td>{{ $student->full_name }}</td>
                                            <td>{{ $student->reg_number }}</td>
                                            <td>
                                                <span class="badge badge-{{ $student->token_balance > 0 ? 'success' : 'danger' }}">
                                                    {{ $student->token_balance ?? 0 }} tokens
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ str_replace(':studentId', $student->id, $tokenHistoryRoute) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-history"></i> History
                                                    </a>
                                                    <a href="{{ str_replace(':studentId', $student->id, $tokenAddRoute) }}" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-plus"></i> Add
                                                    </a>
                                                    <a href="{{ str_replace(':studentId', $student->id, $tokenDeductRoute) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-minus"></i> Deduct
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
