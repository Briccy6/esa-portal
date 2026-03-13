@extends('admin.layout')

@section('title', 'Dashboard Error')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-left-danger">
                <div class="card-body">
                    <h4 class="card-title">Dashboard Error</h4>
                    <div class="alert alert-danger">
                        <h5>Error Message:</h5>
                        <p>{{ $error_message }}</p>
                    </div>
                    
                    @if(isset($query_log) && !empty($query_log))
                    <div class="alert alert-warning mt-3">
                        <h5>Last Query Executed:</h5>
                        @foreach($query_log as $query)
                        <div class="mb-2">
                            <strong>Query:</strong> {{ $query['query'] }}<br>
                            @if(!empty($query['bindings']))
                                <strong>Bindings:</strong> {{ json_encode($query['bindings']) }}<br>
                            @endif
                            <strong>Time:</strong> {{ $query['time'] }}ms
                        </div>
                        @endforeach
                    </div>
                    @endif
                    
                    @if(isset($trace))
                    <div class="alert alert-info mt-3">
                        <h5>Stack Trace:</h5>
                        <pre style="max-height: 300px; overflow-y: auto;">{{ $trace }}</pre>
                    </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Retry</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
