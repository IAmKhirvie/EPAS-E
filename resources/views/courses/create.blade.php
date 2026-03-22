@extends('layouts.app')

@section('title', 'Create New Course - EPAS-E')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Courses</a></li>
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
                                    <label class="cb-field-label">Assign Instructor <span class="required">(Required)</span></label>
                                    <select class="form-select @error('instructor_id') is-invalid @enderror"
                                        name="instructor_id"
                                        id="instructor_select" required>
                                        <option value="" disabled selected>-- Select an Instructor --</option>
                                        @foreach($instructors->sortBy(fn($i) => $i->last_name . $i->first_name) as $instructor)
                                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->last_name }}, {{ $instructor->first_name }} ({{ $instructor->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="cb-field-hint">Only the assigned instructor can edit this course and its modules.</div>
                                </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="cb-field-label">Target Sections <span class="optional">(optional — leave empty for all sections)</span></label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($sections as $sec)
                                    <div class="form-check">
                                        <input class="form-check-input section-checkbox" type="checkbox" value="{{ $sec }}" id="sec_{{ $sec }}"
                                            {{ in_array($sec, old('target_sections') ? explode(',', old('target_sections')) : []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sec_{{ $sec }}">{{ $sec }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="target_sections" id="target_sections_input" value="{{ old('target_sections') }}">
                                <div class="cb-field-hint">Select which sections can see this course. Leave all unchecked for all sections.</div>
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
                    <a href="{{ route('content.management') }}" class="btn btn-outline-secondary">
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

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Tom Select - light/dark mode support */
    .ts-wrapper .ts-control {
        background-color: var(--cb-surface, #fff);
        border-color: var(--cb-border, #dee2e6);
        color: var(--cb-text, #212529);
    }

    .ts-wrapper .ts-dropdown {
        background-color: var(--cb-surface, #fff);
        border-color: var(--cb-border, #dee2e6);
        color: var(--cb-text, #212529);
    }

    .ts-wrapper .ts-dropdown .option {
        color: var(--cb-text, #212529);
    }

    .ts-wrapper .ts-dropdown .option:hover,
    .ts-wrapper .ts-dropdown .option.active {
        background-color: var(--cb-surface-alt, #f8f9fa);
        color: var(--cb-text, #212529);
    }

    .ts-wrapper .ts-dropdown input {
        background-color: var(--cb-surface, #fff);
        color: var(--cb-text, #212529);
    }

    /* Dark mode */
    .dark-mode .ts-wrapper .ts-control {
        background-color: var(--card-bg);
        border-color: var(--border);
        color: var(--text-primary);
    }

    .dark-mode .ts-wrapper .ts-dropdown {
        background-color: var(--card-bg);
        border-color: var(--border);
        color: var(--text-primary);
    }

    .dark-mode .ts-wrapper .ts-dropdown .option {
        color: var(--text-primary);
    }

    .dark-mode .ts-wrapper .ts-dropdown .option:hover,
    .dark-mode .ts-wrapper .ts-dropdown .option.active {
        background-color: var(--light-gray);
        color: var(--text-primary);
    }

    .dark-mode .ts-wrapper .ts-dropdown input {
        background-color: var(--card-bg);
        color: var(--text-primary);
    }
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tom Select for instructor dropdown
        if (document.getElementById('instructor_select')) {
            new TomSelect('#instructor_select', {
                mode: 'single',
                allowEmptyOption: true,
                placeholder: 'Search instructor by name or email...',
                sortField: {
                    field: 'text',
                    direction: 'asc'
                },
                maxOptions: 500,
                onInitialize: function() {
                    // Hide the disabled placeholder from the dropdown list
                    const emptyOption = this.options[''];
                    if (emptyOption) {
                        emptyOption.disabled = true;
                    }
                },
            });
        }

        // Course code auto-fill
        const courseNameInput = document.getElementById('course_name');
        const courseCodeInput = document.getElementById('course_code');
        courseNameInput.addEventListener('blur', function() {
            if (!courseCodeInput.value) {
                const code = courseNameInput.value.toUpperCase().replace(/[^A-Z0-9]/g, ' ').trim().replace(/\s+/g, '-').substring(0, 20);
                if (code) courseCodeInput.value = code;
            }
        });

        // Sync section checkboxes with hidden input
        const sectionCheckboxes = document.querySelectorAll('.section-checkbox');
        const hiddenInput = document.getElementById('target_sections_input');

        function syncSections() {
            const checked = [...sectionCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
            hiddenInput.value = checked.join(',');
        }
        sectionCheckboxes.forEach(cb => cb.addEventListener('change', syncSections));
    });
</script>
@endpush
@endsection