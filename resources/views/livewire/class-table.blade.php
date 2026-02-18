<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="mb-0">
            Class Management
            @if($sectionFilter)
                <span class="text-muted"> / {{ $sectionFilter }}</span>
            @endif
        </h4>
    </div>

    {{-- No Advisory Section Warning --}}
    @if(isset($noAdvisorySection) && $noAdvisorySection)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            You are not assigned to any section. Please contact an administrator.
        </div>
        @return
    @endif

    {{-- Search --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <div class="flex-grow-1" style="min-width: 200px;">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm"
                placeholder="Search student name, ID...">
        </div>
        @if($sectionFilter)
            <button wire:click="clearSection" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> All Sections
            </button>
        @endif
    </div>

    {{-- Loading --}}
    <div wire:loading class="text-center py-2">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div wire:loading.class="opacity-50">
        @if($sectionFilter && $students)
            {{-- Section Detail: Student Table --}}
            <div class="mb-3">
                @php
                    $advisers = $advisersBySection->get($sectionFilter, collect());
                @endphp
                @if($advisers->isNotEmpty())
                    <small class="text-muted">
                        <i class="fas fa-chalkboard-teacher me-1"></i>
                        Adviser(s): {{ $advisers->map(fn($a) => $a->full_name)->join(', ') }}
                    </small>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="cursor:pointer;" wire:click="sortBy('student_id')">
                                Student ID
                                @if($sortField === 'student_id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th style="cursor:pointer;" wire:click="sortBy('last_name')">
                                Name
                                @if($sortField === 'last_name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th style="cursor:pointer;" wire:click="sortBy('email')">
                                Email
                                @if($sortField === 'email')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th>Department</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->student_id ?? '-' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $student->profile_image_url }}" alt="" class="rounded-circle" width="28" height="28">
                                        <span class="fw-medium">{{ $student->full_name }}</span>
                                    </div>
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->department?->name ?? '-' }}</td>
                                <td>
                                    @if((int) $student->stat === 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No students found in this section.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($students->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        Showing {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} of {{ $students->total() }}
                    </small>
                    {{ $students->links() }}
                </div>
            @endif
        @else
            {{-- Section Cards --}}
            <div class="row g-3">
                @forelse($allSections as $section)
                    @php
                        $sectionStudents = $studentsBySection->get($section, collect());
                        $advisers = $advisersBySection->get($section, collect());
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100" style="cursor: pointer;" wire:click="selectSection('{{ $section }}')">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $section }}</h5>
                                    <span class="badge bg-primary">{{ $sectionStudents->count() }} students</span>
                                </div>
                                @if($advisers->isNotEmpty())
                                    <small class="text-muted d-block mb-2">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>
                                        {{ $advisers->map(fn($a) => $a->full_name)->join(', ') }}
                                    </small>
                                @else
                                    <small class="text-muted d-block mb-2">
                                        <i class="fas fa-exclamation-circle me-1"></i> No adviser assigned
                                    </small>
                                @endif
                                <div class="d-flex gap-2">
                                    <small class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ $sectionStudents->where('stat', 1)->count() }} active
                                    </small>
                                    <small class="text-warning">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $sectionStudents->where('stat', 0)->count() }} pending
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="fas fa-school fa-3x mb-3 opacity-50"></i>
                        <p>No sections found.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>
