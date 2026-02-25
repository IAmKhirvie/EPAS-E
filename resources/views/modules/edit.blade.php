@extends('layouts.app')

@section('title', 'Edit Module - EPAS-E')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item active">Edit: {{ $module->module_number }}</li>
        </ol>
    </nav>

    <div class="cb-container--simple">
        <form method="POST" action="{{ route('modules.update', $module) }}">
            @csrf
            @method('PUT')

            <div class="cb-main">
                <div class="cb-header cb-header--module">
                    <h4><i class="fas fa-edit me-2"></i>Edit Learning Module</h4>
                    <p>{{ $module->module_number }}: {{ $module->module_name }}</p>
                </div>

                <div class="cb-body">
                    {{-- Module Identity --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-id-card"></i> Module Identity</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Qualification Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('qualification_title') is-invalid @enderror"
                                           name="qualification_title" value="{{ old('qualification_title', $module->qualification_title) }}" required>
                                    @error('qualification_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Unit of Competency <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('unit_of_competency') is-invalid @enderror"
                                           name="unit_of_competency" value="{{ old('unit_of_competency', $module->unit_of_competency) }}" required>
                                    @error('unit_of_competency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Module Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('module_title') is-invalid @enderror"
                                           name="module_title" value="{{ old('module_title', $module->module_title) }}" required>
                                    @error('module_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Module Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('module_number') is-invalid @enderror"
                                           name="module_number" value="{{ old('module_number', $module->module_number) }}" required>
                                    @error('module_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div>
                                <label class="cb-field-label">Module Name <span class="required">*</span></label>
                                <input type="text" class="form-control @error('module_name') is-invalid @enderror"
                                       name="module_name" value="{{ old('module_name', $module->module_name) }}" required>
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
                                      name="table_of_contents" rows="6">{{ old('table_of_contents', $module->table_of_contents) }}</textarea>
                            @error('table_of_contents')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="cb-field-label">How to Use CBLM <span class="optional">(optional)</span></label>
                            <textarea class="form-control @error('how_to_use_cblm') is-invalid @enderror"
                                      name="how_to_use_cblm" rows="4">{{ old('how_to_use_cblm', $module->how_to_use_cblm) }}</textarea>
                            @error('how_to_use_cblm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="cb-field-label">Introduction <span class="optional">(optional)</span></label>
                            <textarea class="form-control @error('introduction') is-invalid @enderror"
                                      name="introduction" rows="4">{{ old('introduction', $module->introduction) }}</textarea>
                            @error('introduction')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="cb-field-label">Learning Outcomes <span class="optional">(optional)</span></label>
                            <textarea class="form-control @error('learning_outcomes') is-invalid @enderror"
                                      name="learning_outcomes" rows="4">{{ old('learning_outcomes', $module->learning_outcomes) }}</textarea>
                            @error('learning_outcomes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Settings --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-cog"></i> Settings</div>
                        <div class="cb-settings">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $module->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Module</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('modules.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Module
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
