@extends('teacher.layout')

@section('title', 'Live Class Requests')

@section('content')

<style>
.lc-wrap { padding: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

/* ── Card ── */
.lc-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.09);
    border: 1px solid #e8ecf0;
    overflow: hidden;
}

/* ── Header ── */
.lc-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #f0f2f5;
    flex-wrap: wrap;
    gap: 12px;
}
.lc-head-left { display: flex; align-items: center; gap: 14px; }
.lc-head-icon {
    width: 44px; height: 44px;
    background: #eff6ff;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    color: #2563eb; font-size: 19px; flex-shrink: 0;
}
.lc-head-title { font-size: 1.15rem; font-weight: 700; color: #111827; margin: 0; line-height: 1.3; }
.lc-head-sub   { font-size: 0.78rem; color: #6b7280; margin: 2px 0 0; }

/* ── Content ── */
.lc-content { padding: 24px; }

/* ── Request Item ── */
.lc-request {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    background: #fafbfc;
    transition: all 0.2s;
}
.lc-request:hover {
    border-color: #d1d5db;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.lc-request:last-child { margin-bottom: 0; }

.lc-request-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 16px;
}
.lc-request-info {
    flex: 1;
}
.lc-request-name {
    font-size: 1.05rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}
.lc-request-meta {
    display: flex;
    gap: 16px;
    font-size: 0.85rem;
    color: #6b7280;
}
.lc-request-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.lc-request-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}
.lc-request-status.pending {
    background: #fef3c7;
    color: #92400e;
}
.lc-request-status.approved {
    background: #d1fae5;
    color: #065f46;
}
.lc-request-status.rejected {
    background: #fee2e2;
    color: #991b1b;
}

.lc-request-reason {
    background: #fff;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 16px;
    border: 1px solid #e5e7eb;
}
.lc-request-reason-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.lc-request-reason-text {
    font-size: 0.9rem;
    color: #374151;
    line-height: 1.5;
}

.lc-request-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.lc-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}
.lc-btn-approve {
    background: #10b981;
    color: white;
}
.lc-btn-approve:hover {
    background: #059669;
}
.lc-btn-reject {
    background: #ef4444;
    color: white;
}
.lc-btn-reject:hover {
    background: #dc2626;
}
.lc-btn-disabled {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
}

/* ── Empty State ── */
.lc-empty {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}
.lc-empty-icon {
    font-size: 3rem;
    margin-bottom: 16px;
    opacity: 0.3;
}
.lc-empty-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 8px;
    color: #374151;
}
.lc-empty-text {
    font-size: 0.9rem;
    line-height: 1.5;
}

/* ── Modal ── */
.lc-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}
.lc-modal.show {
    display: flex;
}
.lc-modal-content {
    background: white;
    border-radius: 12px;
    padding: 24px;
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}
.lc-modal-header {
    margin-bottom: 20px;
}
.lc-modal-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 8px;
}
.lc-modal-subtitle {
    font-size: 0.9rem;
    color: #6b7280;
}
.lc-modal-body {
    margin-bottom: 20px;
}
.lc-form-group {
    margin-bottom: 16px;
}
.lc-form-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}
.lc-form-textarea {
    width: 100%;
    min-height: 100px;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    resize: vertical;
}
.lc-modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}
</style>

<div class="lc-wrap">
    <div class="lc-card">
        <div class="lc-head">
            <div class="lc-head-left">
                <div class="lc-head-icon">📋</div>
                <div>
                    <h1 class="lc-head-title">Class Requests</h1>
                    <p class="lc-head-sub">{{ $liveClass->title }} - Manage student access requests</p>
                </div>
            </div>
            <a href="{{ route('teacher.live-classes.show', $liveClass) }}" class="lc-btn lc-btn-secondary">
                ← Back to Class
            </a>
        </div>

        <div class="lc-content">
            @if($requests->count() > 0)
                <div id="requests-container">
                    @foreach($requests as $request)
                        <div class="lc-request" data-request-id="{{ $request->id }}">
                            <div class="lc-request-header">
                                <div class="lc-request-info">
                                    <div class="lc-request-name">
                                        {{ $request->user->full_name ?? 'Unknown Student' }}
                                    </div>
                                    <div class="lc-request-meta">
                                        <div class="lc-request-meta-item">
                                            📧 {{ $request->user->email ?? 'No email' }}
                                        </div>
                                        <div class="lc-request-meta-item">
                                            📚 {{ $request->user->course->name ?? 'No course' }}
                                        </div>
                                        <div class="lc-request-meta-item">
                                            🕒 {{ $request->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                <div class="lc-request-status {{ $request->status }}">
                                    {{ $request->status }}
                                </div>
                            </div>

                            @if($request->reason)
                                <div class="lc-request-reason">
                                    <div class="lc-request-reason-label">Reason for Request</div>
                                    <div class="lc-request-reason-text">{{ $request->reason }}</div>
                                </div>
                            @endif

                            @if($request->status === 'pending')
                                <div class="lc-request-actions">
                                    <button type="button" class="lc-btn lc-btn-approve" onclick="approveRequest({{ $request->id }})">
                                        ✅ Approve
                                    </button>
                                    <button type="button" class="lc-btn lc-btn-reject" onclick="showRejectModal({{ $request->id }})">
                                        ❌ Reject
                                    </button>
                                </div>
                            @else
                                <div class="lc-request-actions">
                                    <span class="lc-btn lc-btn-disabled">
                                        {{ $request->status === 'approved' ? '✅ Approved' : '❌ Rejected' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="lc-empty">
                    <div class="lc-empty-icon">📭</div>
                    <div class="lc-empty-title">No Pending Requests</div>
                    <div class="lc-empty-text">
                        There are no student requests for this live class at the moment.<br>
                        Check back later for new requests.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="lc-modal">
    <div class="lc-modal-content">
        <div class="lc-modal-header">
            <div class="lc-modal-title">Reject Request</div>
            <div class="lc-modal-subtitle">Please provide a reason for rejecting this request</div>
        </div>
        <div class="lc-modal-body">
            <div class="lc-form-group">
                <label class="lc-form-label">Rejection Reason</label>
                <textarea id="rejectReason" class="lc-form-textarea" placeholder="Enter reason for rejection..." required></textarea>
            </div>
        </div>
        <div class="lc-modal-actions">
            <button type="button" class="lc-btn lc-btn-secondary" onclick="closeRejectModal()">Cancel</button>
            <button type="button" class="lc-btn lc-btn-reject" onclick="confirmReject()">Reject Request</button>
        </div>
    </div>
</div>

<script>
let currentRequestId = null;

function approveRequest(requestId) {
    if (!confirm('Approve this request?')) return;
    
    fetch(`{{ route('teacher.live-classes.requests.approve', [$liveClass, 'requestId']) }}`.replace('requestId', requestId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error approving request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error approving request');
    });
}

function showRejectModal(requestId) {
    currentRequestId = requestId;
    document.getElementById('rejectModal').classList.add('show');
    document.getElementById('rejectReason').focus();
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('show');
    document.getElementById('rejectReason').value = '';
    currentRequestId = null;
}

function confirmReject() {
    const reason = document.getElementById('rejectReason').value.trim();
    
    if (!reason) {
        alert('Please provide a reason for rejection');
        return;
    }
    
    fetch(`{{ route('teacher.live-classes.requests.reject', [$liveClass, 'requestId']) }}`.replace('requestId', currentRequestId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeRejectModal();
            location.reload();
        } else {
            alert('Error rejecting request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error rejecting request');
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
    }
});

// Close modal on background click
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>

@endsection
