@extends('layouts.app')

@section('title', 'Edit Course - EPAS-E')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Course: {{ $course->course_name }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('courses.update', $course) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_name" class="form-label required">Course Name</label>
                                    <input type="text"
                                        class="form-control @error('course_name') is-invalid @enderror"
                                        id="course_name"
                                        name="course_name"
                                        value="{{ old('course_name', $course->course_name) }}"
                                        required>
                                    @error('course_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_code" class="form-label required">Course Code</label>
                                    <input type="text"
                                        class="form-control @error('course_code') is-invalid @enderror"
                                        id="course_code"
                                        name="course_code"
                                        value="{{ old('course_code', $course->course_code) }}"
                                        required>
                                    @error('course_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sector" class="form-label">Sector</label>
                                    <input type="text"
                                        class="form-control @error('sector') is-invalid @enderror"
                                        id="sector"
                                        name="sector"
                                        value="{{ old('sector', $course->sector) }}">
                                    @error('sector')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @if(auth()->user()->role === 'admin' && isset($instructors))
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instructor_id" class="form-label">Assigned Instructor</label>
                                    <select class="form-select @error('instructor_id') is-invalid @enderror"
                                        id="instructor_id"
                                        name="instructor_id">
                                        <option value="">-- No Instructor Assigned --</option>
                                        @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->full_name }} ({{ $instructor->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Only the assigned instructor can edit this course and its modules.</small>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Course Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description"
                                name="description"
                                rows="4">{{ old('description', $course->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox"
                                class="form-check-input"
                                id="is_active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active Course</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary">Back to Course</a>
                            <div>
                                <button type="submit" class="btn btn-primary">Update Course</button>
                            </div>
                        </div>
                    </form>

                    @if(in_array(auth()->user()->role, ['admin', 'instructor']) && $course->modules->isEmpty())
                    <hr class="my-4">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                        </h6>
                        <p class="mb-2">This course has no modules. You can safely delete it if needed.</p>
                        <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this course? This action cannot be undone.')">
                                <i class="fas fa-trash me-1"></i>Delete Course
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection