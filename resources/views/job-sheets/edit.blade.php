@extends('layouts.app')

@section('title', 'Edit Job Sheet')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item active">Edit Job Sheet</li>
        </ol>
    </nav>

    <form action="{{ route('job-sheets.update', [$informationSheet, $jobSheet]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="cb-container">
            {{-- Sidebar --}}
            <div class="cb-sidebar">
                <div class="cb-sidebar__title">Job Sheet Guide</div>
                <div class="cb-sidebar__group">
                    <div class="cb-sidebar__group-label"><i class="fas fa-info-circle"></i> Sections</div>
                    <a href="#section-basic" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--green"><i class="fas fa-pen"></i></span>
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Basic Info</span></span>
                    </a>
                    <a href="#section-objectives" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--blue"><i class="fas fa-bullseye"></i></span>
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Objectives</span></span>
                    </a>
                    <a href="#section-tools" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--orange"><i class="fas fa-wrench"></i></span>
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Tools</span></span>
                    </a>
                    <a href="#section-safety" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--red"><i class="fas fa-shield-alt"></i></span>
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Safety</span></span>
                    </a>
                </div>
                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title"><i class="fas fa-lightbulb"></i> Tips</div>
                    Each step should have clear instructions and an expected outcome.
                </div>
            </div>

            {{-- Main Panel --}}
            <div class="cb-main">
                <div class="cb-header cb-header--job">
                    <h4><i class="fas fa-edit me-2"></i>Edit Job Sheet</h4>
                    <p>Update job procedures, tools, and safety requirements</p>
                </div>

                <div class="cb-body">
                    {{-- Basic Info --}}
                    <div class="cb-section" id="section-basic">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Basic Information</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="cb-field-label">Job Number <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="job_number" value="{{ old('job_number', $jobSheet->job_number) }}" required>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $jobSheet->title) }}" required>
                                </div>
                            </div>
                            <div>
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $jobSheet->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Objectives --}}
                    <div class="cb-section" id="section-objectives">
                        <div class="cb-section__title"><i class="fas fa-bullseye"></i> Objectives</div>
                        <div id="objectives-container">
                            @foreach($jobSheet->objectives_list as $objective)
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="objectives[]" value="{{ $objective }}" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('objectives-container', 'objectives[]', 'Enter objective', true)">
                            <i class="fas fa-plus"></i> Add Objective
                        </button>
                    </div>

                    {{-- Tools --}}
                    <div class="cb-section" id="section-tools">
                        <div class="cb-section__title"><i class="fas fa-wrench"></i> Tools Required</div>
                        <div id="tools-container">
                            @foreach($jobSheet->tools_required_list as $tool)
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="tools_required[]" value="{{ $tool }}" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('tools-container', 'tools_required[]', 'Enter tool', true)">
                            <i class="fas fa-plus"></i> Add Tool
                        </button>
                    </div>

                    {{-- Safety --}}
                    <div class="cb-section" id="section-safety">
                        <div class="cb-section__title"><i class="fas fa-shield-alt"></i> Safety Requirements</div>
                        <div id="safety-container">
                            @foreach($jobSheet->safety_requirements_list as $safety)
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="safety_requirements[]" value="{{ $safety }}" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('safety-container', 'safety_requirements[]', 'Enter safety requirement', true)">
                            <i class="fas fa-plus"></i> Add Safety Requirement
                        </button>
                    </div>

                    {{-- References --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-book"></i> Reference Materials</div>
                        <div id="references-container">
                            @forelse($jobSheet->reference_materials_list as $ref)
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="reference_materials[]" value="{{ $ref }}">
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @empty
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="reference_materials[]" placeholder="Enter reference material">
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @endforelse
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('references-container', 'reference_materials[]', 'Enter reference material')">
                            <i class="fas fa-plus"></i> Add Reference
                        </button>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('job-sheets.show', $jobSheet) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Job Sheet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function addItem(containerId, inputName, placeholder) {
    DynamicForm.addListItem(containerId, inputName, placeholder);
}
function removeItem(button) {
    DynamicForm.removeListItem(button);
}
</script>
@endsection
