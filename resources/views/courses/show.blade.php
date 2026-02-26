@extends('layouts.app')

@section('title', $course->course_name . ' - EPAS-E')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <x-back-button :route="route('courses.index')" label="Back to Courses" />
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $course->course_name }}</h1>
                    <p class="text-muted mb-0">{{ $course->course_code }}</p>
                    @if($course->instructor)
                    <small class="text-muted">
                        <i class="fas fa-chalkboard-teacher me-1"></i>Instructor: {{ $course->instructor->full_name }}
                    </small>
                    @endif
                </div>
                @if(isset($canEdit) && $canEdit)
                <div>
                    <a href="{{ route('courses.modules.create', $course) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Module
                    </a>
                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-edit me-2"></i>Edit Course
                    </a>
                </div>
                @endif
            </div>

            <!-- Course Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Course Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($course->sector)
                        <div class="col-md-6 mb-3">
                            <strong>Sector:</strong>
                            <span class="text-muted">{{ $course->sector }}</span>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <strong>Course Code:</strong>
                            <span class="text-muted">{{ $course->course_code }}</span>
                        </div>
                        @if($course->description)
                        <div class="col-12 mb-3">
                            <strong>Description:</strong>
                            <p class="text-muted mb-0">{{ $course->description }}</p>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong>
                            <span class="badge {{ $course->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $course->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Assigned Instructor:</strong>
                            @if($course->instructor)
                            <span class="text-muted">{{ $course->instructor->full_name }}</span>
                            @else
                            <span class="text-muted">Not assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modules Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>Course Modules
                        <span class="badge bg-primary ms-2">{{ $course->modules->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($course->modules->count() > 0)
                    <div class="row">
                        @foreach($course->modules as $module)
                        <div class="col-md-6 mb-4">
                            <div class="card module-card h-100">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">{{ $module->module_number }}</h6>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $module->module_name }}</h5>
                                    <p class="card-text text-muted">{{ $module->module_title }}</p>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>Qualification Title:</strong> {{ $module->qualification_title }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <strong>Unit of Competency:</strong> {{ $module->unit_of_competency }}
                                        </small>
                                    </div>
                                    <div class="module-stats">
                                        <small class="text-muted">
                                            <i class="fas fa-file-alt me-1"></i>
                                            {{ $module->informationSheets->count() }} {{ Str::plural('Information Sheet', $module->informationSheets->count()) }}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('courses.modules.show', [$course, $module, $module->slug]) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View Module
                                            </a>
                                            <a href="{{ route('courses.modules.print', [$course, $module]) }}" class="btn btn-outline-secondary btn-sm" target="_blank" title="Print Preview">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="{{ route('courses.modules.download', [$course, $module]) }}" class="btn btn-outline-success btn-sm" title="Download for Offline">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                        @if(in_array(auth()->user()->role, ['admin', 'instructor']))
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('courses.modules.edit', [$course, $module]) }}">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('courses.modules.sheets.create', [$course, $module]) }}">
                                                        <i class="fas fa-file-alt me-2"></i>Add Content
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('courses.modules.destroy', [$course, $module]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure? This will delete all associated information sheets and content.')">
                                                            <i class="fas fa-trash me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-2x text-muted mb-3"></i>
                        <h5>No Modules Created</h5>
                        <p class="text-muted">This course doesn't have any modules yet.</p>
                        @if(in_array(auth()->user()->role, ['admin', 'instructor']))
                        <a href="{{ route('courses.modules.create', $course) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Module
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .module-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 1px solid var(--border);
    }

    .module-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .module-stats {
        border-top: 1px solid #f0f0f0;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }
</style>
@endpush