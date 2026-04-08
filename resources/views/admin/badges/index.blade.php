@extends('layouts.app')

@section('title', 'Badge Management')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Badge Management</h4>
            <p class="text-muted mb-0">Create and manage badges that students can earn</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBadgeModal">
            <i class="fas fa-plus me-1"></i> New Badge
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($badges->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-award fa-4x text-muted mb-3"></i>
                <h5>No Badges Created</h5>
                <p class="text-muted">Create your first badge to get started.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBadgeModal">
                    <i class="fas fa-plus me-1"></i> Create Badge
                </button>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($badges as $badge)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="badge-icon-display me-3" style="color: {{ $badge->color ?? '#ffc107' }};">
                                        <i class="{{ $badge->icon ?? 'fas fa-medal' }} fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $badge->name }}</h5>
                                        <span class="badge bg-{{ $badge->type === 'achievement' ? 'primary' : ($badge->type === 'milestone' ? 'info' : ($badge->type === 'streak' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst($badge->type) }}
                                        </span>
                                    </div>
                                </div>
                                @if(!$badge->is_active)
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>

                            <p class="text-muted small mb-2">{{ $badge->description ?: 'No description' }}</p>

                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span><i class="fas fa-users me-1"></i> {{ $badge->users_count }} earned</span>
                                @if($badge->points_required > 0)
                                    <span><i class="fas fa-star me-1"></i> {{ $badge->points_required }} pts required</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editBadge({{ $badge->id }}, {{ json_encode($badge) }})">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <form action="{{ route('admin.badges.destroy', $badge) }}" method="POST" class="flex-fill"
                                      onsubmit="return confirm('Delete badge &quot;{{ $badge->name }}&quot;? Students who earned it will lose it.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Create Badge Modal --}}
<div class="modal fade" id="createBadgeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.badges.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Create Badge</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g., First Login">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="e.g., Awarded for logging in the first time"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Icon *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i id="createIconPreview" class="fas fa-medal"></i></span>
                                <select name="icon" class="form-select" onchange="document.getElementById('createIconPreview').className=this.value" required>
                                    <option value="fas fa-medal">Medal</option>
                                    <option value="fas fa-trophy">Trophy</option>
                                    <option value="fas fa-star">Star</option>
                                    <option value="fas fa-crown">Crown</option>
                                    <option value="fas fa-gem">Gem</option>
                                    <option value="fas fa-bolt">Bolt</option>
                                    <option value="fas fa-fire">Fire</option>
                                    <option value="fas fa-rocket">Rocket</option>
                                    <option value="fas fa-shield-alt">Shield</option>
                                    <option value="fas fa-graduation-cap">Graduation Cap</option>
                                    <option value="fas fa-book-reader">Book Reader</option>
                                    <option value="fas fa-check-double">Double Check</option>
                                    <option value="fas fa-certificate">Certificate</option>
                                    <option value="fas fa-award">Award</option>
                                    <option value="fas fa-thumbs-up">Thumbs Up</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color *</label>
                            <select name="color" class="form-select" required>
                                <option value="#ffc107">Gold</option>
                                <option value="#6f42c1">Purple</option>
                                <option value="#0d6efd">Blue</option>
                                <option value="#198754">Green</option>
                                <option value="#dc3545">Red</option>
                                <option value="#fd7e14">Orange</option>
                                <option value="#20c997">Teal</option>
                                <option value="#6c757d">Silver</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="achievement">Achievement</option>
                                <option value="milestone">Milestone</option>
                                <option value="streak">Streak</option>
                                <option value="special">Special</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Points Required</label>
                            <input type="number" name="points_required" class="form-control" min="0" value="0" placeholder="0 = manual only">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Create Badge</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Badge Modal --}}
<div class="modal fade" id="editBadgeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editBadgeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Badge</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Icon *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i id="editIconPreview" class="fas fa-medal"></i></span>
                                <select name="icon" id="editIcon" class="form-select" onchange="document.getElementById('editIconPreview').className=this.value" required>
                                    <option value="fas fa-medal">Medal</option>
                                    <option value="fas fa-trophy">Trophy</option>
                                    <option value="fas fa-star">Star</option>
                                    <option value="fas fa-crown">Crown</option>
                                    <option value="fas fa-gem">Gem</option>
                                    <option value="fas fa-bolt">Bolt</option>
                                    <option value="fas fa-fire">Fire</option>
                                    <option value="fas fa-rocket">Rocket</option>
                                    <option value="fas fa-shield-alt">Shield</option>
                                    <option value="fas fa-graduation-cap">Graduation Cap</option>
                                    <option value="fas fa-book-reader">Book Reader</option>
                                    <option value="fas fa-check-double">Double Check</option>
                                    <option value="fas fa-certificate">Certificate</option>
                                    <option value="fas fa-award">Award</option>
                                    <option value="fas fa-thumbs-up">Thumbs Up</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color *</label>
                            <select name="color" id="editColor" class="form-select" required>
                                <option value="#ffc107">Gold</option>
                                <option value="#6f42c1">Purple</option>
                                <option value="#0d6efd">Blue</option>
                                <option value="#198754">Green</option>
                                <option value="#dc3545">Red</option>
                                <option value="#fd7e14">Orange</option>
                                <option value="#20c997">Teal</option>
                                <option value="#6c757d">Silver</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Type *</label>
                            <select name="type" id="editType" class="form-select" required>
                                <option value="achievement">Achievement</option>
                                <option value="milestone">Milestone</option>
                                <option value="streak">Streak</option>
                                <option value="special">Special</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Points Required</label>
                            <input type="number" name="points_required" id="editPointsRequired" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" value="1" checked>
                            <label class="form-check-label" for="editIsActive">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Badge</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.badge-icon-display {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(0,0,0,0.04);
}
</style>
<script>
function editBadge(id, badge) {
    document.getElementById('editBadgeForm').action = '/admin/badges/' + id;
    document.getElementById('editName').value = badge.name;
    document.getElementById('editDescription').value = badge.description || '';
    document.getElementById('editIcon').value = badge.icon || 'fas fa-medal';
    document.getElementById('editIconPreview').className = badge.icon || 'fas fa-medal';
    document.getElementById('editColor').value = badge.color || '#ffc107';
    document.getElementById('editType').value = badge.type || 'achievement';
    document.getElementById('editPointsRequired').value = badge.points_required || 0;
    document.getElementById('editIsActive').checked = badge.is_active;
    new bootstrap.Modal(document.getElementById('editBadgeModal')).show();
}
</script>
@endsection
