@extends('layouts.app')

@section('title', 'Create Job Sheet')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item"><a href="{{ route('modules.show', $informationSheet->module_id) }}">{{ $informationSheet->module->module_name }}</a></li>
            <li class="breadcrumb-item active">Create Job Sheet</li>
        </ol>
    </nav>

    <form action="{{ route('job-sheets.store', $informationSheet) }}" method="POST" enctype="multipart/form-data" id="jobSheetForm">
        @csrf

        <div class="cb-container">
            {{-- Sidebar --}}
            <div class="cb-sidebar">
                <div class="cb-sidebar__title">Job Sheet Guide</div>

                <div class="cb-sidebar__group">
                    <div class="cb-sidebar__group-label"><i class="fas fa-info-circle"></i> Sections</div>
                    <a href="#section-basic" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--green"><i class="fas fa-pen"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Basic Info</span>
                            <span class="cb-sidebar__item-desc">Title, number, description</span>
                        </span>
                    </a>
                    <a href="#section-objectives" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--blue"><i class="fas fa-bullseye"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Objectives</span>
                            <span class="cb-sidebar__item-desc">Learning goals</span>
                        </span>
                    </a>
                    <a href="#section-tools" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--orange"><i class="fas fa-wrench"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Tools</span>
                            <span class="cb-sidebar__item-desc">Required tools</span>
                        </span>
                    </a>
                    <a href="#section-safety" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--red"><i class="fas fa-shield-alt"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Safety</span>
                            <span class="cb-sidebar__item-desc">Requirements</span>
                        </span>
                    </a>
                    <a href="#section-steps" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--purple"><i class="fas fa-list-ol"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Job Steps</span>
                            <span class="cb-sidebar__item-desc">Step-by-step procedure</span>
                        </span>
                    </a>
                </div>

                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title"><i class="fas fa-lightbulb"></i> Tips</div>
                    Each step should have clear instructions and an expected outcome so students know what to aim for.
                </div>
            </div>

            {{-- Main Panel --}}
            <div class="cb-main">
                <div class="cb-header cb-header--job">
                    <h4><i class="fas fa-hard-hat me-2"></i>Create Job Sheet</h4>
                    <p>Define step-by-step job procedures with tools and safety requirements</p>
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
                                    <label class="cb-field-label">Job Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('job_number') is-invalid @enderror" name="job_number" value="{{ old('job_number') }}" placeholder="e.g., JS-1.1" required>
                                    @error('job_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div>
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Objectives --}}
                    <div class="cb-section" id="section-objectives">
                        <div class="cb-section__title"><i class="fas fa-bullseye"></i> Objectives</div>
                        <div id="objectives-container">
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="objectives[]" placeholder="Enter objective" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('objectives-container', 'objectives[]', 'Enter objective', true)">
                            <i class="fas fa-plus"></i> Add Objective
                        </button>
                    </div>

                    {{-- Tools --}}
                    <div class="cb-section" id="section-tools">
                        <div class="cb-section__title"><i class="fas fa-wrench"></i> Tools Required</div>
                        <div id="tools-container">
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="tools_required[]" placeholder="Enter tool" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('tools-container', 'tools_required[]', 'Enter tool', true)">
                            <i class="fas fa-plus"></i> Add Tool
                        </button>
                    </div>

                    {{-- Safety --}}
                    <div class="cb-section" id="section-safety">
                        <div class="cb-section__title"><i class="fas fa-shield-alt"></i> Safety Requirements</div>
                        <div id="safety-container">
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="safety_requirements[]" placeholder="Enter safety requirement" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('safety-container', 'safety_requirements[]', 'Enter safety requirement', true)">
                            <i class="fas fa-plus"></i> Add Safety Requirement
                        </button>
                    </div>

                    {{-- References --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-book"></i> Reference Materials</div>
                        <div id="references-container">
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="reference_materials[]" placeholder="Enter reference material">
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('references-container', 'reference_materials[]', 'Enter reference material')">
                            <i class="fas fa-plus"></i> Add Reference
                        </button>
                    </div>

                    {{-- Steps --}}
                    <div class="cb-section" id="section-steps">
                        <div class="cb-items-header">
                            <h5><i class="fas fa-list-ol"></i> Job Steps <span class="cb-count-badge">1</span></h5>
                        </div>

                        <div id="steps-container">
                            <div class="cb-item-card step-card">
                                <div class="cb-item-card__header">
                                    <div class="left-section">
                                        <span class="cb-item-card__number">1</span>
                                        <span class="cb-item-card__title">Step #1</span>
                                    </div>
                                    <div class="right-section">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="DynamicForm.removeItemCard(this, 'step-card', 'Step')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="cb-item-card__body">
                                    <input type="hidden" name="steps[0][step_number]" value="1">
                                    <div class="mb-3">
                                        <label class="cb-field-label">Instruction <span class="required">*</span></label>
                                        <textarea class="form-control" name="steps[0][instruction]" rows="2" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="cb-field-label">Expected Outcome <span class="required">*</span></label>
                                        <textarea class="form-control" name="steps[0][expected_outcome]" rows="2" required></textarea>
                                    </div>
                                    <div>
                                        <label class="cb-field-label">Step Image <span class="optional">(optional)</span></label>
                                        <input type="file" class="form-control" name="steps[0][image]" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="cb-add-btn" onclick="addStep()">
                            <i class="fas fa-plus"></i> Add Step
                        </button>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <span class="cb-footer__hint d-none d-md-inline">All fields marked * are required</span>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Job Sheet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let stepCount = 1;

function addStep() {
    DynamicForm.addItemCard('steps-container', 'step-card', (count) => `
        <input type="hidden" name="steps[${count}][step_number]" value="${count + 1}">
        <div class="mb-3">
            <label class="cb-field-label">Instruction <span class="required">*</span></label>
            <textarea class="form-control" name="steps[${count}][instruction]" rows="2" required></textarea>
        </div>
        <div class="mb-3">
            <label class="cb-field-label">Expected Outcome <span class="required">*</span></label>
            <textarea class="form-control" name="steps[${count}][expected_outcome]" rows="2" required></textarea>
        </div>
        <div>
            <label class="cb-field-label">Step Image <span class="optional">(optional)</span></label>
            <input type="file" class="form-control" name="steps[${count}][image]" accept="image/*">
        </div>
    `, 'Step');
    stepCount++;
}
</script>
@endsection
