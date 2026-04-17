@extends('admin.layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-recycle me-2"></i>Recalculate Report Card Positions
                    </h4>
                </div>
                <div class="card-body p-5">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>About this tool:</strong><br>
                        This will recalculate student rankings for all report cards based on the corrected mark calculation formula. 
                        Both test and exam marks will be normalized to percentages before calculating positions.
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> This operation may take a moment depending on the number of report cards in your system.
                    </div>

                    <button type="button" class="btn btn-lg btn-success w-100" id="recalculateBtn">
                        <i class="fas fa-play-circle me-2"></i>Start Recalculation
                    </button>

                    <div id="progressContainer" style="display: none;" class="mt-4">
                        <div class="progress mb-3">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 50%"></div>
                        </div>
                        <div id="resultContainer" class="alert alert-info">
                            <i class="fas fa-spinner fa-spin me-2"></i>Processing...
                        </div>
                    </div>

                    <div id="successContainer" style="display: none;" class="mt-4">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>✅ Recalculation Complete!</strong>
                            <ul class="mb-0 mt-3" id="resultsList"></ul>
                        </div>
                        <a href="{{ route('admin.report-cards.index') }}" class="btn btn-primary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Back to Report Cards
                        </a>
                    </div>

                    <div id="errorContainer" style="display: none;" class="mt-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>❌ Error:</strong>
                            <p id="errorMessage" class="mb-0 mt-2"></p>
                        </div>
                        <button type="button" class="btn btn-danger w-100" id="retryBtn">
                            <i class="fas fa-redo me-2"></i>Try Again
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('recalculateBtn').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    
    document.getElementById('progressContainer').style.display = 'block';
    document.getElementById('successContainer').style.display = 'none';
    document.getElementById('errorContainer').style.display = 'none';

    fetch('{{ route("admin.recalculate-positions.execute") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('progressContainer').style.display = 'none';
        
        if (data.success) {
            document.getElementById('resultsList').innerHTML = data.results
                .map(r => `<li>${r}</li>`)
                .join('');
            
            document.getElementById('successContainer').style.display = 'block';
        } else {
            document.getElementById('errorMessage').textContent = data.message;
            document.getElementById('errorContainer').style.display = 'block';
            btn.disabled = false;
        }
    })
    .catch(error => {
        document.getElementById('progressContainer').style.display = 'none';
        document.getElementById('errorMessage').textContent = 'Connection error: ' + error.message;
        document.getElementById('errorContainer').style.display = 'block';
        btn.disabled = false;
    });
});

document.getElementById('retryBtn').addEventListener('click', function() {
    document.getElementById('recalculateBtn').click();
});
</script>
@endsection
