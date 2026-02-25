@extends('layouts.app')

@section('title', 'Create New Course - EPAS-E')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item active">Create New Course</li>
        </ol>
    </nav>

    <div class="cb-container--simple">
        <form method="POST" action="{{ route('courses.store') }}">
            @csrf

            <div class="cb-main">
                <div class="cb-header cb-header--course">
                    <h4><i class="fas fa-graduation-cap me-2"></i>Create New Course</h4>
                    <p>Set up a new course with instructor assignment</p>
                </div>

                <div class="cb-body">
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Course Details</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Course Name <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('course_name') is-invalid @enderror"
                                           id="course_name" name="course_name" value="{{ old('course_name') }}"
                                           placeholder="e.g., Electronic Products Assembly and Servicing" required>
                                    @error('course_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Course Code <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('course_code') is-invalid @enderror"
                                           id="course_code" name="course_code" value="{{ old('course_code') }}"
                                           placeholder="e.g., EPAS-NCII" required>
                                    @error('course_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Sector <span class="optional">(optional)</span></label>
                                    <input type="text" class="form-control @error('sector') is-invalid @enderror"
                                           name="sector" value="{{ old('sector') }}" placeholder="e.g., Electronics Sector">
                                    @error('sector')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                @if(isset($instructors))
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Assign Instructor <span class="optional">(optional)</span></label>
                                    <select class="form-select @error('instructor_id') is-invalid @enderror" name="instructor_id">
                                        <option value="">-- No Instructor Assigned --</option>
                                        @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->full_name }} ({{ $instructor->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="cb-field-hint">Only the assigned instructor can edit this course and its modules.</div>
                                </div>
                                @endif
                            </div>
                            <div>
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          name="description" rows="4" placeholder="Enter course description...">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Course
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseNameInput = document.getElementById('course_name');
    const courseCodeInput = document.getElementById('course_code');
    courseNameInput.addEventListener('blur', function() {
        if (!courseCodeInput.value) {
            const code = courseNameInput.value.toUpperCase().replace(/[^A-Z0-9]/g, ' ').trim().replace(/\s+/g, '-').substring(0, 20);
            if (code) courseCodeInput.value = code;
        }
    });
});
</script>
@endpush
@endsection
