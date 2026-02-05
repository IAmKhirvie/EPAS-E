@extends('layouts.app')

@section('title', 'Pending Registrations')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-user-clock me-2 text-primary"></i>Student Registrations
        </h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Status Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="?status=pending">
                <i class="fas fa-clock me-1"></i> Pending
                @if($counts['pending'] > 0)
                    <span class="badge bg-warning text-dark">{{ $counts['pending'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'email_verified' ? 'active' : '' }}" href="?status=email_verified">
                <i class="fas fa-envelope-check me-1"></i> Awaiting Approval
                @if($counts['email_verified'] > 0)
                    <span class="badge bg-info">{{ $counts['email_verified'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" href="?status=rejected">
                <i class="fas fa-times-circle me-1"></i> Rejected
                @if($counts['rejected'] > 0)
                    <span class="badge bg-danger">{{ $counts['rejected'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="?status=all">
                <i class="fas fa-list me-1"></i> All
                <span class="badge bg-secondary">{{ $counts['all'] }}</span>
            </a>
        </li>
    </ul>

    <!-- Registrations Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($registrations->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No registrations found.</p>
                </div>
            @else
                <form id="bulkForm" method="POST" action="{{ route('admin.registrations.bulk-approve') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registrations as $registration)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $registration->id }}" class="form-check-input row-checkbox">
                                    </td>
                                    <td>
                                        <strong>{{ $registration->full_name }}</strong>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $registration->email }}">{{ $registration->email }}</a>
                                    </td>
                                    <td>
                                        @switch($registration->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i> Pending Email
                                                </span>
                                                @break
                                            @case('email_verified')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-envelope-check me-1"></i> Awaiting Approval
                                                </span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i> Approved
                                                </span>
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i> Rejected
                                                </span>
                                                @break
                                            @case('transferred')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-user-check me-1"></i> Transferred
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $registration->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($registration->status === 'email_verified')
                                                <button type="button" class="btn btn-success btn-sm" title="Approve"
                                                        onclick="submitAction('{{ route('admin.registrations.approve', $registration) }}', 'POST')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif

                                            @if(!in_array($registration->status, ['transferred', 'rejected']))
                                                <button type="button" class="btn btn-danger btn-sm" title="Reject"
                                                        data-bs-toggle="modal" data-bs-target="#rejectModal"
                                                        data-id="{{ $registration->id }}"
                                                        data-name="{{ $registration->full_name }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif

                                            @if($registration->status === 'pending')
                                                <button type="button" class="btn btn-info btn-sm" title="Resend Verification"
                                                        onclick="submitAction('{{ route('admin.registrations.resend', $registration) }}', 'POST')">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            @endif

                                            @if($registration->status === 'rejected')
                                                <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                                        onclick="if(confirm('Delete this registration permanently?')) submitAction('{{ route('admin.registrations.destroy', $registration) }}', 'DELETE')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="card-footer bg-light d-flex align-items-center gap-2">
                        <span class="text-muted me-2">With selected:</span>
                        <button type="submit" formaction="{{ route('admin.registrations.bulk-approve') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-check me-1"></i> Approve
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#bulkRejectModal">
                            <i class="fas fa-times me-1"></i> Reject
                        </button>
                    </div>
                </form>

                <!-- Pagination -->
                <div class="d-flex justify-content-center py-3">
                    {{ $registrations->appends(['status' => $status])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Reject Registration</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Rejecting registration for: <strong id="rejectName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Enter rejection reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Bulk Reject</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Reject all selected registrations?</p>
                <div class="mb-3">
                    <label class="form-label">Reason (optional)</label>
                    <textarea id="bulkRejectReason" class="form-control" rows="3" placeholder="Enter rejection reason..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBulkReject">Reject Selected</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');

    selectAll?.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Reject modal
    const rejectModal = document.getElementById('rejectModal');
    rejectModal?.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');

        document.getElementById('rejectName').textContent = name;
        document.getElementById('rejectForm').action = '/admin/registrations/' + id + '/reject';
    });

    // Bulk reject
    document.getElementById('confirmBulkReject')?.addEventListener('click', function() {
        const form = document.getElementById('bulkForm');
        const reason = document.getElementById('bulkRejectReason').value;

        // Add reason to form
        let reasonInput = form.querySelector('input[name="reason"]');
        if (!reasonInput) {
            reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            form.appendChild(reasonInput);
        }
        reasonInput.value = reason;

        form.action = '{{ route("admin.registrations.bulk-reject") }}';
        form.submit();
    });
});

// Function to submit individual actions without nested forms
function submitAction(url, method) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.style.display = 'none';

    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                      document.querySelector('input[name="_token"]')?.value;
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);

    // Add method spoofing for DELETE
    if (method === 'DELETE') {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
    }

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
