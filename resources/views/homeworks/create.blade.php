@extends('layouts.app')

@section('title', 'Create Homework')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item"><a href="{{ route('modules.show', $informationSheet->module_id) }}">{{ $informationSheet->module->module_name }}</a></li>
            <li class="breadcrumb-item active">Create Homework</li>
        </ol>
    </nav>

    <form action="{{ route('homeworks.store', $informationSheet) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="cb-container">
            {{-- Sidebar --}}
            <div class="cb-sidebar">
                <div class="cb-sidebar__title">Homework Guide</div>

                <div class="cb-sidebar__group">
                    <div class="cb-sidebar__group-label"><i class="fas fa-info-circle"></i> Sections</div>
                    <a href="#section-basic" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--blue"><i class="fas fa-pen"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Basic Info</span>
                            <span class="cb-sidebar__item-desc">Title and instructions</span>
                        </span>
                    </a>
                    <a href="#section-settings" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--orange"><i class="fas fa-cog"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Settings</span>
                            <span class="cb-sidebar__item-desc">Due date, points</span>
                        </span>
                    </a>
                    <a href="#section-requirements" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--green"><i class="fas fa-list-check"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Requirements</span>
                            <span class="cb-sidebar__item-desc">What to submit</span>
                        </span>
                    </a>
                    <a href="#section-guidelines" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--purple"><i class="fas fa-file-lines"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Guidelines</span>
                            <span class="cb-sidebar__item-desc">Submission rules</span>
                        </span>
                    </a>
                </div>

                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title"><i class="fas fa-lightbulb"></i> Tips</div>
                    Set clear deadlines and point values. Detailed requirements help students understand what's expected.
                </div>
            </div>

            {{-- Main Panel --}}
            <div class="cb-main">
                <div class="cb-header cb-header--homework">
                    <h4><i class="fas fa-book-open me-2"></i>Create Homework</h4>
                    <p>Set up homework assignments with deadlines and requirements</p>
                </div>

                <div class="cb-body">
                    <div class="cb-context-badge">
                        <i class="fas fa-file-alt"></i>
                        <span>Information Sheet: <strong>{{ $informationSheet->title }}</strong></span>
                    </div>

                    {{-- Basic Info --}}
                    <div class="cb-section" id="section-basic">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Basic Information</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="cb-field-label">Homework Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('homework_number') is-invalid @enderror" name="homework_number" value="{{ old('homework_number') }}" placeholder="e.g., HW-1.1" required>
                                    @error('homework_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="cb-field-label">Instructions <span class="required">*</span></label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror" name="instructions" rows="4" required>{{ old('instructions') }}</textarea>
                                @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Settings --}}
                    <div class="cb-section" id="section-settings">
                        <div class="cb-section__title"><i class="fas fa-cog"></i> Settings</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Due Date <span class="required">*</span></label>
                                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" name="due_date" value="{{ old('due_date') }}" required>
                                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Maximum Points <span class="required">*</span></label>
                                    <input type="number" class="form-control @error('max_points') is-invalid @enderror" name="max_points" value="{{ old('max_points', 100) }}" min="1" required>
                                    @error('max_points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Requirements --}}
                    <div class="cb-section" id="section-requirements">
                        <div class="cb-section__title"><i class="fas fa-list-check"></i> Requirements</div>
                        <div id="requirements-container">
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="requirements[]" placeholder="Enter requirement" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('requirements-container', 'requirements[]', 'Enter requirement', true)">
                            <i class="fas fa-plus"></i> Add Requirement
                        </button>
                    </div>

                    {{-- Guidelines --}}
                    <div class="cb-section" id="section-guidelines">
                        <div class="cb-section__title"><i class="fas fa-file-lines"></i> Submission Guidelines</div>
                        <div id="guidelines-container">
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="submission_guidelines[]" placeholder="Enter submission guideline" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('guidelines-container', 'submission_guidelines[]', 'Enter submission guideline', true)">
                            <i class="fas fa-plus"></i> Add Guideline
                        </button>
                    </div>

                    {{-- Reference Images --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-images"></i> Reference Images</div>
                        <label class="cb-upload-area">
                            <input type="file" class="d-none" name="reference_images[]" accept="image/*" multiple onchange="this.closest('.cb-upload-area').classList.add('has-file')">
                            <i class="fas fa-cloud-upload-alt d-block"></i>
                            <div class="cb-upload-area__text">
                                <strong>Click to upload</strong> images<br>
                                <small>You can upload multiple images</small>
                            </div>
                        </label>
                        @error('reference_images')<div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <span class="cb-footer__hint d-none d-md-inline">All fields marked * are required</span>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Homework
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
