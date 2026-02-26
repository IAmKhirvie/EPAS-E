@extends('layouts.app')

@section('title', 'Create New Module - EPAS-E')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item active">Create New Module</li>
        </ol>
    </nav>

    <div class="cb-container--simple">
        <form method="POST" action="{{ route('courses.modules.store', $course) }}">
            @csrf

            <div class="cb-main">
                <div class="cb-header cb-header--module">
                    <h4><i class="fas fa-book me-2"></i>Create New Learning Module</h4>
                    <p>Set up a competency-based learning module</p>
                </div>

                <div class="cb-body">
                    {{-- Course Selection --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-graduation-cap"></i> Course Assignment</div>
                        <div class="mb-3">
                            <label class="cb-field-label">Select Course <span class="required">*</span></label>
                            <select class="form-select @error('course_id') is-invalid @enderror" name="course_id" required>
                                <option value="">Select a Course</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }} ({{ $course->course_code }})
                                </option>
                                @endforeach
                            </select>
                            @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Module Identity --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-id-card"></i> Module Identity</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Qualification Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('qualification_title') is-invalid @enderror"
                                           name="qualification_title" value="{{ old('qualification_title', 'Electronic Products Assembly And Servicing NCII') }}" required>
                                    @error('qualification_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Unit of Competency <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('unit_of_competency') is-invalid @enderror"
                                           name="unit_of_competency" value="{{ old('unit_of_competency', 'Assemble Electronic Products') }}" required>
                                    @error('unit_of_competency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Module Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('module_title') is-invalid @enderror"
                                           name="module_title" value="{{ old('module_title', 'Assembling Electronic Products') }}" required>
                                    @error('module_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Module Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('module_number') is-invalid @enderror"
                                           name="module_number" value="{{ old('module_number') }}" placeholder="e.g., Module 1" required>
                                    @error('module_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div>
                                <label class="cb-field-label">Module Name <span class="required">*</span></label>
                                <input type="text" class="form-control @error('module_name') is-invalid @enderror"
                                       name="module_name" value="{{ old('module_name', 'Competency based learning material') }}" required>
                                @error('module_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-file-alt"></i> Module Content</div>
                        <div class="mb-3">
                            <label class="cb-field-label">Table of Contents <span class="optional">(optional)</span></label>
                            <textarea class="form-control @error('table_of_contents') is-invalid @enderror"
                                      name="table_of_contents" rows="6" placeholder="Enter the table of contents with page numbers...">{{ old('table_of_contents') }}</textarea>
                            @error('table_of_contents')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="cb-field-label">How to Use CBLM <span class="optional">(optional)</span></label>
                            <textarea class="form-control @error('how_to_use_cblm') is-invalid @enderror"
                                      name="how_to_use_cblm" rows="4">{{ old('how_to_use_cblm') }}</textarea>
                            @error('how_to_use_cblm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="cb-field-label">Introduction <span class="optional">(optional)</span></label>
                            <textarea class="form-control @error('introduction') is-invalid @enderror"
                                      name="introduction" rows="4">{{ old('introduction') }}</textarea>
                            @error('introduction')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="cb-field-label">Learning Outcomes <span class="optional">(optional)</span></label>
                            <textarea class="form-control @error('learning_outcomes') is-invalid @enderror"
                                      name="learning_outcomes" rows="4">{{ old('learning_outcomes') }}</textarea>
                            @error('learning_outcomes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Module
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
