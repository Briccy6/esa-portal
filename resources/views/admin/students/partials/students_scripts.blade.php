<script>
let searchTimeout;
const searchDelay = 500;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Students management system initialized');
    
    // Initialize search inputs
    const searchInputs = ['nameSearch', 'regNumberSearch', 'emailSearch', 'telephoneSearch', 'courseSearch'];
    
    searchInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                console.log('Search input detected:', inputId, this.value);
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, searchDelay);
            });
            
            input.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    performSearch();
                }
            });
        }
    });

    initializeTooltips();
    initializePaginationLinks();
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

function performSearch() {
    console.log('Performing search...');
    
    const formData = {
        name: document.getElementById('nameSearch')?.value.trim() || '',
        reg_number: document.getElementById('regNumberSearch')?.value.trim() || '',
        email: document.getElementById('emailSearch')?.value.trim() || '',
        telephone: document.getElementById('telephoneSearch')?.value.trim() || '',
        course: document.getElementById('courseSearch')?.value.trim() || ''
    };

    console.log('Search data:', formData);

    const queryString = new URLSearchParams(formData).toString();
    const url = window.location.pathname + (queryString ? '?' + queryString : '');
    
    console.log('Fetching URL:', url);
    fetchStudents(url);
}

function fetchStudents(url) {
    const container = document.getElementById('studentsTableContainer');
    
    if (!container) {
        console.error('Container not found');
        return;
    }
    
    console.log('Fetching students from:', url);
    showLoading(container);

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        console.log('HTML received, updating table');
        container.innerHTML = html;
        hideLoading(container);
        initializeTooltips();
        initializePaginationLinks();
        
        // Update URL without page reload
        window.history.pushState({path: url}, '', url);
    })
    .catch(error => {
        console.error('Error fetching students:', error);
        hideLoading(container);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger m-3';
        errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Error loading students. Please try again.';
        container.insertBefore(errorDiv, container.firstChild);
        
        setTimeout(() => errorDiv.remove(), 5000);
    });
}

function showLoading(container) {
    let overlay = container.querySelector('.loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        container.appendChild(overlay);
    }
    container.style.opacity = '0.6';
    container.style.pointerEvents = 'none';
}

function hideLoading(container) {
    const overlay = container.querySelector('.loading-overlay');
    if (overlay) {
        overlay.remove();
    }
    container.style.opacity = '1';
    container.style.pointerEvents = 'auto';
}

function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        const oldTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (oldTooltip) {
            oldTooltip.dispose();
        }
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initializePaginationLinks() {
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            const url = paginationLink.getAttribute('href');
            if (url && !paginationLink.classList.contains('disabled')) {
                console.log('Pagination clicked:', url);
                fetchStudents(url);
            }
        }
    });
}

function clearFilters() {
    console.log('Clearing filters');
    document.getElementById('nameSearch').value = '';
    document.getElementById('regNumberSearch').value = '';
    document.getElementById('emailSearch').value = '';
    document.getElementById('telephoneSearch').value = '';
    document.getElementById('courseSearch').value = '';
    performSearch();
}

// Modal Functions
function confirmDelete(studentId, studentName) {
    document.getElementById('studentName').textContent = studentName;
    document.getElementById('deleteForm').action = '/admin/students/' + studentId;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function showPasswordModal(regNumber, password) {
    document.getElementById('modalRegNumber').textContent = regNumber;
    document.getElementById('modalPassword').value = password;
    document.getElementById('modalPassword').type = 'text';
    document.getElementById('togglePasswordIcon').className = 'bi bi-eye';
    const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
    modal.show();
}

function togglePasswordVisibility() {
    const input = document.getElementById('modalPassword');
    const icon = document.getElementById('togglePasswordIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye-slash';
    }
}

function showDiscountModal(studentId, regNumber, fullName, discount) {
    document.getElementById('discountRegNumber').textContent = regNumber;
    document.getElementById('discountFullName').textContent = fullName;
    document.getElementById('discountInput').value = discount;
    document.getElementById('discountForm').action = '/admin/students/' + studentId + '/discount';
    const modal = new bootstrap.Modal(document.getElementById('discountModal'));
    modal.show();
}

function showTranscriptModal(studentId, fullName, regNumber) {
    const modalBody = document.getElementById('transcriptModalBody');
    modalBody.innerHTML = '<div class="text-center"><span class="spinner-border"></span> Loading...</div>';
    
    fetch('/students/' + studentId + '/transcript/ajax')
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading transcript:', error);
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading transcript</div>';
        });
    
    const modal = new bootstrap.Modal(document.getElementById('transcriptModal'));
    modal.show();
}

// Transaction editing functions (only for authorized users)
function editTransaction(transactionId, amount, type, status, paidAt) {
    document.getElementById('edit_transaction_id').value = transactionId;
    document.getElementById('edit_amount').value = amount;
    document.getElementById('edit_type').value = type;
    document.getElementById('edit_status').value = status;
    
    if (paidAt) {
        const date = new Date(paidAt);
        const formattedDate = date.toISOString().slice(0, 16);
        document.getElementById('edit_paid_at').value = formattedDate;
    }
    
    const form = document.getElementById('editTransactionForm');
    form.action = '/admin/transactions/' + transactionId + '/edit';
    
    const modal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
    modal.show();
}

function deleteTransaction(transactionId, studentName) {
    if (confirm('Are you sure you want to delete this transaction for ' + studentName + '? This action cannot be undone.')) {
        fetch('/admin/transactions/' + transactionId + '/delete', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Transaction deleted successfully');
                location.reload();
            } else {
                alert('Error deleting transaction: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting transaction');
        });
    }
}

// Export Functions
function exportToCSV() {
    const table = document.querySelector('.table');
    if (!table) return;
    
    const rows = Array.from(table.querySelectorAll('tr'));
    const csv = rows.map(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        return cells.slice(0, -1).map(cell => {
            return `"${cell.textContent.trim().replace(/"/g, '""')}"`;
        }).join(',');
    }).join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'students_list_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function printTable() {
    window.print();
}
</script>
