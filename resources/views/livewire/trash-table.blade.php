<div wire:init="loadData">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h4 class="mb-1">Trash</h4>
            <p class="text-muted small mb-0">Deleted items can be restored or permanently removed.</p>
        </div>
    </div>

    {{-- Filter Counts Summary --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <span class="badge bg-secondary">Total: {{ $counts['all'] ?? 0 }}</span>
        @if(($counts['module'] ?? 0) > 0)
            <span class="badge bg-primary">Modules: {{ $counts['module'] }}</span>
        @endif
        @if(($counts['topic'] ?? 0) > 0)
            <span class="badge bg-info">Topics: {{ $counts['topic'] }}</span>
        @endif
        @if(($counts['information_sheet'] ?? 0) > 0)
            <span class="badge bg-success">Info Sheets: {{ $counts['information_sheet'] }}</span>
        @endif
        @if(($counts['homework'] ?? 0) > 0)
            <span class="badge bg-warning text-dark">Homework: {{ $counts['homework'] }}</span>
        @endif
        @if($isAdmin && ($counts['course'] ?? 0) > 0)
            <span class="badge bg-dark">Courses: {{ $counts['course'] }}</span>
        @endif
    </div>

    {{-- Type Filter Tabs --}}
    <ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto" style="scrollbar-width: thin;">
        <li class="nav-item">
            <button wire:click="$set('typeFilter', 'all')" class="nav-link {{ $typeFilter === 'all' ? 'active' : '' }}">
                All <span class="badge bg-secondary ms-1">{{ $counts['all'] ?? 0 }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="$set('typeFilter', 'module')" class="nav-link {{ $typeFilter === 'module' ? 'active' : '' }}">
                Modules <span class="badge bg-primary ms-1">{{ $counts['module'] ?? 0 }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="$set('typeFilter', 'topic')" class="nav-link {{ $typeFilter === 'topic' ? 'active' : '' }}">
                Topics <span class="badge bg-info ms-1">{{ $counts['topic'] ?? 0 }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="$set('typeFilter', 'information_sheet')" class="nav-link {{ $typeFilter === 'information_sheet' ? 'active' : '' }}">
                Info Sheets <span class="badge bg-success ms-1">{{ $counts['information_sheet'] ?? 0 }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="$set('typeFilter', 'homework')" class="nav-link {{ $typeFilter === 'homework' ? 'active' : '' }}">
                Homework <span class="badge bg-warning text-dark ms-1">{{ $counts['homework'] ?? 0 }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="$set('typeFilter', 'self_check')" class="nav-link {{ $typeFilter === 'self_check' ? 'active' : '' }}">
                Self Checks <span class="badge bg-danger ms-1">{{ $counts['self_check'] ?? 0 }}</span>
            </button>
        </li>
        @if($isAdmin && isset($counts['course']))
        <li class="nav-item">
            <button wire:click="$set('typeFilter', 'course')" class="nav-link {{ $typeFilter === 'course' ? 'active' : '' }}">
                Courses <span class="badge bg-dark ms-1">{{ $counts['course'] ?? 0 }}</span>
            </button>
        </li>
        @endif
        <li class="nav-item">
            <button wire:click="$set('typeFilter', 'announcement')" class="nav-link {{ $typeFilter === 'announcement' ? 'active' : '' }}">
                Announcements <span class="badge bg-info ms-1">{{ $counts['announcement'] ?? 0 }}</span>
            </button>
        </li>
    </ul>

    {{-- Search --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <div class="flex-grow-1" style="min-width: 200px;">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm"
                placeholder="Search by name...">
        </div>
    </div>

    {{-- Bulk Actions --}}
    @if(count($selectedItems) > 0)
        <div class="d-flex flex-wrap gap-2 mb-3 p-2 rounded border bulk-action-bar">
            <span class="align-self-center text-muted small">{{ count($selectedItems) }} selected:</span>
            <button wire:click="bulkRestore" wire:confirm="Restore selected items? They will be moved back to their original location." class="btn btn-success btn-sm">
                <i class="fas fa-undo me-1"></i> Restore
            </button>
            <button wire:click="bulkForceDelete" wire:confirm="PERMANENTLY delete selected items? This action cannot be undone!" class="btn btn-danger btn-sm">
                <i class="fas fa-trash me-1"></i> Delete Forever
            </button>
        </div>
    @endif

    {{-- Loading --}}
    <div wire:loading class="text-center py-2">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    @if(!$readyToLoad)
    <div class="p-3">
        <x-skeleton type="table-row" :count="8" />
    </div>
    @else

    {{-- Table --}}
    <div class="table-responsive" wire:loading.class="opacity-50">
        <table class="table table-hover table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                    </th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Parent</th>
                    <th style="cursor:pointer;" wire:click="toggleSort">
                        Deleted
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <input type="checkbox" wire:model.live="selectedItems" value="{{ $item['unique_key'] }}" class="form-check-input">
                        </td>
                        <td>
                            @php
                                $typeColors = [
                                    'module' => 'primary',
                                    'topic' => 'info',
                                    'information_sheet' => 'success',
                                    'homework' => 'warning',
                                    'self_check' => 'danger',
                                    'task_sheet' => 'secondary',
                                    'job_sheet' => 'dark',
                                    'checklist' => 'secondary',
                                    'course' => 'dark',
                                    'announcement' => 'info',
                                ];
                                $color = $typeColors[$item['type']] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} {{ $color === 'warning' ? 'text-dark' : '' }}">
                                {{ $item['type_label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-medium">{{ Str::limit($item['name'], 50) }}</div>
                        </td>
                        <td>
                            @if($item['parent_name'])
                                <small class="text-muted">{{ Str::limit($item['parent_name'], 30) }}</small>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted" title="{{ $item['deleted_at']->format('M d, Y h:i A') }}">
                                {{ $item['deleted_at']->diffForHumans() }}
                            </small>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button wire:click="restoreItem('{{ $item['type'] }}', {{ $item['id'] }})"
                                        wire:confirm="Restore this {{ $item['type_label'] }}?"
                                        class="btn btn-outline-success btn-sm" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button wire:click="forceDeleteItem('{{ $item['type'] }}', {{ $item['id'] }})"
                                        wire:confirm="PERMANENTLY delete this {{ $item['type_label'] }}? This cannot be undone!"
                                        class="btn btn-outline-danger btn-sm" title="Delete Forever">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-trash-alt fa-3x mb-3 d-block opacity-25"></i>
                            <p class="mb-1">Trash is empty</p>
                            <small>Deleted items will appear here for recovery.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($total > $perPage)
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">
            Showing {{ $from }}-{{ $to }} of {{ $total }}
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous --}}
                <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                    <button wire:click="previousPage" class="page-link" {{ $currentPage <= 1 ? 'disabled' : '' }}>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </li>

                {{-- Page numbers --}}
                @for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
                    <li class="page-item {{ $i === $currentPage ? 'active' : '' }}">
                        <button wire:click="gotoPage({{ $i }})" class="page-link">{{ $i }}</button>
                    </li>
                @endfor

                {{-- Next --}}
                <li class="page-item {{ $currentPage >= $lastPage ? 'disabled' : '' }}">
                    <button wire:click="nextPage" class="page-link" {{ $currentPage >= $lastPage ? 'disabled' : '' }}>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </li>
            </ul>
        </nav>
    </div>
    @else
    <div class="mt-3">
        <small class="text-muted">
            Showing {{ $from }}-{{ $to }} of {{ $total }}
        </small>
    </div>
    @endif
    @endif

    <style>
    /* Bulk action bar styling */
    .bulk-action-bar {
        background-color: var(--light, #f8f9fa);
        border-color: var(--border, #e0e0e0) !important;
    }

    .dark-mode .bulk-action-bar {
        background-color: var(--light-gray) !important;
        border-color: var(--border) !important;
    }
    </style>
</div>
