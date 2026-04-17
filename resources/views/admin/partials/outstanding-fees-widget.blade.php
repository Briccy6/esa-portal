<!-- Outstanding Fees Summary Widget - Add to Admin Dashboard -->

<div class="col-md-3">
    <div class="card border-0 shadow-sm" id="outstandingFeesCard">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <p class="text-muted mb-1 small">
                        <i class="fas fa-exclamation-triangle text-danger me-1"></i>
                        Students w/ Outstanding Fees
                    </p>
                    <h3 class="mb-0 text-danger" id="outstandingCount">--</h3>
                </div>
                <i class="fas fa-money-bill-wave fs-3 text-danger opacity-25"></i>
            </div>
            
            <div class="progress mb-3" style="height: 4px;">
                <div class="progress-bar bg-danger" id="outstandingPercent" role="progressbar" style="width: 0%"></div>
            </div>
            
            <small class="text-muted d-block mb-2">
                Total Outstanding: <span id="outstandingAmount" class="fw-bold">RWF 0</span>
            </small>
            
            <a href="{{ route('admin.report-cards.outstanding-fees') }}" class="btn btn-sm btn-outline-danger w-100">
                <i class="fas fa-eye me-1"></i>View All
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch outstanding fees stats
    fetch('{{ route("admin.report-cards.check-fees-for-generation") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({
            course_id: '',  // Empty to get all students
            academic_year_id: '',
            term_ids: []
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const count = data.count || 0;
            const totalOutstanding = data.students
                .reduce((sum, student) => sum + (student.outstanding_amount || 0), 0);
            
            document.getElementById('outstandingCount').textContent = count;
            
            // Format with proper thousand separators and 2 decimal places
            const formatted = new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
                useGrouping: true
            }).format(totalOutstanding);
            document.getElementById('outstandingAmount').textContent = 'RWF ' + formatted;
            
            // Update color based on severity
            const card = document.getElementById('outstandingFeesCard');
            if (count > 10) {
                card.classList.add('border-danger');
                document.getElementById('outstandingPercent').style.width = '100%';
            } else if (count > 5) {
                document.getElementById('outstandingPercent').style.width = '70%';
            } else if (count > 0) {
                document.getElementById('outstandingPercent').style.width = '40%';
            }
        }
    })
    .catch(error => console.error('Error fetching outstanding fees:', error));
});
</script>
