@extends(auth()->guard('admin')->check() ? 'admin.layout' : 'secretary.layout')

@section('title', 'Token Management')

@section('content')
@include('includes.dynamic-token-routes')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Token Management</h1>
        <a href="{{$tokenHistoryBaseRoute}}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-history fa-sm text-white-50"></i> View All Transactions
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$students->count()}}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format($students->where('token', '!=', null)->sum('token.balance'))}}</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">With Tokens</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$students->where('token', '!=', null)->count()}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format($students->where('token.balance', '<', 5)->count())}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Students</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Registration</th>
                            <th>Token Balance</th>
                            <th>Total Earned</th>
                            <th>Total Spent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-gray-800">{{$student->full_name}}</div>
                                <div class="text-gray-500 small">{{$student->email}}</div>
                            </td>
                            <td>
                                <div>{{$student->reg_number}}</div>
                                <div class="text-gray-500 small">{{$student->course->course_name ?? 'N/A'}}</div>
                            </td>
                            <td>
                                @if($student->token)
                                <div class="font-weight-bold text-gray-800">{{$student->token->balance}} tokens</div>
                                @else
                                <span class="text-gray-500">No account</span>
                                @endif
                            </td>
                            <td>{{$student->token->total_earned ?? 0}}</td>
                            <td>{{$student->token->total_spent ?? 0}}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button onclick="openAddTokenModal({{$student->id}}, '{{$student->full_name}}')" 
                                            class="btn btn-success">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                    <button onclick="openDeductTokenModal({{$student->id}}, '{{$student->full_name}}', {{$student->token->balance ?? 0}})" 
                                            class="btn btn-danger">
                                        <i class="fas fa-minus"></i> Deduct
                                    </button>
                                    <a href="{{str_replace(':studentId', $student->id, $tokenHistoryRoute)}}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-history"></i> History
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between mt-3">
                <div class="text-muted">
                    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} results
                </div>
                <div>
                    {!! $students->links() !!}
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Add Tokens Modal -->
<div class="modal fade" id="addTokenModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tokens</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{$tokenAddRoute}}" method="POST" id="addTokenForm">
                @csrf
                <input type="hidden" name="student_id" id="addStudentId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addStudentName" class="font-weight-bold">Student</label>
                        <input type="text" id="addStudentName" readonly 
                               class="form-control bg-light">
                    </div>

                    <div class="form-group">
                        <label for="amount" class="font-weight-bold">Amount</label>
                        <input type="number" name="amount" min="1" max="1000" required
                               class="form-control" placeholder="Enter amount">
                    </div>

                    <div class="form-group">
                        <label for="description" class="font-weight-bold">Description</label>
                        <input type="text" name="description" required
                               class="form-control" placeholder="Reason for adding tokens">
                    </div>

                    <div class="form-group">
                        <label for="source" class="font-weight-bold">Source</label>
                        <select name="source" required class="form-control">
                            <option value="system">System Grant</option>
                            <option value="bonus">Bonus</option>
                            <option value="compensation">Compensation</option>
                            <option value="refund">Refund</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Tokens
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deduct Tokens Modal -->
<div class="modal fade" id="deductTokenModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deduct Tokens</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{$tokenDeductRoute}}" method="POST" id="deductTokenForm">
                @csrf
                <input type="hidden" name="student_id" id="deductStudentId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="deductStudentName" class="font-weight-bold">Student</label>
                        <input type="text" id="deductStudentName" readonly 
                               class="form-control bg-light">
                    </div>

                    <div class="form-group">
                        <label for="currentBalance" class="font-weight-bold">Current Balance</label>
                        <input type="text" id="currentBalance" readonly 
                               class="form-control bg-light">
                    </div>

                    <div class="form-group">
                        <label for="deductAmount" class="font-weight-bold">Amount to Deduct</label>
                        <input type="number" name="amount" min="1" required
                               class="form-control" placeholder="Enter amount to deduct">
                    </div>

                    <div class="form-group">
                        <label for="deductDescription" class="font-weight-bold">Description</label>
                        <input type="text" name="description" required
                               class="form-control" placeholder="Reason for deduction">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-minus"></i> Deduct Tokens
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Get the base routes from the include
const addTokenRoute = "{{$tokenAddRoute}}";
const deductTokenRoute = "{{$tokenDeductRoute}}";

function openAddTokenModal(studentId, studentName) {
    document.getElementById('addStudentId').value = studentId;
    document.getElementById('addStudentName').value = studentName;
    document.getElementById('addTokenForm').action = addTokenRoute.replace(':studentId', studentId);
    $('#addTokenModal').modal('show');
}

function openDeductTokenModal(studentId, studentName, currentBalance) {
    document.getElementById('deductStudentId').value = studentId;
    document.getElementById('deductStudentName').value = studentName;
    document.getElementById('currentBalance').value = currentBalance + ' tokens';
    document.getElementById('deductTokenForm').action = deductTokenRoute.replace(':studentId', studentId);
    $('#deductTokenModal').modal('show');
}

// Close modals when hidden
$('#addTokenModal').on('hidden.bs.modal', function () {
    document.getElementById('addTokenForm').reset();
});

$('#deductTokenModal').on('hidden.bs.modal', function () {
    document.getElementById('deductTokenForm').reset();
});
</script>
@endsection
