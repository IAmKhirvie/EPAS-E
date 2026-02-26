@extends('layouts.app')

@section('title', 'Courses - EPAS-E')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">EPAS-E Courses</h1>
                @if(in_array(auth()->user()->role, ['admin', 'instructor']))
                    <a href="{{ route('courses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Course
                    </a>
                @endif
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Available Courses</h5>
                </div>
                @if(Request::get('manage'))
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        You are in management mode. Click "Back to Normal View" to return to the student view.
                        <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-info ms-3">Back to Normal View</a>
                    </div>
                @endif
                <div class="card-body">
                    @if($courses->count() > 0)
                        <div class="row">
                            @foreach($courses as $course)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card course-card h-100">
                                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $course->course_code }}</h6>
                                            <span class="badge bg-light text-dark">
                                                {{ $course->modules_count }} {{ Str::plural('Module', $course->modules_count) }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $course->course_name }}</h5>
                                            @if($course->sector)
                                                <p class="card-text text-muted small mb-2">
                                                    <i class="fas fa-industry me-1"></i>{{ $course->sector }}
                                                </p>
                                            @endif
                                            @if($course->description)
                                                <p class="card-text">{{ Str::limit($course->description, 120) }}</p>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View Course
                                                </a>
                                                @if(in_array(auth()->user()->role, ['admin', 'instructor']))
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('courses.edit', $course) }}">
                                                                    <i class="fas fa-edit me-2"></i>Edit Course
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('courses.modules.create', $course) }}">
                                                                    <i class="fas fa-plus me-2"></i>Add Module
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this course? All modules and content will be lost.')">
                                                                        <i class="fas fa-trash me-2"></i>Delete Course
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
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h4>No Courses Available</h4>
                            <p class="text-muted">No learning courses have been created yet.</p>
                            @if(in_array(auth()->user()->role, ['admin', 'instructor']))
                                <a href="{{ route('courses.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create First Course
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
.course-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid var(--border);
}

.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.course-card .card-header {
    border-bottom: 2px solid var(--primary);
}
</style>
@endpush