@extends('layouts.app')

@section('title', 'Edit Homework')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item active">Edit Homework</li>
        </ol>
    </nav>

    <form action="{{ route('homeworks.update', [$informationSheet, $homework]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="cb-container">
            {{-- Sidebar --}}
            <div class="cb-sidebar">
                <div class="cb-sidebar__title">Homework Guide</div>
                <div class="cb-sidebar__group">
                    <div class="cb-sidebar__group-label"><i class="fas fa-info-circle"></i> Sections</div>
                    <a href="#section-basic" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--blue"><i class="fas fa-pen"></i></span>
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Basic Info</span></span>
                    </a>
                    <a href="#section-settings" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--orange"><i class="fas fa-cog"></i></span>
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Settings</span></span>
                    </a>
                    <a href="#section-requirements" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--green"><i class="fas fa-list-check"></i></span>
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Requirements</span></span>
                    </a>
                    <a href="#section-guidelines" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--purple"><i class="fas fa-file-lines"></i></span>
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Guidelines</span></span>
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
                    <h4><i class="fas fa-edit me-2"></i>Edit Homework</h4>
                    <p>Update homework details, deadlines, and requirements</p>
                </div>

                <div class="cb-body">
                    {{-- Basic Info --}}
                    <div class="cb-section" id="section-basic">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Basic Information</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="cb-field-label">Homework Number <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="homework_number" value="{{ old('homework_number', $homework->homework_number) }}" required>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $homework->title) }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $homework->description) }}</textarea>
                            </div>
                            <div>
                                <label class="cb-field-label">Instructions <span class="required">*</span></label>
                                <textarea class="form-control" name="instructions" rows="4" required>{{ old('instructions', $homework->instructions) }}</textarea>
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
                                    <input type="datetime-local" class="form-control" name="due_date" value="{{ old('due_date', $homework->due_date->format('Y-m-d\TH:i')) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Maximum Points <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="max_points" value="{{ old('max_points', $homework->max_points) }}" min="1" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Requirements --}}
                    <div class="cb-section" id="section-requirements">
                        <div class="cb-section__title"><i class="fas fa-list-check"></i> Requirements</div>
                        <div id="requirements-container">
                            @foreach($homework->requirements_list as $requirement)
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="requirements[]" value="{{ $requirement }}" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('requirements-container', 'requirements[]', 'Enter requirement', true)">
                            <i class="fas fa-plus"></i> Add Requirement
                        </button>
                    </div>

                    {{-- Guidelines --}}
                    <div class="cb-section" id="section-guidelines">
                        <div class="cb-section__title"><i class="fas fa-file-lines"></i> Submission Guidelines</div>
                        <div id="guidelines-container">
                            @foreach($homework->submission_guidelines_list as $guideline)
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="submission_guidelines[]" value="{{ $guideline }}" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('guidelines-container', 'submission_guidelines[]', 'Enter submission guideline', true)">
                            <i class="fas fa-plus"></i> Add Guideline
                        </button>
                    </div>

                    {{-- Reference Images --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-images"></i> Reference Images</div>
                        @if(count($homework->reference_images_list) > 0)
                        <div class="mb-3">
                            <div class="cb-field-hint mb-2">Current images:</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($homework->reference_images_list as $image)
                                <img src="{{ Storage::url($image) }}" alt="Reference" class="img-thumbnail" style="max-height: 100px;">
                                @endforeach
                            </div>
                        </div>
                        @endif
                        <label class="cb-upload-area">
                            <input type="file" class="d-none" name="reference_images[]" accept="image/*" multiple onchange="this.closest('.cb-upload-area').classList.add('has-file')">
                            <i class="fas fa-cloud-upload-alt d-block"></i>
                            <div class="cb-upload-area__text">
                                <strong>Click to upload</strong> new images<br>
                                <small>Upload new images to replace existing ones</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('homeworks.show', $homework) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Homework
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
