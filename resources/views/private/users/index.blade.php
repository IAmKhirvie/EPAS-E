@extends('layouts.app')

@section('title', $pageTitle ?? 'Users')

@section('content')
<link rel="stylesheet" href="{{ dynamic_asset('css/pages/index.css')}}">

@php
    $currentRoute = $currentRoute ?? 'private.users.index';
    $pageTitle = $pageTitle ?? 'User Management';
    $roleFilter = $roleFilter ?? null;
    $canDelete = $canDelete ?? (Auth::user()->role === 'admin');
    $canCreate = $canCreate ?? (Auth::user()->role === 'admin');
@endphp

<div class="content-area">
    <!-- Header with Title and Stats -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-users me-2 text-primary"></i>{{ $pageTitle }}
                    </h5>
                    <small class="text-muted">Manage and view {{ $roleFilter ? $roleFilter : 'user' }} accounts</small>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        <i class="fas fa-users me-1"></i> Total: {{ $users->total() }}
                    </span>
                    @if($filterCounts['pending'] ?? 0 > 0)
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 ms-1">
                            <i class="fas fa-clock me-1"></i> Pending: {{ $filterCounts['pending'] }}
                        </span>
                    @endif
                    @if($canCreate)
                        <a href="{{ route('private.users.import') }}" class="btn btn-success btn-sm ms-2">
                            <i class="fas fa-file-import me-1"></i>Import Users
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <div class="row align-items-center g-2">
                <!-- Search -->
                <div class="col-md-7 col-lg-8">
                    <form method="GET" action="{{ route($currentRoute) }}" id="searchForm">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                placeholder="Search by name, email, or ID..." value="{{ request('search') }}"
                                autocomplete="off">
                            @if(request('search'))
                                <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                        <input type="hidden" name="sort" value="{{ request('sort', '') }}">
                        <input type="hidden" name="direction" value="{{ request('direction', '') }}">
                        <input type="hidden" name="filter" value="{{ request('filter', '') }}">
                    </form>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-md-3 col-lg-2">
                    <div class="dropdown w-100">
                        <button class="btn btn-outline-secondary w-100 dropdown-toggle d-flex align-items-center justify-content-between" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>
                                <i class="fas fa-filter me-1"></i>
                                @if(request()->has('filter') && request('filter'))
                                    {{ ucwords(str_replace(['=', '_'], [': ', ' '], request('filter'))) }}
                                @else
                                    Filter
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end w-100" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item filter-option {{ !request()->has('filter') || !request('filter') ? 'active' : '' }}" href="#" data-filter="all">
                                <i class="fas fa-list me-2"></i>All {{ $roleFilter ? ucfirst($roleFilter) . 's' : 'Users' }}
                                <span class="badge bg-secondary float-end">{{ $filterCounts['total'] ?? $users->total() }}</span>
                            </a></li>
                            @if(!$roleFilter)
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header"><i class="fas fa-user-tag me-1"></i>By Role</h6></li>
                                <li><a class="dropdown-item filter-option {{ request('filter') == 'role=student' ? 'active' : '' }}" href="#" data-filter="role=student">
                                    <i class="fas fa-user-graduate me-2 text-info"></i>Students
                                    <span class="badge bg-info float-end">{{ $filterCounts['students'] ?? '' }}</span>
                                </a></li>
                                <li><a class="dropdown-item filter-option {{ request('filter') == 'role=instructor' ? 'active' : '' }}" href="#" data-filter="role=instructor">
                                    <i class="fas fa-chalkboard-teacher me-2 text-success"></i>Instructors
                                    <span class="badge bg-success float-end">{{ $filterCounts['instructors'] ?? '' }}</span>
                                </a></li>
                                <li><a class="dropdown-item filter-option {{ request('filter') == 'role=admin' ? 'active' : '' }}" href="#" data-filter="role=admin">
                                    <i class="fas fa-user-shield me-2 text-primary"></i>Admins
                                    <span class="badge bg-primary float-end">{{ $filterCounts['admins'] ?? '' }}</span>
                                </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header"><i class="fas fa-toggle-on me-1"></i>By Status</h6></li>
                            <li><a class="dropdown-item filter-option {{ request('filter') == 'status=pending' ? 'active' : '' }}" href="#" data-filter="status=pending">
                                <i class="fas fa-clock me-2 text-warning"></i>Pending Approval
                                <span class="badge bg-warning text-dark float-end">{{ $filterCounts['pending'] ?? '' }}</span>
                            </a></li>
                            <li><a class="dropdown-item filter-option {{ request('filter') == 'status=active' ? 'active' : '' }}" href="#" data-filter="status=active">
                                <i class="fas fa-check-circle me-2 text-success"></i>Active
                                <span class="badge bg-success float-end">{{ $filterCounts['active'] ?? '' }}</span>
                            </a></li>
                            <li><a class="dropdown-item filter-option {{ request('filter') == 'verified=no' ? 'active' : '' }}" href="#" data-filter="verified=no">
                                <i class="fas fa-envelope-open me-2 text-danger"></i>Unverified Email
                                <span class="badge bg-danger float-end">{{ $filterCounts['unverified'] ?? '' }}</span>
                            </a></li>
                        </ul>
                    </div>
                </div>

                <!-- Clear Filters -->
                <div class="col-md-2 col-lg-2 text-end">
                    @if(request()->has('filter') || request()->has('search') || request()->has('sort'))
                        <a href="{{ route($currentRoute) }}" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Clear all filters">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Bulk Actions Form -->
    <form id="bulkActionForm" method="POST">
        @csrf
        <input type="hidden" name="_method" id="bulkMethod" value="POST">

        <!-- Desktop Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            @if($canDelete)
                            <th width="40">
                                <input type="checkbox" id="selectAll" class="form-check-input" title="Select all">
                            </th>
                            @endif
                            <th data-sort="id"># <i class="fas fa-sort"></i></th>
                            <th></th>
                            <th data-sort="first_name">Name <i class="fas fa-sort"></i></th>
                            <th data-sort="student_id">Student ID <i class="fas fa-sort"></i></th>
                            <th data-sort="email">Email <i class="fas fa-sort"></i></th>
                            <th data-sort="role">Role <i class="fas fa-sort"></i></th>
                            <th data-sort="department_id">Department <i class="fas fa-sort"></i></th>
                            <th data-sort="section">Section <i class="fas fa-sort"></i></th>
                            <th data-sort="room_number">Room <i class="fas fa-sort"></i></th>
                            <th data-sort="email_verified_at">Verified <i class="fas fa-sort"></i></th>
                            <th data-sort="stat">Status <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                            <tr>
                                @if($canDelete)
                                <td>
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input row-checkbox" {{ $user->id == Auth::id() ? 'disabled title=Cannot select yourself' : '' }}>
                                </td>
                                @endif
                                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                                <td>
                                    <img src="{{ $user->profile_image_url }}" alt="Avatar" class="rounded-circle" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->initials) }}&background=007fc9&color=fff&size=32'">
                                </td>
                                <td>{{ $user->full_name }}</td>
                                <td>{{ $user->student_id ?? 'N/A' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($user->email, 15) }}</td>
                                <td><span class="badge bg-{{ $user->role == 'admin' ? 'primary' : ($user->role == 'instructor' ? 'info' : 'secondary') }}">{{ ucfirst($user->role) }}</span></td>
                                <td>{{ \Illuminate\Support\Str::limit($user->department->name ?? 'N/A', 12) }}</td>
                                <td>{{ $user->section ?? 'N/A' }}</td>
                                <td>{{ $user->room_number ?? 'N/A' }}</td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-warning text-dark">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->stat)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="action-buttons">
                                    @if(!$user->stat && $canDelete)
                                        <button type="button" class="btn btn-sm btn-success"
                                                onclick="if(confirm('Approve this user?')) submitUserAction('{{ route('private.users.approve', $user->id) }}', 'POST')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif

                                    <a href="{{ route('private.users.edit', $user->id)}}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>

                                    @if($canDelete && $user->id != Auth::id())
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="if(confirm('Are you sure you want to DELETE this user?')) submitUserAction('{{ route('private.users.destroy', $user->id) }}', 'DELETE')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canDelete ? 13 : 12 }}" class="text-center py-3 text-muted">
                                    <i class="fas fa-users fa-lg mb-2"></i>
                                    <p class="mb-0">No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        @if($canDelete)
        <div id="bulkActionsBar" class="card border-0 shadow-sm mt-3" style="display: none;">
            <div class="card-body py-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="text-muted me-2">
                        <strong id="selectedCount">0</strong> selected:
                    </span>
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('activate')" title="Activate selected users">
                        <i class="fas fa-check me-1"></i>Activate
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('deactivate')" title="Deactivate selected users">
                        <i class="fas fa-ban me-1"></i>Deactivate
                    </button>
                    <div class="input-group input-group-sm" style="width: auto;">
                        <input type="text" id="sectionInput" class="form-control" placeholder="Section..." style="width: 100px;">
                        <button type="button" class="btn btn-info" onclick="bulkAction('assign-section')" title="Assign section">
                            <i class="fas fa-users-cog"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')" title="Delete selected users">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
        @endif
    </form>

    <!-- Mobile Expandable Profiles -->
    <div class="mobile-users-container">
        @forelse ($users as $user)
            <div class="mobile-profile-card" id="profile-{{ $user->id }}">
                <div class="mobile-profile-header" onclick="toggleProfile({{ $user->id }})">
                    <div class="mobile-profile-avatar">
                        <img src="{{ $user->profile_image_url }}" alt="{{ $user->full_name }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->initials) }}&background=007fc9&color=fff&size=64'">
                    </div>
                    <div class="mobile-profile-info">
                        <div class="mobile-profile-name">{{ $user->full_name }}</div>
                        <div class="mobile-profile-role">{{ ucfirst($user->role) }}</div>
                        <div class="mobile-profile-badges">
                            @if($user->stat)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="mobile-profile-actions">
                        @if(!$user->stat && $canDelete)
                            <button type="button" class="btn btn-success" title="Approve"
                                    onclick="if(confirm('Approve this user?')) submitUserAction('{{ route('private.users.approve', $user->id) }}', 'POST')">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif

                        <a href="{{ route('private.users.edit', $user->id)}}" class="btn btn-outline-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>

                        @if($canDelete && $user->id != Auth::id())
                            <button type="button" class="btn btn-outline-danger" title="Delete"
                                    onclick="if(confirm('Are you sure you want to DELETE this user?')) submitUserAction('{{ route('private.users.destroy', $user->id) }}', 'DELETE')">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                    <button class="mobile-expand-btn">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="mobile-profile-details">
                    <div class="mobile-details-content">
                        <div class="mobile-detail-grid">
                            <div class="mobile-detail-item">
                                <span class="mobile-detail-label">Email</span>
                                <span class="mobile-detail-value">{{ $user->email }}</span>
                            </div>
                            <div class="mobile-detail-item">
                                <span class="mobile-detail-label">Student ID</span>
                                <span class="mobile-detail-value">{{ $user->student_id ?? 'N/A' }}</span>
                            </div>
                            <div class="mobile-detail-item">
                                <span class="mobile-detail-label">Department</span>
                                <span class="mobile-detail-value">{{ $user->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="mobile-detail-item">
                                <span class="mobile-detail-label">Section</span>
                                <span class="mobile-detail-value">{{ $user->section ?? 'N/A' }}</span>
                            </div>
                            <div class="mobile-detail-item">
                                <span class="mobile-detail-label">Room</span>
                                <span class="mobile-detail-value">{{ $user->room_number ?? 'N/A' }}</span>
                            </div>
                            <div class="mobile-detail-item">
                                <span class="mobile-detail-label">Email Verified</span>
                                <span class="mobile-detail-value">
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-warning text-dark">No</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="fas fa-users fa-2x mb-3"></i>
                <p>No users found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="pagination-container">
            <div class="pagination-info">
                {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }}
            </div>
            
            <nav aria-label="User pagination">
                <ul class="pagination mb-0">
                    @if ($users->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">&laquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev">&laquo;</a>
                        </li>
                    @endif

                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        @if ($page == $users->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if ($users->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next">&raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">&raquo;</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
</div>

<script>
function toggleProfile(userId) {
    const profileCard = document.getElementById(`profile-${userId}`);
    profileCard.classList.toggle('expanded');
}

// Close other profiles when one is opened (optional)
function closeOtherProfiles(currentUserId) {
    document.querySelectorAll('.mobile-profile-card').forEach(card => {
        if (!card.id.includes(currentUserId)) {
            card.classList.remove('expanded');
        }
    });
}

// Add click event to close other profiles when one is opened
document.querySelectorAll('.mobile-profile-header').forEach(header => {
    header.addEventListener('click', function() {
        const profileCard = this.closest('.mobile-profile-card');
        const userId = profileCard.id.split('-')[1];
        closeOtherProfiles(userId);
    });
});

// Clear search function
function clearSearch() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.value = '';
        document.getElementById('searchForm').submit();
    }
}

// Bulk Actions
const selectAllCheckbox = document.getElementById('selectAll');
const rowCheckboxes = document.querySelectorAll('.row-checkbox');
const bulkActionsBar = document.getElementById('bulkActionsBar');
const selectedCountEl = document.getElementById('selectedCount');
const bulkForm = document.getElementById('bulkActionForm');

function updateBulkActionsBar() {
    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
    if (selectedCountEl) selectedCountEl.textContent = checkedCount;
    if (bulkActionsBar) {
        bulkActionsBar.style.display = checkedCount > 0 ? 'block' : 'none';
    }
}

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => {
            if (!cb.disabled) cb.checked = this.checked;
        });
        updateBulkActionsBar();
    });
}

rowCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkActionsBar);
});

function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one user.');
        return;
    }

    let confirmMsg = '';
    let url = '';

    switch (action) {
        case 'delete':
            confirmMsg = `Are you sure you want to delete ${checkedBoxes.length} user(s)? This cannot be undone.`;
            url = '{{ route("private.users.bulk-delete") }}';
            break;
        case 'activate':
            confirmMsg = `Activate ${checkedBoxes.length} user(s)?`;
            url = '{{ route("private.users.bulk-activate") }}';
            break;
        case 'deactivate':
            confirmMsg = `Deactivate ${checkedBoxes.length} user(s)?`;
            url = '{{ route("private.users.bulk-deactivate") }}';
            break;
        case 'assign-section':
            const section = document.getElementById('sectionInput').value.trim();
            if (!section) {
                alert('Please enter a section name.');
                return;
            }
            confirmMsg = `Assign ${checkedBoxes.length} user(s) to section "${section}"?`;
            url = '{{ route("private.users.bulk-assign-section") }}';
            // Add section as hidden input
            let sectionInput = bulkForm.querySelector('input[name="section"]');
            if (!sectionInput) {
                sectionInput = document.createElement('input');
                sectionInput.type = 'hidden';
                sectionInput.name = 'section';
                bulkForm.appendChild(sectionInput);
            }
            sectionInput.value = section;
            break;
    }

    if (confirm(confirmMsg)) {
        bulkForm.action = url;
        bulkForm.submit();
    }
}

// Function to submit individual user actions (approve, delete) without nested forms
function submitUserAction(url, method) {
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

@section('scripts')
<script src="{{ dynamic_asset('js/users/index.js') }}"></script>
@endsection