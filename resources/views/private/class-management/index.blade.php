@extends('layouts.app')

@section('title', 'Class Management')

@section('content')
    <div class="content-area">
        {{-- Show message if instructor has no advisory section --}}
        @if(isset($noAdvisorySection) && $noAdvisorySection)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>No Advisory Section Assigned</strong>
                <p class="mb-0 mt-2">You have not been assigned to any section yet. Please contact an administrator to assign you to a class section.</p>
            </div>
        @else
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>
                @if($sectionFilter)
                    @if(isset($isInstructor) && $isInstructor)
                        My Class: {{ $sectionFilter }}
                    @else
                        Section: {{ $sectionFilter }}
                    @endif
                @else
                    Class Management
                @endif
            </h4>
            <div>
                <span class="badge bg-primary">Total Sections: {{ $allSections->count() }}</span>
                <span class="badge bg-info ms-2">
                    Total Students: {{ $sectionFilter ? ($students->total() ?? 0) : $studentsBySection->flatten()->count() }}
                </span>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('class-management.index') }}">
                    <div class="row">
                        <div class="{{ isset($isInstructor) && $isInstructor ? 'col-md-10' : 'col-md-6' }}">
                            <div class="search-container">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" class="form-control search-input"
                                       placeholder="Search students by name or student ID..."
                                       value="{{ $search }}"
                                       onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
                            </div>
                        </div>
                        @if(!isset($isInstructor) || !$isInstructor)
                        <div class="col-md-4">
                            <select class="form-select" name="section" onchange="this.form.submit()">
                                <option value="">All Sections</option>
                                @foreach($allSections as $section)
                                    <option value="{{ $section }}" {{ $sectionFilter == $section ? 'selected' : '' }}>
                                        {{ $section }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($sectionFilter)
            <!-- Table View for Specific Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Students in {{ $sectionFilter }}</h5>
                        @if($currentAdviser)
                            <small class="text-muted">
                                Adviser: 
                                <strong>{{ $currentAdviser->full_name }}</strong>
                                @if($currentAdviser->email)
                                    ({{ $currentAdviser->email }})
                                @endif
                            </small>
                        @else
                            <small class="text-muted">No adviser assigned</small>
                        @endif
                    </div>
                    <div>
                        @if(!isset($isInstructor) || !$isInstructor)
                        <button type="button" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#assignAdviserModal">
                            <i class="fas fa-user-plus me-1"></i> Assign Adviser
                        </button>
                        <a href="{{ route('class-management.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to All Sections
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>student</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Room</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                    <tr>
                                        <td>{{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}</td>
                                        <td>
                                            <img src="{{ $student->profile_image_url }}" alt="User Avatar" class="rounded-circle" width="32" height="32" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->initials) }}&background=007fc9&color=fff&size=32'">
                                        </td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->student_id ?? 'N/A' }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->department->name ?? 'N/A' }}</td>
                                        <td>{{ $student->room_number ?? 'N/A' }}</td>
                                        <td>
                                            @if($student->stat)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('private.users.edit', $student->id)}}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            @if($search)
                                                No students found in {{ $sectionFilter }} matching "{{ $search }}"
                                            @else
                                                No students found in {{ $sectionFilter }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($students->hasPages())
                        <div class="pagination-container mt-3">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
            </div>

        @else
            <!-- Grid View for All Sections -->
            <div class="row">
                @forelse($allSections as $section)
                    @php
                        $sectionAdviser = $advisersBySection[$section] ?? null;
                    @endphp
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="card section-card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $section }}</h6>
                                <span class="badge bg-light text-dark">{{ count($studentsBySection[$section] ?? []) }} students</span>
                            </div>
                            <div class="card-body">
                                <!-- Adviser Info -->
                                @if($sectionAdviser)
                                    <div class="adviser-info mb-3 p-2 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $sectionAdviser->profile_image_url }}" alt="Adviser Avatar" class="rounded-circle me-2" width="32" height="32" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($sectionAdviser->initials) }}&background=28a745&color=fff&size=32'">
                                            <div class="flex-grow-1">
                                                <div class="fw-medium small">{{ $sectionAdviser->full_name }}</div>
                                                <small class="text-muted">Section Adviser</small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="adviser-info mb-3 p-2 bg-warning bg-opacity-10 rounded">
                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            No adviser assigned
                                        </small>
                                    </div>
                                @endif

                                <!-- Student List -->
                                @if(isset($studentsBySection[$section]) && count($studentsBySection[$section]) > 0)
                                    <div class="student-list">
                                        @foreach($studentsBySection[$section]->take(5) as $student)
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="{{ $student->profile_image_url }}" alt="Avatar" class="rounded-circle me-2" width="32" height="32" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->initials) }}&background=007fc9&color=fff&size=32'">
                                                <div class="flex-grow-1">
                                                    <div class="fw-medium">{{ $student->full_name }}</div>
                                                    <small class="text-muted">{{ $student->student_id ?? 'No student' }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if(count($studentsBySection[$section]) > 5)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">+{{ count($studentsBySection[$section]) - 5 }} more students</small>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <p>No students in this section</p>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="{{ route('class-management.show', $section) }}" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="fas fa-eye me-1"></i> View Section
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                                <h5>No Class Sections Found</h5>
                                <p class="text-muted">There are no students assigned to sections yet.</p>
                                <a href="{{ route('private.students.index') }}" class="btn btn-primary">
                                    <i class="fas fa-user-graduate me-1"></i> Manage Students
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        @endif
        @endif {{-- End noAdvisorySection check --}}
    </div>

    <!-- Assign Adviser Modal (Admin Only) -->
    @if($sectionFilter && (!isset($isInstructor) || !$isInstructor))
    <div class="modal fade" id="assignAdviserModal" tabindex="-1" aria-labelledby="assignAdviserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignAdviserModalLabel">Assign Adviser to {{ $sectionFilter }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('class-management.assign-adviser', $sectionFilter) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="adviser_id" class="form-label">Select Adviser</label>
                            <select class="form-select" id="adviser_id" name="adviser_id" required>
                                <option value="">-- Choose an instructor --</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ $currentAdviser && $currentAdviser->id == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->full_name }} 
                                        @if($instructor->email)
                                            ({{ $instructor->email }})
                                        @endif
                                        @if($instructor->department)
                                            - {{ $instructor->department->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($currentAdviser)
                            <div class="alert alert-info">
                                <small>
                                    <strong>Current Adviser:</strong> {{ $currentAdviser->full_name }}
                                    @if($currentAdviser->email)
                                        ({{ $currentAdviser->email }})
                                    @endif
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign Adviser</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <style>
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .section-card {
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .section-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .student-list {
        max-height: 200px;
        overflow-y: auto;
    }

    .search-container {
        position: relative;
    }

    .search-container .fa-search {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }

    .search-container .search-input {
        padding-left: 40px;
    }

    .adviser-info {
        border-left: 3px solid #28a745;
    }
    </style>
@endsection