@extends('layouts.app')

@section('title', 'Edit Course - EPAS-E')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item active">Edit: {{ $course->course_name }}</li>
        </ol>
    </nav>

    <div class="cb-container--simple">
        <form method="POST" action="{{ route('courses.update', $course) }}">
            @csrf
            @method('PUT')

            <div class="cb-main">
                <div class="cb-header cb-header--course">
                    <h4><i class="fas fa-edit me-2"></i>Edit Course</h4>
                    <p>{{ $course->course_name }}</p>
                </div>

                <div class="cb-body">
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Course Details</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Course Name <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('course_name') is-invalid @enderror"
                                           name="course_name" value="{{ old('course_name', $course->course_name) }}" required>
                                    @error('course_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Course Code <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('course_code') is-invalid @enderror"
                                           name="course_code" value="{{ old('course_code', $course->course_code) }}" required>
                                    @error('course_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Sector <span class="optional">(optional)</span></label>
                                    <input type="text" class="form-control @error('sector') is-invalid @enderror"
                                           name="sector" value="{{ old('sector', $course->sector) }}">
                                    @error('sector')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                @if(auth()->user()->role === 'admin' && isset($instructors))
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Assigned Instructor <span class="optional">(optional)</span></label>
                                    <select class="form-select @error('instructor_id') is-invalid @enderror" name="instructor_id">
                                        <option value="">-- No Instructor Assigned --</option>
                                        @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->full_name }} ({{ $instructor->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="cb-field-hint">Only the assigned instructor can edit this course and its modules.</div>
                                </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          name="description" rows="4">{{ old('description', $course->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-cog"></i> Settings</div>
                        <div class="cb-settings">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Course</label>
                            </div>
                        </div>
                    </div>

                    @if(in_array(auth()->user()->role, ['admin', 'instructor']) && $course->modules->isEmpty())
                    <div class="cb-section">
                        <div class="cb-section__title" style="color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Danger Zone</div>
                        <div class="cb-settings" style="border: 1px solid #fecaca; background: #fef2f2;">
                            <p class="mb-2" style="font-size: 0.85rem;">This course has no modules. You can safely delete it if needed.</p>
                            <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this course? This action cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i>Delete Course
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="cb-footer">
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Course
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
