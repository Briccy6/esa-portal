@extends('admin.layout')

@section('title', 'Payments List')

@section('content')
<div class="container mt-4">
    <h1>Payments</h1>

    <a href="{{ route('admin.payments.create') }}" class="btn btn-success mb-3">Add New Payment</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($payments->count() > 0)
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Applies To</th>
                    <th>Required</th>
                    <th>Default Amount</th>
                    <th>Discount (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->name }}</td>
                    <td>{{ ucfirst($payment->applies_to) }}</td>
                    <td>{{ $payment->is_required ? 'Yes' : 'No' }}</td>
                    <td>{{ number_format($payment->default_amount, 2) }}</td>
                    <td>{{ $payment->default_discount ?? 0 }}</td>
                    <td>
                        <a href="{{ route('admin.payments.edit', $payment->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No payment types found.</p>
    @endif
</div>
@endsection
