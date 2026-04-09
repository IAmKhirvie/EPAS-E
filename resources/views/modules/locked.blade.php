@extends('layouts.app')

@section('title', 'Module Locked - EPAS-E')

@section('content')
<div class="container-fluid py-3 bg-white border-bottom mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->course_name }}</a></li>
                    <li class="breadcrumb-item active">{{ $module->module_number }}</li>
                </ol>
            </nav>
            <h4 class="mb-1">
                <i class="fas fa-lock text-warning me-2"></i>
                {{ $module->module_number }}: {{ $module->module_name }}
            </h4>
            <p class="text-muted mb-0 small">{{ $module->qualification_title }}</p>
        </div>
        <div>
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Course
            </a>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-warning shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-5x text-warning"></i>
                    </div>
                    <h3 class="card-title mb-3">Module Locked</h3>
                    <p class="text-muted mb-4">
                        This module requires you to complete the following prerequisite(s) first:
                    </p>

                    <div class="list-group mb-4">
                        @foreach($unmetPrerequisites as $prereq)
                            <a href="{{ route('courses.modules.show', [$course, $prereq]) }}"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div class="text-start">
                                    <strong class="text-primary">{{ $prereq->module_number }}</strong>
                                    <span class="ms-2">{{ $prereq->module_title }}</span>
                                </div>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <p class="text-muted small mb-4">
                        <i class="fas fa-info-circle me-1"></i>
                        Complete all self-checks and assessments in the prerequisite modules to unlock this content.
                    </p>

                    <a href="{{ route('courses.show', $course) }}" class="btn btn-primary">
                        <i class="fas fa-book-open me-1"></i> View All Modules
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
