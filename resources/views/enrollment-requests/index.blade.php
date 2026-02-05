@extends('layouts.app')

@section('title', 'Enrollment Requests')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Enrollment Requests</h4>
            <p class="text-muted mb-0">
                @if($user->role === 'instructor')
                    Manage your student enrollment requests
                @else
                    Review and process enrollment requests from instructors
                @endif
            </p>
        </div>
        @if($user->role === 'instructor')
        <a href="{{ route('enrollment-requests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Request
        </a>
        @endif
    </div>

    <!-- Status Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}"
               href="{{ route('enrollment-requests.index', ['status' => 'all']) }}">
                All <span class="badge bg-secondary ms-1">{{ $counts['all'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}"
               href="{{ route('enrollment-requests.index', ['status' => 'pending']) }}">
                Pending <span class="badge bg-warning text-dark ms-1">{{ $counts['pending'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}"
               href="{{ route('enrollment-requests.index', ['status' => 'approved']) }}">
                Approved <span class="badge bg-success ms-1">{{ $counts['approved'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}"
               href="{{ route('enrollment-requests.index', ['status' => 'rejected']) }}">
                Rejected <span class="badge bg-danger ms-1">{{ $counts['rejected'] }}</span>
            </a>
        </li>
    </ul>

    <!-- Requests List -->
    <div class="card">
        <div class="card-body p-0">
            @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Section</th>
                            @if($user->role === 'admin')
                            <th>Requested By</th>
                            @endif
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($request->student)
                                    <img src="{{ $request->student->profile_image_url }}" alt="Avatar"
                                         class="rounded-circle me-2" width="32" height="32"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($request->student->initials) }}&background=007fc9&color=fff&size=32'">
                                    @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2"
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-white" style="font-size: 14px;"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $request->student_display_name }}</div>
                                        <small class="text-muted">{{ $request->student_display_email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $request->section }}</span>
                            </td>
                            @if($user->role === 'admin')
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $request->instructor->profile_image_url }}" alt="Avatar"
                                         class="rounded-circle me-2" width="24" height="24"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($request->instructor->initials) }}&background=28a745&color=fff&size=24'">
                                    <span>{{ $request->instructor->full_name }}</span>
                                </div>
                            </td>
                            @endif
                            <td>
                                @if($request->isPending())
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                @elseif($request->isApproved())
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i> Approved
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i> Rejected
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $request->created_at->format('M j, Y') }}</div>
                                <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($request->isPending())
                                    @if($user->role === 'admin')
                                    <button type="button" class="btn btn-sm btn-success me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal{{ $request->id }}">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $request->id }}">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    @else
                                    <form action="{{ route('enrollment-requests.cancel', $request) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Cancel this enrollment request?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                    @endif
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailsModal{{ $request->id }}">
                                        <i class="fas fa-info-circle"></i> Details
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($requests->hasPages())
            <div class="card-footer">
                {{ $requests->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5>No Enrollment Requests</h5>
                <p class="text-muted">
                    @if($status === 'pending')
                        There are no pending enrollment requests.
                    @elseif($status === 'approved')
                        No approved requests found.
                    @elseif($status === 'rejected')
                        No rejected requests found.
                    @else
                        No enrollment requests have been made yet.
                    @endif
                </p>
                @if($user->role === 'instructor')
                <a href="{{ route('enrollment-requests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Create Request
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
@foreach($requests as $request)
    @if($request->isPending() && $user->role === 'admin')
    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Enrollment Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('enrollment-requests.approve', $request) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Approve <strong>{{ $request->student_display_name }}</strong> to be enrolled in <strong>{{ $request->section }}</strong>?</p>
                        @if($request->notes)
                        <div class="alert alert-info">
                            <strong>Instructor Notes:</strong> {{ $request->notes }}
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Admin Notes (Optional)</label>
                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="Add any notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Enrollment Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('enrollment-requests.reject', $request) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Reject enrollment request for <strong>{{ $request->student_display_name }}</strong>?</p>
                        @if($request->notes)
                        <div class="alert alert-info">
                            <strong>Instructor Notes:</strong> {{ $request->notes }}
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="admin_notes" class="form-control" rows="3" required
                                      placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-1"></i> Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if(!$request->isPending())
    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal{{ $request->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <dl class="row">
                        <dt class="col-sm-4">Student</dt>
                        <dd class="col-sm-8">{{ $request->student_display_name }}</dd>

                        <dt class="col-sm-4">Section</dt>
                        <dd class="col-sm-8">{{ $request->section }}</dd>

                        <dt class="col-sm-4">Requested By</dt>
                        <dd class="col-sm-8">{{ $request->instructor->full_name }}</dd>

                        <dt class="col-sm-4">Request Date</dt>
                        <dd class="col-sm-8">{{ $request->created_at->format('M j, Y g:i A') }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            @if($request->isApproved())
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Processed By</dt>
                        <dd class="col-sm-8">{{ $request->processedBy?->full_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Processed At</dt>
                        <dd class="col-sm-8">{{ $request->processed_at?->format('M j, Y g:i A') ?? 'N/A' }}</dd>

                        @if($request->notes)
                        <dt class="col-sm-4">Instructor Notes</dt>
                        <dd class="col-sm-8">{{ $request->notes }}</dd>
                        @endif

                        @if($request->admin_notes)
                        <dt class="col-sm-4">Admin Notes</dt>
                        <dd class="col-sm-8">{{ $request->admin_notes }}</dd>
                        @endif
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection
