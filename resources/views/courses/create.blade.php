@extends('layouts.app')

@section('title', 'Create New Course - EPAS-E')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create New Course</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('courses.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_name" class="form-label required">Course Name</label>
                                    <input type="text" 
                                           class="form-control @error('course_name') is-invalid @enderror" 
                                           id="course_name" 
                                           name="course_name" 
                                           value="{{ old('course_name') }}" 
                                           placeholder="e.g., Electronic Products Assembly and Servicing"
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
                                           value="{{ old('course_code') }}" 
                                           placeholder="e.g., EPAS-NCII"
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
                                           value="{{ old('sector') }}"
                                           placeholder="e.g., Electronics Sector">
                                    @error('sector')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @if(isset($instructors))
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instructor_id" class="form-label">Assign Instructor</label>
                                    <select class="form-select @error('instructor_id') is-invalid @enderror"
                                            id="instructor_id"
                                            name="instructor_id">
                                        <option value="">-- No Instructor Assigned --</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
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
                                      rows="4" 
                                      placeholder="Enter course description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate course code from course name
    const courseNameInput = document.getElementById('course_name');
    const courseCodeInput = document.getElementById('course_code');

    courseNameInput.addEventListener('blur', function() {
        if (!courseCodeInput.value) {
            const courseName = courseNameInput.value;
            const code = courseName
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, ' ')
                .trim()
                .replace(/\s+/g, '-')
                .substring(0, 20);
            
            if (code) {
                courseCodeInput.value = code;
            }
        }
    });
});
</script>
@endpush