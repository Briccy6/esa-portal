// students-management.js
// This file contains all JavaScript functions for student management

let searchTimeout;
const searchDelay = 500;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Students management JS loaded');
    
    initializeSearchListeners();
    initializeTooltips();
    initializePaginationHandlers();
});

function initializeSearchListeners() {
    const searchInputs = ['nameSearch', 'regNumberSearch', 'emailSearch', 'telephoneSearch', 'courseSearch'];
    
    searchInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
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
}

function initializePaginationHandlers() {
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            const url = paginationLink.getAttribute('href');
            if (url && !paginationLink.classList.contains('disabled')) {
                fetchStudents(url);
            }
        }
    });
}

function performSearch() {
    const formData = {
        name: document.getElementById('nameSearch').value.trim(),
        reg_number: document.getElementById('regNumberSearch').value.trim(),
        email: document.getElementById('emailSearch').value.trim(),
        telephone: document.getElementById('telephoneSearch').value.trim(),
        course: document.getElementById('courseSearch').value.trim()
    };

    const queryString = new URLSearchParams(formData).toString();
    const baseUrl = document.querySelector('meta[name="students-index-url"]')?.getAttribute('content') || '/admin/students';
    const url = baseUrl + (queryString ? '?' + queryString : '');
    
    fetchStudents(url);
}

function fetchStudents(url) {
    const container = document.getElementById('studentsTableContainer');
    
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
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContent = doc.querySelector('#studentsTableContainer');
        
        hideLoading(container);
        
        if (newContent) {
            container.innerHTML = newContent.innerHTML;
        } else {
            container.innerHTML = html;
        }
        
        // Re-enable pointer events after content is updated
        container.style.pointerEvents = 'auto';
        container.style.opacity = '1';
        
        initializeTooltips();
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

function clearFilters() {
    document.getElementById('nameSearch').value = '';
    document.getElementById('regNumberSearch').value = '';
    document.getElementById('emailSearch').value = '';
    document.getElementById('telephoneSearch').value = '';
    document.getElementById('courseSearch').value = '';
    performSearch();
}

// ========================================
// MODAL FUNCTIONS (GLOBAL SCOPE)
// ========================================

function confirmDelete(studentId, studentName) {
    console.log('=== DELETE FUNCTION CALLED ===');
    console.log('Student ID:', studentId);
    console.log('Student Name:', studentName);
    
    // Set the student name in the modal
    const studentNameElement = document.getElementById('studentName');
    if (studentNameElement) {
        studentNameElement.textContent = studentName;
    }
    
    // Get the delete form and set its action
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        // Build the URL using the base URL from meta tag or default
        const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
        const deleteUrl = baseUrl + '/admin/students/' + studentId;
        
        deleteForm.action = deleteUrl;
        console.log('Delete form action set to:', deleteForm.action);
        
        // Show the modal
        const deleteModalElement = document.getElementById('deleteModal');
        if (deleteModalElement) {
            const deleteModal = new bootstrap.Modal(deleteModalElement);
            deleteModal.show();
            console.log('Modal displayed successfully');
        } else {
            console.error('Delete modal element not found!');
        }
    } else {
        console.error('Delete form not found!');
    }
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
    
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
    document.getElementById('discountForm').action = baseUrl + '/admin/students/' + studentId + '/discount';
    
    const modal = new bootstrap.Modal(document.getElementById('discountModal'));
    modal.show();
}

function showTranscriptModal(studentId, fullName, regNumber) {
    document.getElementById('transcriptStudentName').textContent = fullName;
    document.getElementById('transcriptRegNumber').textContent = regNumber;
    
    const tbody = document.getElementById('transcriptTableBody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center"><span class="spinner-border spinner-border-sm"></span> Loading...</td></tr>';
    
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
    fetch(baseUrl + '/admin/students/' + studentId + '/transcript-ajax')
        .then(response => response.text())
        .then(html => {
            tbody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading transcript:', error);
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading transcript</td></tr>';
        });
    
    const modal = new bootstrap.Modal(document.getElementById('transcriptModal'));
    modal.show();
}

// Export and Print Functions
function exportToCSV() {
    const table = document.querySelector('#studentsTableContainer table');
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

// Make functions globally available
window.confirmDelete = confirmDelete;
window.showPasswordModal = showPasswordModal;
window.togglePasswordVisibility = togglePasswordVisibility;
window.showDiscountModal = showDiscountModal;
window.showTranscriptModal = showTranscriptModal;
window.exportToCSV = exportToCSV;
window.printTable = printTable;
window.clearFilters = clearFilters;