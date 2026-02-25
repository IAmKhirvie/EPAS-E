@extends('layouts.app')

@section('title', 'Create Information Sheet - EPAS-E')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item">{{ $module->course->course_name }}</li>
            <li class="breadcrumb-item">Module {{ $module->module_number }}</li>
            <li class="breadcrumb-item active">Create Information Sheet</li>
        </ol>
    </nav>

    <div class="cb-container--simple">
        <form action="{{ route('information-sheets.store', $module) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="cb-main">
                <div class="cb-header cb-header--info-sheet">
                    <h4><i class="fas fa-file-alt me-2"></i>Create Information Sheet</h4>
                    <p>Add a new information sheet to Module {{ $module->module_number }}</p>
                </div>

                <div class="cb-body">
                    {{-- Context --}}
                    <div class="cb-context-badge">
                        <i class="fas fa-book"></i>
                        <span>Module: <strong>{{ $module->module_number }} &mdash; {{ $module->module_name }}</strong></span>
                    </div>

                    {{-- Sheet Details --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Sheet Details</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="cb-field-label">Sheet Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('sheet_number') is-invalid @enderror"
                                           name="sheet_number" value="{{ old('sheet_number') }}"
                                           placeholder="e.g., 1.1, 1.2, 2.1" required>
                                    @error('sheet_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="cb-field-hint">Format: ModuleNumber.SheetNumber (e.g., 1.1)</div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           name="title" value="{{ old('title') }}"
                                           placeholder="e.g., Introduction to Electronics and Electricity" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Document Upload --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-upload"></i> Document Upload</div>
                        <div class="cb-upload-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <small>PDF, Word, Excel, PowerPoint (max 10MB)</small>
                            <input type="file" class="form-control mt-2 @error('file') is-invalid @enderror"
                                   name="file" accept=".pdf,.xlsx,.xls,.doc,.docx,.ppt,.pptx">
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-align-left"></i> Content</div>
                        <div class="mb-3">
                            <label class="cb-field-label">Content <span class="optional">(optional)</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      name="content" rows="10"
                                      placeholder="Enter the main content for this information sheet...">{{ old('content') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Settings --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-cog"></i> Settings</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="cb-field-label">Display Order</label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror"
                                           name="order" value="{{ old('order', $nextOrder) }}" min="0">
                                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="cb-field-hint">Lower numbers appear first</div>
                                </div>
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
                            <i class="fas fa-save me-1"></i>Create Information Sheet
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
